<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Referral;
use App\Models\InitAssessment;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar',
        'resume',
        'phone',
        'national_code',
        'birth_date',
        'card_number',
        'medical_number',
        'email',
        'days',
        'profile_path'
    ];

    public function resumeRecord()
    {
        return $this->hasOne(Resume::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'referral_user', 'doctor_id', 'client_id');
    }

    public function referrals()
    {
        return $this->belongsToMany(Referral::class, 'referral_user', 'doctor_id', 'referral_id');
    }

    public function assessments()
    {
        return $this->belongsToMany(InitAssessment::class, 'assessment_user', 'doctor_id', 'init_assessment_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class);
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
