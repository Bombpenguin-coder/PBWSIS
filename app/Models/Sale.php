<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $primaryKey = 'sale_id';
    protected $fillable = ['sale_date', 'total_amount', 'discount_type', 'discount_amount', 'payment_method', 'order_channel'];

    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id', 'sale_id');
    }
}