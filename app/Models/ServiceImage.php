<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceImage extends Model
{
    protected $table = 'service_images';

    protected $fillable = [
        'service_id',
        'image_url',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
