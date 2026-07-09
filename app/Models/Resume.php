<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'title',
        'bio',
        'specialization',
        'educations',
        'experiences',
        'skills',
        'certifications',
        'content',
        'social_links',
        'file'
    ];

    protected $casts = [
        'educations' => 'array',
        'experiences' => 'array',
        'skills' => 'array',
        'certifications' => 'array',
        'social_links' => 'array',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
