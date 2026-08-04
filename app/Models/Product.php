<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id'; // Ensure this matches your primary key column

    protected $fillable = ['product_name', 'price', 'stock_quantity', 'status'];
}