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
}
