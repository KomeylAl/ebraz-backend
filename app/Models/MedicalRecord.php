<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_number',
        'reference_source',
        'client_id',
        'admission_date',
        'visit_date'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function images()
    {
        return $this->hasMany(RecordImage::class);
    }

    public function companion()
    {
        return $this->belongsTo(Companion::class);
    }

}
