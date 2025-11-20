<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;
use App\Models\Referral;
use App\Models\InitAssessment;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'birth_date',
        'address'
    ];

    public function referrals()
    {
        return $this->belongsToMany(Referral::class, 'referral_user', 'client_id', 'referral_id');
    }

    public function assessments()
    {
        return $this->belongsToMany(InitAssessment::class, 'assessment_user', 'client_id', 'init_assessment_id');
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'referral_user', 'client_id', 'doctor_id');
    }

    public function record()
    {
        return $this->hasOne(MedicalRecord::class, 'client_id', 'id');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function receivedNotifications()
    {
        return $this->morphMany(NotificationRead::class, 'receiver');
    }

}
