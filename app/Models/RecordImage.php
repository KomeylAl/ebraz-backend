<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordImage extends Model
{
    use HasFactory;

    public function record()
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

}
