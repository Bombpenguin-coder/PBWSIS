@extends('layouts.app')

@section('title', 'Category Management')
@section('header_title', 'Product Categories')

@section('content')
<div class="min-h-screen bg-[#18191c] text-zinc-100 p-2 md:p-4">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-900/40 border-l-4 border-emerald-500 text-emerald-200 font-medium rounded-r-lg shadow-md text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-rose-900/40 border-l-4 border-rose-500 text-rose-200 font-medium rounded-r-lg shadow-md text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-rose-900/40 border-l-4 border-rose-500 text-rose-200 font-medium rounded-r-lg shadow-md text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Add Category Form -->
        <div class="bg-[#202226] border border-zinc-800 p-6 rounded-xl shadow-xl h-fit">
            <h2 class="text-lg font-bold mb-4 text-white border-b border-zinc-800 pb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#f97316]"></span>
                Add New Category
            </h2>
            <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Category Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 text-zinc-400" for="category_name">Category Name *</label>
                    <input type="text" name="category_name" id="category_name" placeholder="e.g., Meals, Milk Tea, Coffee" required 
                           class="w-full bg-[#18191c] border border-zinc-700 text-white p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f97316] placeholder-zinc-500 text-sm">
                </div>

                <!-- Discount Option -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 text-zinc-400" for="is_discountable">Discount Available</label>
                    <select name="is_discountable" id="is_discountable" 
                            class="w-full bg-[#18191c] border border-zinc-700 text-white p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f97316] text-sm cursor-pointer">
                        <option value="1">Applicable (Discounts Allowed)</option>
                        <option value="0">Not Applicable (No Discounts)</option>
                    </select>
                </div>

                <!-- Cup Sizes -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 text-zinc-400">Available Cup Sizes</label>
                    <div class="grid grid-cols-3 gap-2 bg-[#18191c] p-3 rounded-lg border border-zinc-800">
                        <label class="inline-flex items-center text-xs font-semibold text-zinc-300 cursor-pointer">
                            <input type="checkbox" name="has_size_small" value="1" class="rounded bg-zinc-800 border-zinc-700 text-[#f97316] focus:ring-[#f97316]">
                            <span class="ml-2">Small</span>
                        </label>
                        <label class="inline-flex items-center text-xs font-semibold text-zinc-300 cursor-pointer">
                            <input type="checkbox" name="has_size_medium" value="1" class="rounded bg-zinc-800 border-zinc-700 text-[#f97316] focus:ring-[#f97316]">
                            <span class="ml-2">Medium</span>
                        </label>
                        <label class="inline-flex items-center text-xs font-semibold text-zinc-300 cursor-pointer">
                            <input type="checkbox" name="has_size_large" value="1" class="rounded bg-zinc-800 border-zinc-700 text-[#f97316] focus:ring-[#f97316]">
                            <span class="ml-2">Large</span>
                        </label>
                    </div>
                </div>

                <!-- Sugar Level Toggle -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 text-zinc-400">Sugar Level Selection</label>
                    <div class="flex items-center space-x-6 bg-[#18191c] p-3 rounded-lg border border-zinc-800">
                        <label class="inline-flex items-center text-xs font-semibold text-zinc-300 cursor-pointer">
                            <input type="radio" name="has_sugar_level" value="1" class="bg-zinc-800 border-zinc-700 text-[#f97316] focus:ring-[#f97316]">
                            <span class="ml-2">Enabled</span>
                        </label>
                        <label class="inline-flex items-center text-xs font-semibold text-zinc-300 cursor-pointer">
                            <input type="radio" name="has_sugar_level" value="0" checked class="bg-zinc-800 border-zinc-700 text-[#f97316] focus:ring-[#f97316]">
                            <span class="ml-2">Disabled</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#f97316] hover:bg-[#ea580c] text-white font-extrabold py-2.5 px-4 rounded-xl transition duration-200 text-sm shadow-md mt-2">
                    Save Category
                </button>
            </form>
        </div>

        <!-- Category List Table -->
        <div class="md:col-span-2 bg-[#202226] border border-zinc-800 p-6 rounded-xl shadow-xl">
            <h2 class="text-lg font-bold mb-4 text-white border-b border-zinc-800 pb-2">Current Categories</h2>
            <div class="overflow-x-auto rounded-lg border border-zinc-800">
                <table class="min-w-full bg-[#18191c] text-zinc-300">
                    <thead class="bg-[#111214] text-zinc-400 text-xs uppercase tracking-wider border-b border-zinc-800">
                        <tr>
                            <th class="py-3 px-4 text-left font-bold">Category Name</th>
                            <th class="py-3 px-4 text-left font-bold">Discount</th>
                            <th class="py-3 px-4 text-left font-bold">Cup Sizes</th>
                            <th class="py-3 px-4 text-left font-bold">Sugar Level</th>
                            <th class="py-3 px-4 text-center font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-zinc-800">
                        @forelse($categories as $category)
                            <tr class="hover:bg-[#202226] transition duration-150">
                                <td class="py-3 px-4 font-bold text-white">
                                    {{ $category->category_name }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($category->is_discountable)
                                        <span class="bg-emerald-950/80 text-emerald-400 border border-emerald-800/50 font-bold px-2 py-0.5 rounded-full">Available</span>
                                    @else
                                        <span class="bg-zinc-800 text-zinc-400 px-2 py-0.5 rounded-full">None</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-1 flex-wrap">
                                        @if($category->has_size_small) <span class="bg-zinc-800 text-zinc-200 font-mono font-bold px-1.5 py-0.5 rounded border border-zinc-700">S</span> @endif
                                        @if($category->has_size_medium) <span class="bg-zinc-800 text-zinc-200 font-mono font-bold px-1.5 py-0.5 rounded border border-zinc-700">M</span> @endif
                                        @if($category->has_size_large) <span class="bg-zinc-800 text-zinc-200 font-mono font-bold px-1.5 py-0.5 rounded border border-zinc-700">L</span> @endif
                                        @if(!$category->has_size_small && !$category->has_size_medium && !$category->has_size_large)
                                            <span class="text-zinc-600">N/A</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($category->has_sugar_level)
                                        <span class="text-emerald-400 font-bold">Enabled</span>
                                    @else
                                        <span class="text-zinc-500">Disabled</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center space-x-2">
                                    <button type="button" 
                                            onclick="openEditModal(
                                                {{ $category->category_id ?? $category->id }}, 
                                                '{{ addslashes($category->category_name) }}', 
                                                {{ $category->is_discountable ? 1 : 0 }}, 
                                                {{ $category->has_size_small ? 1 : 0 }}, 
                                                {{ $category->has_size_medium ? 1 : 0 }}, 
                                                {{ $category->has_size_large ? 1 : 0 }}, 
                                                {{ $category->has_sugar_level ? 1 : 0 }}
                                            )"
                                            class="bg-blue-600/80 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded text-xs transition duration-150">
                                        Edit
                                    </button>

                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete category {{ $category->category_name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-700/80 hover:bg-rose-700 text-white font-bold py-1 px-3 rounded text-xs transition duration-150">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 px-4 text-center text-zinc-500">No categories found. Create one to organize your products!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="hidden fixed inset-0 bg-black/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-[#202226] border border-zinc-700 rounded-xl shadow-2xl max-w-md w-full p-6 text-zinc-100">
            <h2 class="text-lg font-bold mb-4 text-white border-b border-zinc-800 pb-2">Edit Category</h2>
            
            <form id="editCategoryForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 text-zinc-400" for="edit_category_name">Category Name *</label>
                    <input type="text" id="edit_category_name" name="category_name" required 
                           class="w-full bg-[#18191c] border border-zinc-700 text-white p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f97316] text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 text-zinc-400" for="edit_is_discountable">Discount Available</label>
                    <select id="edit_is_discountable" name="is_discountable" 
                            class="w-full bg-[#18191c] border border-zinc-700 text-white p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f97316] text-sm cursor-pointer">
                        <option value="1">Applicable (Discounts Allowed)</option>
                        <option value="0">Not Applicable (No Discounts)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 text-zinc-400">Available Cup Sizes</label>
                    <div class="grid grid-cols-3 gap-2 bg-[#18191c] p-3 rounded-lg border border-zinc-800">
                        <label class="inline-flex items-center text-xs font-semibold text-zinc-300 cursor-pointer">
                            <input type="checkbox" id="edit_has_size_small" name="has_size_small" value="1" class="rounded bg-zinc-800 border-zinc-700 text-[#f97316] focus:ring-[#f97316]">
                            <span class="ml-2">Small</span>
                        </label>
                        <label class="inline-flex items-center text-xs font-semibold text-zinc-300 cursor-pointer">
                            <input type="checkbox" id="edit_has_size_medium" name="has_size_medium" value="1" class="rounded bg-zinc-800 border-zinc-700 text-[#f97316] focus:ring-[#f97316]">
                            <span class="ml-2">Medium</span>
                        </label>
                        <label class="inline-flex items-center text-xs font-semibold text-zinc-300 cursor-pointer">
                            <input type="checkbox" id="edit_has_size_large" name="has_size_large" value="1" class="rounded bg-zinc-800 border-zinc-700 text-[#f97316] focus:ring-[#f97316]">
                            <span class="ml-2">Large</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 text-zinc-400">Sugar Level Selection</label>
                    <div class="flex items-center space-x-6 bg-[#18191c] p-3 rounded-lg border border-zinc-800">
                        <label class="inline-flex items-center text-xs font-semibold text-zinc-300 cursor-pointer">
                            <input type="radio" id="edit_sugar_yes" name="has_sugar_level" value="1" class="bg-zinc-800 border-zinc-700 text-[#f97316] focus:ring-[#f97316]">
                            <span class="ml-2">Enabled</span>
                        </label>
                        <label class="inline-flex items-center text-xs font-semibold text-zinc-300 cursor-pointer">
                            <input type="radio" id="edit_sugar_no" name="has_sugar_level" value="0" class="bg-zinc-800 border-zinc-700 text-[#f97316] focus:ring-[#f97316]">
                            <span class="ml-2">Disabled</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeEditModal()" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold py-2 px-4 rounded-lg text-xs transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-[#f97316] hover:bg-[#ea580c] text-white font-bold py-2 px-4 rounded-lg text-xs transition shadow">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, name, discountable, small, medium, large, sugar) {
        const form = document.getElementById('editCategoryForm');
        form.action = `/categories/${id}`;

        document.getElementById('edit_category_name').value = name;
        document.getElementById('edit_is_discountable').value = discountable;
        
        document.getElementById('edit_has_size_small').checked = Boolean(small);
        document.getElementById('edit_has_size_medium').checked = Boolean(medium);
        document.getElementById('edit_has_size_large').checked = Boolean(large);

        if (sugar) {
            document.getElementById('edit_sugar_yes').checked = true;
        } else {
            document.getElementById('edit_sugar_no').checked = true;
        }

        document.getElementById('editCategoryModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editCategoryModal').classList.add('hidden');
    }
</script>
@endsection