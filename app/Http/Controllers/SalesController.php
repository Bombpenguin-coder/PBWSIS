<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
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

            // 1. Create the main Sale record
            $sale = Sale::create([
                'sale_date' => now(),
                'total_amount' => $request->total_amount,
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'order_channel' => $request->channel,
                'payment_method' => 'Pending' // Will update later
            ]);

            // 2. Loop through the cart items to save details and deduct stock
            foreach ($request->items as $item) {
                SaleDetail::create([
                    'sale_id' => $sale->sale_id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity']
                ]);

                // Deduct from the finished products inventory
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
}