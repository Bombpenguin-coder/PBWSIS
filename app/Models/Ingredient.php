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
        'max_capacity',    // Stores the reference batch total (e.g. 20 boxes)
        'reorder_level',   // Stores the 30% yellow warning threshold
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredients', 'ingredient_id', 'product_id')
                    ->withPivot('quantity_needed');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'ingredient_id', 'ingredient_id');
    }

    /**
     * Yellow Warning Threshold (30% of reference batch capacity)
     */
    public function getWarningThresholdAttribute(): int
    {
        $max = $this->max_capacity > 0 ? $this->max_capacity : 1;
        return (int) ceil($max * 0.30);
    }

    /**
     * Red Critical Threshold (10% of reference batch capacity)
     */
    public function getCriticalThresholdAttribute(): int
    {
        $max = $this->max_capacity > 0 ? $this->max_capacity : 1;
        return (int) ceil($max * 0.10);
    }

    /**
     * Returns 3-tier stock status: 'critical' (red), 'warning' (yellow), or 'optimal' (green)
     */
    public function getStockStatusAttribute(): string
    {
        $boxes = (float) $this->quantity;

        if ($boxes <= 0 || $boxes <= $this->critical_threshold) {
            return 'critical';
        }

        if ($boxes <= $this->warning_threshold) {
            return 'warning';
        }

        return 'optimal';
    }

    public function deductPieces(float $requiredPieces): bool
    {
        $ppb = $this->pieces_per_box > 0 ? $this->pieces_per_box : 1;
        $grandTotalAvailable = ($this->quantity * $ppb) + $this->total_pieces;

        if ($grandTotalAvailable < $requiredPieces) {
            return false;
        }

        $this->total_pieces -= $requiredPieces;

        while ($this->total_pieces < 0 && $this->quantity > 0) {
            $this->quantity -= 1;
            $this->total_pieces += $ppb;
        }

        $this->save();

        return true;
    }
}