<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $fillable = [
        'name'
    ];

    public function services()
    {
        return $this->hasMany(Service::class, 'categorie_id');
    }
}
