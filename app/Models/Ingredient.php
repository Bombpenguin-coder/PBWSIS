<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['ingredient_name', 'quantity', 'unit', 'max_capacity', 'reorder_level'];

    /**
     * The products that use this ingredient.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredients')
                    ->withPivot('quantity_needed')
                    ->withTimestamps();
    }
}