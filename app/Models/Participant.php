<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_en', 'phone', 'national_code', 'gender', 'approved', 'joined_at'];

    public function workshops() {
        return $this->belongsToMany(Workshop::class, 'participant_workshop')
            ->withPivot('approved', 'joined_at')
            ->withTimestamps();
    }
}
