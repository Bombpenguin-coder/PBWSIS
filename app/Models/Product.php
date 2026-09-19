<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Product extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $primaryKey = 'product_id';
    protected $fillable = ['product_name', 'image', 'price', 'status'];
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
                // Determine pieces per box (fallback to 1 if not set)
                $ppb = $ingredient->pieces_per_box > 0 ? $ingredient->pieces_per_box : 1;

                // Calculate total loose pieces (Sealed Boxes * Pieces per Box + Loose Pieces)
                $totalAvailablePieces = ($ingredient->quantity * $ppb) + ($ingredient->total_pieces ?? 0);

                // Divide total available pieces by the required pieces per portion
                $possiblePortions[] = floor($totalAvailablePieces / $required);
            }
        }

        return empty($possiblePortions) ? 0 : (int) max(0, min($possiblePortions));
    }
}