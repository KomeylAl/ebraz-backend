<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkshopSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_date', 
        'start_time', 
        'end_time',
        'location',
        'link',
        'work_shop_id',
        'title',
        'description'
    ];

    public function workshop() {
        return $this->belongsTo(Workshop::class);
    }
}
