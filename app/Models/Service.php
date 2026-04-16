<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'professional_id',
        'categorie_id',
        'city',
        'title',
        'description',
        'price_min',
        'price_max',
    ];
}
