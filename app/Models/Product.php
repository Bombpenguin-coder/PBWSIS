<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    protected $fillable = ['product_name', 'price', 'status'];
    protected $appends = ['available_stock'];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredients', 'product_id', 'ingredient_id')
                    ->withPivot('quantity_needed'); // Matches migration column name
    }

    public function getAvailableStockAttribute()
    {
        if ($this->ingredients->isEmpty()) {
            return 999;
        }

        $possiblePortions = [];

        foreach ($this->ingredients as $ingredient) {
            // Uses 'quantity_needed' from pivot
            $required = $ingredient->pivot->quantity_needed ?? 0;
            if ($required > 0) {
                $possiblePortions[] = floor($ingredient->quantity / $required);
            }
        }

        return empty($possiblePortions) ? 0 : (int) max(0, min($possiblePortions));
    }
}