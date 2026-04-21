<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $fillable = [
        'client_id',
        'service_id',
        'message',
        'price',
        'is_Cancled',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'is_Cancled' => 'boolean',
        ];
    }

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function review() {
        return $this->hasOne(Reviews::class, 'order_id');
    }
}
