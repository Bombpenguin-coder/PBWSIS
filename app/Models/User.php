<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    use HasFactory, Notifiable;
    use \OwenIt\Auditing\Auditable;

    // Override the default 'id' primary key
    protected $primaryKey = 'users_id';

    // Allow these specific columns to be saved to the database (Added 'permissions')
    protected $fillable = [
        'username',
        'password',
        'role',
        'permissions',
        'contact_number',
    ];

    // Attributes hidden from serialization
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Attribute casts (Added 'permissions' => 'array')
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }
}