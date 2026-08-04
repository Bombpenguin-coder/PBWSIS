<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'ingredients';
    
    // Adjust primary key if yours is 'ingredient_id' instead of 'id'
    protected $primaryKey = 'id'; 

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_ingredients',
            'ingredient_id',
            'product_id'
        )->withPivot('quantity_required')->withTimestamps();
    }
}