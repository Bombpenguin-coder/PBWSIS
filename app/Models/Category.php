<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'is_discountable',
        'has_size_small',
        'has_size_medium',
        'has_size_large',
        'has_sugar_level',
    ];
}