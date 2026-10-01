<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OperationController extends Controller
{
    public function heldOrders()
    {
        $heldOrders = Order::with('items.product')
            ->where('status', 'held')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('layouts.held_orders', compact('heldOrders')); 
    }

    public function kitchenTickets()
    {
        $tickets = Order::with('items.product')
            ->whereIn('kot_status', ['pending', 'preparing', 'ready'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('layouts.kot', compact('tickets'));
    }

    public function updateKotStatus(Request $request, $id)
    {
        $request->validate(['kot_status' => 'required|in:pending,preparing,ready,served']);
        
        $order = Order::findOrFail($id);
        $order->update(['kot_status' => $request->kot_status]);

        return redirect()->back()->with('success', 'KOT status updated successfully!');
    }

    public function bills()
    {
        $bills = Order::with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('layouts.bills', compact('bills'));
    }

    public function checkoutBill(Request $request, $id)
    {
        $request->validate(['payment_method' => 'required|string']);

        try {
            DB::transaction(function () use ($request, $id) {
                $order = Order::with('items.product.ingredients')->findOrFail($id);

                foreach ($order->items as $item) {
                    if ($item->product && $item->product->ingredients) {
                        foreach ($item->product->ingredients as $ingredient) {
                            $qtyNeeded = $ingredient->pivot->quantity_needed 
                                      ?? $ingredient->pivot->quantity_required 
                                      ?? $ingredient->pivot->quantity 
                                      ?? 1;

                            $deduction = $qtyNeeded * $item->quantity;

                            // Call the unboxing deduction method from your Ingredient model
                            $success = $ingredient->deductPieces($deduction);

                            if (!$success) {
                                throw new \Exception("Insufficient stock for ingredient: {$ingredient->ingredient_name}");
                            }
                        }
                    }
                }

                $order->update([
                    'status'         => 'completed',
                    'payment_status' => 'paid',
                    'payment_method' => $request->payment_method
                ]);
            });

            return redirect()->back()->with('success', 'Bill paid and inventory updated successfully!');

        } catch (\Exception $e) {
            Log::error('Bill Checkout Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }
}