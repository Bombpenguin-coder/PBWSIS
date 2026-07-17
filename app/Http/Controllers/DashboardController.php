<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the main system dashboard.
     */
    public function index()
    {
        // Later, we will query the Sales and Product models here to pass real data to the view.
        // For now, we simply return the connected view.
        return view('dashboard');
    }
}