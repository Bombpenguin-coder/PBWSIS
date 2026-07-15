<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; 

class ProductController extends Controller
{
    public function index()
    {
        // Grab all products from the database
        $products = Product::all();
        
        // Pass them to a new 'inventory' view
        return view('inventory', ['products' => $products]);
    }
}