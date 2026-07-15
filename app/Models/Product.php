<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Override the default primary key
    protected $primaryKey = 'product_id';

    // Allow these columns to be filled via forms
    protected $fillable = [
        'product_name',
        'price',
        'stock_quantity',
        'status',
    ];
}
