<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name'
    ];

    public function professionals()
    {
        return $this->hasMany(Professional::class, 'categorie_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'categorie_id');
    }
}
