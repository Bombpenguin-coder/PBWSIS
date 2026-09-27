@extends('layouts.app')

@section('title', 'Category Management')
@section('header_title', 'Product Categories')

@section('content')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-600 text-green-700 font-medium rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-600 text-red-700 font-medium rounded shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-600 text-red-700 font-medium rounded shadow-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Add Category Form -->
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-red-900 h-fit">
            <h2 class="text-lg font-bold mb-4 text-black">Add New Category</h2>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2 text-gray-700" for="category_name">Category Name *</label>
                    <input type="text" name="category_name" id="category_name" placeholder="e.g., Meals, Beverages" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900 text-sm">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2 text-gray-700" for="description">Description (Optional)</label>
                    <textarea name="description" id="description" rows="3" placeholder="Brief description of category" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900 text-sm"></textarea>
                </div>

                <button type="submit" class="w-full bg-red-900 hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition duration-200 text-sm">
                    Save Category
                </button>
            </form>
        </div>

        <!-- Category List Table -->
        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-bold mb-4 text-black">Current Categories</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-black text-white text-sm">
                        <tr>
                            <th class="py-2 px-4 border-b text-left">ID</th>
                            <th class="py-2 px-4 border-b text-left">Name</th>
                            <th class="py-2 px-4 border-b text-left">Description</th>
                            <th class="py-2 px-4 border-b text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-2 px-4 border-b text-gray-600 font-mono">{{ $category->category_id ?? $category->id }}</td>
                                <td class="py-2 px-4 border-b font-bold text-gray-800">{{ $category->category_name }}</td>
                                <td class="py-2 px-4 border-b text-gray-600">{{ $category->description ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border-b text-center space-x-2">
                                    <button type="button" 
                                            onclick="openEditModal({{ $category->category_id ?? $category->id }}, '{{ addslashes($category->category_name) }}', '{{ addslashes($category->description ?? '') }}')"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs transition duration-150">
                                        Edit
                                    </button>

                                    <form action="{{ route('categories.destroy', $category->category_id ?? $category->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete category {{ $category->category_name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-1 px-3 rounded text-xs transition duration-150">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 px-4 text-center text-gray-500">No categories found. Create one to organize your products!</td>
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
    <div id="editCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full border-t-4 border-red-900 p-6">
            <h2 class="text-lg font-bold mb-4 text-black">Edit Category</h2>
            
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2 text-gray-700" for="edit_category_name">Category Name *</label>
                    <input type="text" id="edit_category_name" name="category_name" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900 text-sm">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2 text-gray-700" for="edit_description">Description (Optional)</label>
                    <textarea id="edit_description" name="description" rows="3" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900 text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-sm transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-900 hover:bg-red-800 text-white font-bold py-2 px-4 rounded text-sm transition">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, description) {
            const form = document.getElementById('editCategoryForm');
            form.action = `/categories/${id}`;

            document.getElementById('edit_category_name').value = name;
            document.getElementById('edit_description').value = description;

            document.getElementById('editCategoryModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editCategoryModal').classList.add('hidden');
        }
    </script>
@endsection