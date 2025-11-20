<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'thumbnail', 'content'
    ];

    public function doctors() {
        return $this->belongsToMany(Doctor::class);
    }
}
