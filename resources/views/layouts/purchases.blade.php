@extends('layouts.app') 

@section('content')
<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-white">Purchases / Restock Inventory</h1>
        <p class="text-zinc-400 text-sm">Log stock receipts and track inventory purchase history.</p>
    </div>

    <div class="bg-[#1a1a1e] border border-zinc-800 rounded-2xl p-6 shadow-xl border-t-4 border-t-[#EA580C]">
        <h2 class="text-lg font-bold text-white mb-4">Record New Purchase</h2>
        
        <form action="{{ route('purchases.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Supplier Select -->
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Supplier</label>
                    <select name="supplier_id" class="w-full px-3 py-2.5 bg-[#0f0f10] border border-zinc-800 rounded-lg text-white focus:outline-none focus:border-[#EA580C]" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name ?? $supplier->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Typeable / Searchable Ingredient Field -->
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Ingredient</label>
                    <input 
                        list="ingredients_list" 
                        name="ingredient_name" 
                        placeholder="Type or select ingredient..." 
                        autocomplete="off" 
                        required
                        class="w-full px-3 py-2.5 bg-[#0f0f10] border border-zinc-800 rounded-lg text-white focus:outline-none focus:border-[#EA580C]"
                    >
                    <datalist id="ingredients_list">
                        @foreach($ingredients as $ingredient)
                            <option value="{{ $ingredient->ingredient_name }}">Stock: {{ $ingredient->quantity }}</option>
                        @endforeach
                    </datalist>
                </div>

                <!-- Box Qty -->
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Box Qty</label>
                    <input type="number" name="box_qty" min="1" required placeholder="0"
                        class="w-full px-3 py-2.5 bg-[#0f0f10] border border-zinc-800 rounded-lg text-white focus:outline-none focus:border-[#EA580C]">
                </div>

                <!-- Unit Cost -->
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Unit Cost (₱)</label>
                    <input type="number" step="0.01" name="unit_cost" min="0" required placeholder="0.00"
                        class="w-full px-3 py-2.5 bg-[#0f0f10] border border-zinc-800 rounded-lg text-white focus:outline-none focus:border-[#EA580C]">
                </div>

                <!-- Purchase Date -->
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Purchase Date</label>
                    <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required
                        class="w-full px-3 py-2.5 bg-[#0f0f10] border border-zinc-800 rounded-lg text-white focus:outline-none focus:border-[#EA580C]">
                </div>
            </div>

            <button type="submit" 
                class="mt-4 px-5 py-2.5 bg-[#EA580C] hover:bg-[#C2410C] text-white font-bold text-sm rounded-lg shadow-lg transition duration-200">
                Log Purchase & Update Stock
            </button>
        </form>
    </div>

    <!-- Purchase History Logs Table -->
    <div class="bg-[#1a1a1e] border border-zinc-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-4 border-b border-zinc-800">
            <h2 class="text-base font-bold text-white">Purchase History Logs</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-300">
                <thead class="bg-[#0f0f10] text-xs uppercase text-zinc-400 font-bold border-b border-zinc-800">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Supplier</th>
                        <th class="px-4 py-3">Ingredient</th>
                        <th class="px-4 py-3">Box Qty</th>
                        <th class="px-4 py-3">Unit Cost</th>
                        <th class="px-4 py-3">Total Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($purchases as $purchase)
                    <tr class="hover:bg-zinc-800/30 transition">
                        <td class="px-4 py-3">{{ $purchase->purchase_date }}</td>
                        <td class="px-4 py-3">{{ $purchase->supplier->name ?? $purchase->supplier->supplier_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $purchase->ingredient->ingredient_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 bg-emerald-950 text-emerald-400 border border-emerald-800 text-xs rounded-md font-bold">
                                +{{ $purchase->quantity_received ?? $purchase->quantity }}
                            </span>
                        </td>
                        <td class="px-4 py-3">₱{{ number_format($purchase->unit_cost, 2) }}</td>
                        <td class="px-4 py-3 font-bold text-white">₱{{ number_format($purchase->total_cost, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-zinc-500">No purchases recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection