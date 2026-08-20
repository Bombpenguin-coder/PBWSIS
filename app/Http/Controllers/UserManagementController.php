<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        // Fetch all staff members from the database
        $users = User::all();
        
        return view('user_management', compact('users'));
    }
}