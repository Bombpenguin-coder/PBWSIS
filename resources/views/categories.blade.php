@extends('layouts.app')

@section('title', 'Category Management')
@section('header_title', 'Product Categories')

@section('content')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Add Category Form -->
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-red-900">
            <h2 class="text-lg font-bold mb-4 text-black">Add New Category</h2>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="category_name">Category Name</label>
                    <input type="text" name="category_name" id="category_name" placeholder="e.g., Meals, Beverages" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2" for="description">Description (Optional)</label>
                    <textarea name="description" id="description" rows="3" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900"></textarea>
                </div>

                <button type="submit" class="w-full bg-red-900 hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition duration-200">
                    Save Category
                </button>
            </form>
        </div>

        <!-- Category List Table -->
        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-bold mb-4 text-black">Current Categories</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-black text-white">
                        <tr>
                            <th class="py-2 px-4 border-b text-left">ID</th>
                            <th class="py-2 px-4 border-b text-left">Name</th>
                            <th class="py-2 px-4 border-b text-left">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-2 px-4 border-b">{{ $category->category_id }}</td>
                                <td class="py-2 px-4 border-b font-bold text-gray-800">{{ $category->category_name }}</td>
                                <td class="py-2 px-4 border-b text-gray-600">{{ $category->description ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 px-4 text-center text-gray-500">No categories found. Create one to organize your products!</td>
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
@endsection