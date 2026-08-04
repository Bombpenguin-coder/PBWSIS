<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    // 1. Tell Laravel your custom Primary Key name
    protected $primaryKey = 'ingredient_id';

    // 2. Allow mass assignment for these fields
    protected $fillable = [
        'ingredient_name',
        'quantity',
        'unit',
        'max_capacity',
        'reorder_level',
    ];
}