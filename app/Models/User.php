<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'phone',
        'role',
        'is_active',
        'password',
        'photo'
    ];

    protected $appends = [
        'name',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function professional()
    {
        return $this->hasOne(professional::class, 'user_id');
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'client_id');
    }

    public function reviews()
    {
        return $this->hasMany(Reviews::class, 'client_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'client_id');
    }

    public function messages()
    {
        return $this->hasMany(Messages::class, 'sender_id');
    }
}
