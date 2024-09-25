<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['referral_id', 'status', 'amount'];

    public function referral() {
        return $this->belongsTo(Referral::class);
    }
}
