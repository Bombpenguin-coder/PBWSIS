@extends('layouts.app')

@section('title', 'VAT Settings')
@section('header_title', 'VAT Configuration')

@section('content')
    <!-- Success/Error Flash Alerts -->
    @if(session('success'))
        <div class="flex items-center bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 p-4 mb-6 rounded shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-xs font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 p-4 mb-6 rounded shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-xs font-semibold">{{ session('error') }}</span>
        </div>
    @endif

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
                <button type="submit" class="bg-rose-700 hover:bg-rose-600 text-white font-bold py-2 px-5 rounded-lg shadow-sm transition duration-150 text-xs">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection