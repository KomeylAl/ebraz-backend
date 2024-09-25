<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'amount', 'status', 'time'];

    public function users() {
        return $this->belongsToMany(User::class, 'referral_user')->withPivot('role');
    }

    public function payment() {
        return $this->hasOne(Payment::class);
    }
}
