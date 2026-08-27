<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Vat;
use App\Models\VatSetting;
use App\Models\Wastage;
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

        // Fetch VAT configuration safely
        $rawVat = null;
        if (class_exists(Vat::class)) {
            $rawVat = Vat::first();
        } 
        if (!$rawVat && class_exists(VatSetting::class)) {
            $rawVat = VatSetting::first();
        }

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

                // 1. Generate Order Number that RESETS EVERY MONTH (e.g., ORD-202608-0001)
                $saleDate = now();
                $monthlyCount = Sale::whereYear('sale_date', $saleDate->year)
                                    ->whereMonth('sale_date', $saleDate->month)
                                    ->count() + 1;
                $orderNumber = 'ORD-' . $saleDate->format('Ym') . '-' . str_pad($monthlyCount, 4, '0', STR_PAD_LEFT);

                // Create Sale Record
                $sale = Sale::create([
                    'order_number'    => $orderNumber,
                    'sale_date'       => $saleDate,
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

                    if ($product->available_stock < $item['quantity']) {
                        throw new \Exception("Insufficient ingredient stock for: {$product->product_name}. Max available portions: {$product->available_stock}");
                    }

                    // Create Sale Detail
                    SaleDetail::create([
                        'sale_id'    => $sale->sale_id ?? $sale->id,
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
                        $ingredient->decrement('quantity', $deductAmount);
                    }
                }

                return response()->json([
                    'message'      => 'Transaction successful!',
                    'sale_id'      => $sale->sale_id ?? $sale->id,
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
        $activeTab = $request->input('tab', 'summary');

        $currentYear  = (int) Carbon::now()->year;
        $currentMonth = (int) Carbon::now()->month;

        // Read GET inputs
        $selectedYear  = $request->filled('year')  ? (int) $request->input('year')  : $currentYear;
        $selectedMonth = $request->filled('month') ? (int) $request->input('month') : $currentMonth;

        // Redirect future date requests to the current month/year to fix the URL
        if ($selectedYear > $currentYear || ($selectedYear === $currentYear && $selectedMonth > $currentMonth)) {
            return redirect()->route('reports.index', [
                'tab'   => $activeTab,
                'month' => $currentMonth,
                'year'  => $currentYear,
            ]);
        }

        // Define exact start and end boundaries for the selected month
        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth()->toDateTimeString();
        $endDate   = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->toDateTimeString();

        // 1. Sales Summary Metrics
        $salesQuery = Sale::whereBetween('sale_date', [$startDate, $endDate]);

        $monthlySalesList = (clone $salesQuery)->with('details.product')
                                               ->orderBy('sale_date', 'desc')
                                               ->get();

        $totalSales    = (clone $salesQuery)->sum('total_amount');
        $totalSubtotal = (clone $salesQuery)->sum('subtotal');
        $totalVat      = (clone $salesQuery)->sum('vat_amount');
        $totalDiscount = (clone $salesQuery)->sum('discount_amount');
        $totalOrders   = (clone $salesQuery)->count();

        // 2. Best Selling Products
        $bestSellers = SaleDetail::select(
                'products.product_name', 
                DB::raw('SUM(sale_details.quantity) as total_qty'), 
                DB::raw('SUM(sale_details.subtotal) as total_revenue')
            )
            ->join('products', 'sale_details.product_id', '=', 'products.product_id')
            ->whereHas('sale', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('sale_date', [$startDate, $endDate]);
            })
            ->groupBy('products.product_id', 'products.product_name')
            ->orderByDesc('total_qty')
            ->get();

        // 3. Payment Method Breakdown
        $paymentMethods = (clone $salesQuery)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // 4. Wastage Costs
        $totalWastageCost = 0;
        if (class_exists(Wastage::class)) {
            $wastageQuery = Wastage::whereBetween('created_at', [$startDate, $endDate]);

            if (\Illuminate\Support\Facades\Schema::hasColumn('wastages', 'total_cost')) {
                $totalWastageCost = $wastageQuery->sum('total_cost');
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('wastages', 'cost')) {
                $totalWastageCost = $wastageQuery->sum('cost');
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('wastages', 'amount')) {
                $totalWastageCost = $wastageQuery->sum('amount');
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('wastages', 'total_amount')) {
                $totalWastageCost = $wastageQuery->sum('total_amount');
            }
        }

        $reportDateTitle = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y');

        return view('sales_reports', compact(
            'monthlySalesList',
            'totalSales',
            'totalSubtotal',
            'totalVat',
            'totalDiscount',
            'totalOrders',
            'bestSellers',
            'paymentMethods',
            'totalWastageCost',
            'selectedMonth',
            'selectedYear',
            'reportDateTitle',
            'activeTab'
        ));
    }
}