<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $primaryKey = 'ingredient_id';

    protected $fillable = [
        'ingredient_name',
        'quantity',
        'unit',
        'max_capacity',
        'reorder_level',
    ];

    // Many-to-Many Relationship to Products (Recipes)
   public function products()
{
    return $this->belongsToMany(Product::class, 'product_ingredients', 'ingredient_id', 'product_id')
                ->withPivot('quantity_needed');
}
}