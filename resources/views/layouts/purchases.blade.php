@extends('layouts.app')

@section('title', 'Purchases')
@section('header_title', 'Purchase Management')

@section('content')
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Purchase Orders</h2>
                <p class="text-xs text-gray-500">Manage stock purchases and supplier orders</p>
            </div>
            <button class="bg-red-900 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                + New Purchase Order
            </button>
        </div>

        <div class="border border-dashed border-gray-200 rounded-lg p-12 text-center text-gray-400 text-xs">
            Purchase module interface coming soon.
        </div>
    </div>
@endsection