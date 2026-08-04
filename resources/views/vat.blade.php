@extends('layouts.app')

@section('title', 'VAT Settings')
@section('header_title', 'VAT Configuration')

@section('content')
<div class="max-w-xl bg-white p-6 rounded-lg shadow-md">
    <form action="{{ route('vat.update', $vat->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">VAT Rate (%)</label>
            <input type="number" step="0.01" name="rate" value="{{ $vat->rate }}" class="w-full border-gray-300 rounded-md shadow-sm p-2 border" required>
        </div>

        <div class="mb-4 flex items-center gap-2">
            <input type="checkbox" name="is_inclusive" id="is_inclusive" {{ $vat->is_inclusive ? 'checked' : '' }}>
            <label for="is_inclusive" class="text-sm text-gray-700">VAT Inclusive in Product Price</label>
        </div>

        <div class="mb-6 flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" {{ $vat->is_active ? 'checked' : '' }}>
            <label for="is_active" class="text-sm text-gray-700">Enable VAT Calculation</label>
        </div>

        <button type="submit" class="bg-red-900 text-white px-4 py-2 rounded-md hover:bg-red-800 transition">Save Changes</button>
    </form>
</div>
@endsection