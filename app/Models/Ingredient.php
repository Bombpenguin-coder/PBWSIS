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
        'quantity',        // Sealed Boxes
        'pieces_per_box',  // Pieces per box capacity
        'total_pieces',    // Active open/loose pieces
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

    /**
     * Deduct loose pieces needed for a product sale.
     * Automatically opens unopened sealed boxes as active stock depletes.
     *
     * @param float $requiredPieces Total loose pieces needed for order
     * @return bool
     */
    public function deductPieces(float $requiredPieces): bool
    {
        $ppb = $this->pieces_per_box > 0 ? $this->pieces_per_box : 1;

        // Calculate grand total available loose pieces across sealed boxes and active stock
        $grandTotalAvailable = ($this->quantity * $ppb) + $this->total_pieces;

        if ($grandTotalAvailable < $requiredPieces) {
            return false; // Insufficient stock
        }

        // Deduct required amount from active loose pieces
        $this->total_pieces -= $requiredPieces;

        // Auto-unbox sealed boxes if active pieces drop below 0
        while ($this->total_pieces < 0 && $this->quantity > 0) {
            $this->quantity -= 1;       // Open 1 sealed box
            $this->total_pieces += $ppb; // Add box capacity to loose stock
        }

        $this->save();

        return true;
    }
}