<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class professional extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'city'
    ];
}
