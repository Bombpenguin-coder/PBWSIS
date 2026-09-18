<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Category extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $primaryKey = 'category_id';
    protected $fillable = ['category_name', 'description'];

    // A category has many products
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
}