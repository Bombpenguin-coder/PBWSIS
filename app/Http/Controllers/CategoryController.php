<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);
        return view('categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name'   => 'required|string|max:255',
            'is_discountable' => 'nullable|boolean',
            'has_size_small'  => 'nullable|boolean',
            'has_size_medium' => 'nullable|boolean',
            'has_size_large'  => 'nullable|boolean',
            'has_sugar_level' => 'nullable|boolean',
        ]);

        Category::create([
            'category_name'   => $request->input('category_name'),
            'is_discountable' => $request->boolean('is_discountable'),
            'has_size_small'  => $request->boolean('has_size_small'),
            'has_size_medium' => $request->boolean('has_size_medium'),
            'has_size_large'  => $request->boolean('has_size_large'),
            'has_sugar_level' => $request->boolean('has_sugar_level'),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name'   => 'required|string|max:255',
            'is_discountable' => 'nullable|boolean',
            'has_size_small'  => 'nullable|boolean',
            'has_size_medium' => 'nullable|boolean',
            'has_size_large'  => 'nullable|boolean',
            'has_sugar_level' => 'nullable|boolean',
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'category_name'   => $request->input('category_name'),
            'is_discountable' => $request->boolean('is_discountable'),
            'has_size_small'  => $request->boolean('has_size_small'),
            'has_size_medium' => $request->boolean('has_size_medium'),
            'has_size_large'  => $request->boolean('has_size_large'),
            'has_sugar_level' => $request->boolean('has_sugar_level'),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }
}