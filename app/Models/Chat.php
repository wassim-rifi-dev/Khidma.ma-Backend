<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'client_id',
        'professional_id',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function professional()
    {
        return $this->belongsTo(professional::class, 'professional_id');
    }

    public function messages()
    {
        return $this->hasMany(Messages::class, 'chat_id');
    }
}
