<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
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
        return view('pointofsale', compact('products'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $sale = Sale::create([
                'sale_date' => now(),
                'total_amount' => $request->total_amount,
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'order_channel' => $request->channel,
                'payment_method' => 'Pending'
            ]);

            foreach ($request->items as $item) {
                SaleDetail::create([
                    'sale_id' => $sale->sale_id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity']
                ]);

                Product::where('product_id', $item['id'])
                       ->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();
            return response()->json(['message' => 'Transaction successful!', 'sale_id' => $sale->sale_id], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Transaction failed. Please try again.'], 500);
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