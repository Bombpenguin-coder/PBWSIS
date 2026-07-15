<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController; // Imports your new Controller
use App\Http\Controllers\ProductController;

// Your existing dashboard route
Route::get('/', [DashboardController::class, 'index']);

// Add this new route for the inventory table
Route::get('/inventory', [ProductController::class, 'index']);