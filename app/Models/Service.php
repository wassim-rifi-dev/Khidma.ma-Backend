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

    protected function casts(): array
    {
        return [
            'price_min' => 'float',
            'price_max' => 'float',
            'rating' => 'float',
        ];
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    public function category()
    {
        return $this->categorie();
    }

    public function requests() {
        return $this->hasMany(Request::class, 'service_id');
    }

    public function reviews() {
        return $this->hasManyThrough(Review::class, Request::class, 'service_id', 'order_id', 'id', 'id');
    }

    public function images()
    {
        return $this->hasMany(ServiceImage::class, 'service_id');
    }
}
