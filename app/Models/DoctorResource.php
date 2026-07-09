<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorResource extends Model
{
    protected $fillable = ['title', 'type', 'description', 'link', 'file'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
