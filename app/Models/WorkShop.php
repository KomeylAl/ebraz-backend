<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkShop extends Model
{
    use HasFactory;

    protected $with = [];
    protected $fillable = [
        'title', 
        'slug', 
        'excerpt', 
        'content',
        'organizers',
        'start_date',
        'end_date',
        'week_day',
        'time',
        'img_path',
        'sessions',
        'participants'
    ];

    public function participants() {
        return $this->belongsToMany(Participant::class, 'participant_workshop')
            ->withPivot('approved', 'joined_at')
            ->withTimestamps();
    }

    public function sessions() {
        return $this->hasMany(WorkshopSession::class);
    }

    // Accessor برای برگردوندن آدرس کامل تصویر
    public function getImgPathAttribute($value) {
        return $value ? asset('storage/' . $value) : null;
    }
}
