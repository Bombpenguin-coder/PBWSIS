<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VatSetting extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'rate', 'is_inclusive', 'is_active'];
}
