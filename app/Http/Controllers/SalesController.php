<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Vat;
use App\Models\VatSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    public function index()
    {
        // Eager load ingredients for dynamic portion calculations
        $products = Product::with('ingredients')
                           ->where('status', 'Available')
                           ->get();

        // 1. Fetch VAT configuration safely
        $rawVat = null;
        if (class_exists(Vat::class)) {
            $rawVat = Vat::first();
        } 
        if (!$rawVat && class_exists(VatSetting::class)) {
            $rawVat = VatSetting::first();
        }

        // 2. Normalize VAT object properties
        $vat = (object) [
            'rate'         => (float) ($rawVat->rate ?? $rawVat->vat_rate ?? 12.00),
            'is_inclusive' => (bool) ($rawVat->is_inclusive ?? $rawVat->vat_inclusive ?? true),
            'is_enabled'   => (bool) ($rawVat->is_enabled ?? $rawVat->is_active ?? true),
            'is_active'    => (bool) ($rawVat->is_enabled ?? $rawVat->is_active ?? true),
        ];

        $discounts = class_exists(Discount::class) 
            ? Discount::where('is_active', true)->get() 
            : collect([]);

        $viewName = view()->exists('pos') ? 'pos' : 'pointofsale';

        return view($viewName, compact('products', 'vat', 'discounts'));
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
                $subtotal = $request->subtotal ?? $request->total_amount;
                
                $vatAmount = $request->vat_amount;
                if ($vatAmount === null || $vatAmount == 0) {
                    $vatAmount = $subtotal - ($subtotal / 1.12);
                }

                $today = Carbon::today();
                $dailyCount = Sale::whereDate('sale_date', $today)->count() + 1;
                $orderNumber = 'ORD-' . $today->format('Ymd') . '-' . str_pad($dailyCount, 4, '0', STR_PAD_LEFT);

                // Create Sale Record
                $sale = Sale::create([
                    'order_number'    => $orderNumber,
                    'sale_date'       => now(),
                    'subtotal'        => $subtotal,
                    'vat_amount'      => round($vatAmount, 2),
                    'discount_type'   => $request->discount_type,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'total_amount'    => $request->total_amount,
                    'order_channel'   => $request->channel ?? 'Walk-in',
                    'payment_method'  => 'Cash',
                ]);

                foreach ($request->items as $item) {
                    $product = Product::with('ingredients')
                                       ->where('product_id', $item['id'])
                                       ->lockForUpdate()
                                       ->firstOrFail();

                    // Check portion availability against raw ingredients
                    if ($product->available_stock < $item['quantity']) {
                        throw new \Exception("Insufficient ingredient stock for: {$product->product_name}. Max available portions: {$product->available_stock}");
                    }

                    // Create Sale Detail
                    SaleDetail::create([
                        'sale_id'    => $sale->sale_id,
                        'product_id' => $product->product_id,
                        'quantity'   => $item['quantity'],
                        'subtotal'   => $product->price * $item['quantity'],
                    ]);

                    // Deduct raw ingredients quantity
foreach ($product->ingredients as $ingredient) {
    $qtyNeeded = $ingredient->pivot->quantity_needed 
              ?? $ingredient->pivot->quantity_required 
              ?? $ingredient->pivot->quantity 
              ?? 1;

    $deductAmount = $qtyNeeded * $item['quantity'];
    $ingredient->decrement('current_stock', $deductAmount);
}
                }

                return response()->json([
                    'message'      => 'Transaction successful!',
                    'sale_id'      => $sale->sale_id,
                    'order_number' => $sale->order_number,
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

    public function reports(Request $request)
    {
        $selectedMonth = (int) $request->input('month', Carbon::now()->month);
        $selectedYear = (int) $request->input('year', Carbon::now()->year);

        $monthlySalesList = Sale::with('details.product')
                                ->whereMonth('sale_date', $selectedMonth)
                                ->whereYear('sale_date', $selectedYear)
                                ->orderBy('sale_date', 'desc')
                                ->get();

        $totalMonthlyAmount = $monthlySalesList->sum('total_amount');
        $totalTransactions = $monthlySalesList->count();

        $reportDateTitle = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y');

        return view('sales_reports', compact(
            'monthlySalesList', 
            'totalMonthlyAmount', 
            'totalTransactions', 
            'selectedMonth', 
            'selectedYear', 
            'reportDateTitle'
        ));
    }
}