<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['product_name', 'price', 'stock_quantity', 'status'];

    /**
     * The ingredients that belong to the product.
     */
    public function ingredients()
    {
        // belongsToMany defines the many-to-many relationship.
        // withPivot allows us to access the 'quantity_needed' column from the middle table.
        return $this->belongsToMany(Ingredient::class, 'product_ingredients')
                    ->withPivot('quantity_needed')
                    ->withTimestamps();
    }
}