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
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'description' => 'nullable|string|max:255',
        ]);

        Category::create([
            'category_name' => $request->category_name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Category added successfully!');
    }
}