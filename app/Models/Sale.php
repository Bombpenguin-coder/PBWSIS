<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Sale extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $primaryKey = 'sale_id';

    protected $fillable = [
        'order_number',
        'sale_date',
        'subtotal',
        'vat_amount',
        'total_amount',
        'discount_type',
        'discount_amount',
        'payment_method',
        'order_channel',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id', 'sale_id');
    }
}