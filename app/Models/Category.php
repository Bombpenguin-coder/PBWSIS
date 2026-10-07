<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'category_id'; // Standard primary key

    protected $fillable = [
        'category_name',
        'is_discountable',
        'has_size_small',
        'has_size_medium',
        'has_size_large',
        'has_sugar_level',
    ];

    /**
     * Get the products associated with this category.
     */
    public function products()
    {
        // Assumes your products table has a 'category_id' column
        return $this->hasMany(Product::class, 'category_id');
    }
}