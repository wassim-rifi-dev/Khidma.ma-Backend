<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    protected $fillable = [
        'order_id',
        'client_id',
        'rating',
        'comment',
    ];

    public function order() {
        return $this->belongsTo(Request::class, 'order_id');
    }

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }
}
