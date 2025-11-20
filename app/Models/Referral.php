<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Doctor;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'amount', 'status', 'time'];

    public function client()
    {
        return $this->belongsToMany(Client::class, 'referral_user', 'referral_id', 'client_id')->withPivot('doctor_id');
    }

    public function doctor()
    {
        return $this->belongsToMany(Doctor::class, 'referral_user', 'referral_id', 'doctor_id')->withPivot('client_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

}
