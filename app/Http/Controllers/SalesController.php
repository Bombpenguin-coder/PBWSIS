<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\VatSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    public function index()
    {
        $products = Product::where('status', 'Available')
                           ->where('stock_quantity', '>', 0)
                           ->get();

        // Fetch VAT configuration with fallback defaults
        $vat = VatSetting::first() ?? (object) [
            'rate' => 12.00,
            'is_inclusive' => true,
            'is_active' => true,
        ];

        // Fetch active discounts if model exists
        $discounts = class_exists(Discount::class) 
            ? Discount::where('is_active', true)->get() 
            : collect([]);

        return view('pointofsale', compact('products', 'vat', 'discounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'total_amount'     => 'required|numeric|min:0',
            'subtotal'         => 'nullable|numeric|min:0',
            'vat_amount'       => 'nullable|numeric|min:0',
            'discount_amount'  => 'nullable|numeric|min:0',
            'discount_type'    => 'nullable|string',
            'channel'          => 'nullable|string',
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 1. Create Sale Record
                $sale = Sale::create([
                    'sale_date'       => now(),
                    'subtotal'        => $request->subtotal ?? 0,
                    'vat_amount'      => $request->vat_amount ?? 0,
                    'discount_type'   => $request->discount_type,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'total_amount'    => $request->total_amount,
                    'order_channel'   => $request->channel ?? 'Walk-in',
                    'payment_method'  => 'Cash',
                ]);

                foreach ($request->items as $item) {
                    // 2. Fetch fresh product data with a pessimistic lock to prevent stock race conditions
                    $product = Product::where('product_id', $item['id'])
                                      ->lockForUpdate()
                                      ->firstOrFail();

                    // Check stock sufficiency before deducting
                    if ($product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for item: {$product->product_name}. Remaining stock: {$product->stock_quantity}");
                    }

                    // 3. Create Sale Detail using actual stored price
                    SaleDetail::create([
                        'sale_id'    => $sale->sale_id,
                        'product_id' => $product->product_id,
                        'quantity'   => $item['quantity'],
                        'subtotal'   => $product->price * $item['quantity'],
                    ]);

                    // 4. Deduct inventory
                    $product->decrement('stock_quantity', $item['quantity']);
                }

                return response()->json([
                    'message' => 'Transaction successful!',
                    'sale_id' => $sale->sale_id,
                ], 200);
            });

        } catch (\Exception $e) {
            Log::error('Checkout Failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Transaction failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function history()
    {
        $todaySalesList = Sale::with('details.product')
                              ->whereDate('sale_date', Carbon::today())
                              ->orderBy('sale_date', 'desc')
                              ->get();

        return view('sales_history', compact('todaySalesList'));
    }

    public function reports()
    {
        $monthlySalesList = Sale::with('details.product')
                                ->whereMonth('sale_date', Carbon::now()->month)
                                ->whereYear('sale_date', Carbon::now()->year)
                                ->orderBy('sale_date', 'desc')
                                ->get();

        $totalMonthlyAmount = $monthlySalesList->sum('total_amount');
        $totalTransactions = $monthlySalesList->count();

        return view('sales_reports', compact('monthlySalesList', 'totalMonthlyAmount', 'totalTransactions'));
    }
}