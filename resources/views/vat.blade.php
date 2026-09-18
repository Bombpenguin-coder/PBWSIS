@extends('layouts.app')

@section('title', 'VAT Settings')
@section('header_title', 'VAT Configuration')

@section('content')
   
    <!-- Dark Card Container -->
    <div class="max-w-xl bg-[#18191c] p-6 rounded-xl shadow-sm border border-zinc-800">
        <div class="border-b border-zinc-800 pb-4 mb-5">
            <h2 class="text-lg font-bold text-white">Tax & VAT Rules</h2>
            <p class="text-xs text-zinc-400">Configure global VAT rate and calculation behavior across sales</p>
        </div>

        <form action="{{ route('vat.update', $vat->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- VAT Rate Field -->
            <div class="mb-5">
                <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="rate">
                    VAT Rate (%)
                </label>
                <div class="relative">
                    <input type="number" step="0.01" name="rate" id="rate" value="{{ old('rate', $vat->rate) }}" required
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500 pr-8">
                    <span class="absolute right-3 top-2.5 text-zinc-500 text-sm font-bold">%</span>
                </div>
                @error('rate')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Toggles & Checkboxes Section -->
            <div class="space-y-3 mb-6 bg-[#202226] p-4 rounded-lg border border-zinc-800">
                <div class="flex items-center gap-2.5">
                    <input type="checkbox" name="is_inclusive" id="is_inclusive" value="1" 
                           {{ old('is_inclusive', $vat->is_inclusive) ? 'checked' : '' }} 
                           class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-zinc-700 bg-[#18191c]">
                    <label for="is_inclusive" class="text-xs font-semibold text-zinc-300 cursor-pointer">
                        VAT Inclusive in Product Price
                    </label>
                </div>

                <div class="flex items-center gap-2.5">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           {{ old('is_active', $vat->is_active) ? 'checked' : '' }} 
                           class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-zinc-700 bg-[#18191c]">
                    <label for="is_active" class="text-xs font-semibold text-zinc-300 cursor-pointer">
                        Enable VAT Calculation System-Wide
                    </label>
                </div>
            </div>
<!-- Action Button -->
<div class="flex justify-end pt-3 border-t border-zinc-800">
    <button type="submit" class="bg-brand-orange hover:bg-brand-orange-hover text-white font-bold py-2 px-5 rounded-lg shadow-sm transition duration-150 text-xs">
        Save Changes
    </button>
</div>
</form>
</div>
@endsection