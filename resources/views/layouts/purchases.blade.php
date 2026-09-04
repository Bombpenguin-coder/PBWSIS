@extends('layouts.app')

@section('title', 'Purchases')
@section('header_title', 'Purchase Management')

@section('content')
    <div class="bg-[#18191c] p-6 rounded-xl shadow-sm border border-zinc-800">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-bold text-white">Purchase Orders</h2>
                <p class="text-xs text-zinc-400">Manage stock purchases and supplier orders</p>
            </div>
            <button class="bg-rose-700 hover:bg-rose-600 text-white text-xs font-semibold px-4 py-2 rounded-lg transition duration-150 shadow-sm">
                + New Purchase Order
            </button>
        </div>

        <div class="border border-dashed border-zinc-800 bg-[#202226]/50 rounded-lg p-12 text-center text-zinc-500 text-xs">
            Purchase module interface coming soon.
        </div>
    </div>
@endsection