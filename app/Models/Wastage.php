<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wastage extends Model
{
    use HasFactory;

    protected $primaryKey = 'wastage_id';
    protected $fillable = ['ingredient_id', 'quantity_wasted', 'reason', 'wastage_date'];

    // Establish relationship: A Wastage log belongs to an Ingredient
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'ingredient_id');
    }
}