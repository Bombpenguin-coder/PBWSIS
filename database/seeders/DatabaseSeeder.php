<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the Owner/Admin Account
        User::create([
            'username' => 'admin_owner',
            'password' => Hash::make('password123'),
            'role' => 'Owner',
            'contact_number' => '09123456789',
        ]);

        // 2. Create the Cashier Account
        User::create([
            'username' => 'cashier_01',
            'password' => Hash::make('cashier123'),
            'role' => 'Cashier',
            'contact_number' => '09987654321',
        ]);

        // 3. Seed Initial Menu Products
        Product::create([
            'product_name' => 'Classic Buffalo Wings (6 pcs)',
            'price' => 180.00,
            'stock_quantity' => 50,
            'status' => 'Available',
        ]);

        Product::create([
            'product_name' => 'Classic Milk Tea (Large)',
            'price' => 95.00,
            'stock_quantity' => 100,
            'status' => 'Available',
        ]);

        // 4. Seed Raw Ingredients
        Ingredient::create([
            'ingredient_name' => 'Raw Chicken Wings',
            'quantity' => 20.5,
            'unit' => 'kg',
            'max_capacity' => 50.00,
            'reorder_level' => 25.00,
        ]);

        Ingredient::create([
            'ingredient_name' => 'Frying Oil',
            'quantity' => 15.0,
            'unit' => 'liters',
            'max_capacity' => 20.00,
            'reorder_level' => 10.00, 
        ]);
    }
}