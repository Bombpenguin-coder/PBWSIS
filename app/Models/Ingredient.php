<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Ingredient extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

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