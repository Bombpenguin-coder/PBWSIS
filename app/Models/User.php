<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Custom primary key
    protected $primaryKey = 'users_id';

    // Mass assignable attributes
    protected $fillable = [
        'username',
        'password',
        'role',
        'contact_number',
    ];

    // Attributes hidden from serialization
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Attribute casts
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}