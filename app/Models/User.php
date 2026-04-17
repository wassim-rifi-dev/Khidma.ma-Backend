<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'is_active',
        'password',
        'photo'
    ];

    public function professional()
    {
        return $this->hasOne(professional::class, 'user_id');
    }

    public function requests() {
        return $this->hasMany(Request::class, 'client_id');
    }
}
