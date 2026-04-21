<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class professional extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'categorie_id',
        'city',
        'rating',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'professional_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Categories::class, 'categorie_id');
    }

    public function category()
    {
        return $this->categorie();
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'professional_id');
    }

    public function requests()
    {
        return $this->hasManyThrough(Request::class, Service::class, 'professional_id', 'service_id');
    }
}
