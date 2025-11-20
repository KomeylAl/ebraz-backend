<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'notifiable_type',
        'notifiable_id',
        'priority',
        'delivery_channels' => 'array',
        'status',
        'meta',
        'scheduled_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'scheduled_at' => 'datetime',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function reads()
    {
        return $this->hasMany(NotificationRead::class);
    }
}
