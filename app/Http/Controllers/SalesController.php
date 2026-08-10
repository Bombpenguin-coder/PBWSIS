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
        $products = Product::where('status', 'Available')
                           ->where('stock_quantity', '>', 0)
                           ->get();

        // 1. Fetch VAT configuration safely from either Vat or VatSetting model
        $rawVat = null;
        if (class_exists(Vat::class)) {
            $rawVat = Vat::first();
        } 
        if (!$rawVat && class_exists(VatSetting::class)) {
            $rawVat = VatSetting::first();
        }

        // 2. Normalize VAT object properties for JS consumption
        $vat = (object) [
            'rate'         => (float) ($rawVat->rate ?? $rawVat->vat_rate ?? 12.00),
            'is_inclusive' => (bool) ($rawVat->is_inclusive ?? $rawVat->vat_inclusive ?? true),
            'is_enabled'   => (bool) ($rawVat->is_enabled ?? $rawVat->is_active ?? true),
        ];
        // Normalize VAT object properties for Blade & JS consumption
$vat = (object) [
    'rate'         => (float) ($rawVat->rate ?? $rawVat->vat_rate ?? 12.00),
    'is_inclusive' => (bool) ($rawVat->is_inclusive ?? $rawVat->vat_inclusive ?? true),
    'is_enabled'   => (bool) ($rawVat->is_enabled ?? $rawVat->is_active ?? true),
    'is_active'    => (bool) ($rawVat->is_enabled ?? $rawVat->is_active ?? true),
];

        // Fetch active discounts if model exists
        $discounts = class_exists(Discount::class) 
            ? Discount::where('is_active', true)->get() 
            : collect([]);

        // Adjust view name to 'pos' or 'pointofsale' depending on your blade filename
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
                
                // Automatic backend VAT calculation if front-end passed 0/null
                $vatAmount = $request->vat_amount;
                if ($vatAmount === null || $vatAmount == 0) {
                    // Extract 12% inclusive VAT component as default calculation
                    $vatAmount = $subtotal - ($subtotal / 1.12);
                }

                // 1. Create Sale Record
                $sale = Sale::create([
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
                    // 2. Fetch fresh product data with a pessimistic lock
                    $product = Product::where('product_id', $item['id'])
                                      ->lockForUpdate()
                                      ->firstOrFail();

                    // Check stock sufficiency
                    if ($product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for item: {$product->product_name}. Remaining stock: {$product->stock_quantity}");
                    }

                    // 3. Create Sale Detail
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