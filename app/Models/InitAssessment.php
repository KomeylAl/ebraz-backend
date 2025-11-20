<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'time',
        'status',
        'file_path'
    ];

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'assessment_user', 'init_assessment_id', 'client_id')->withPivot('doctor_id');
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'assessment_user', 'init_assessment_id', 'doctor_id')->withPivot('client_id');
    }
}
