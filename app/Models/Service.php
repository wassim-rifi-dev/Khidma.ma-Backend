<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'professional_id',
        'categorie_id',
        'city',
        'title',
        'description',
        'price_min',
        'price_max',
        'rating',
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Categories::class, 'categorie_id');
    }

    public function requests() {
        return $this->hasMany(Request::class, 'service_id');
    }

    public function review() {
        return $this->hasMany(Reviews::class , 'order_id');
    }
}
