<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class professional extends Model
{
    protected $fillable = [
        'user_id',
        'categorie_id',
        'city',
        'rating'
    ];

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

    public function chats()
    {
        return $this->hasMany(Chat::class, 'professional_id');
    }
}
