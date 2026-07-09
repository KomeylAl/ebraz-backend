<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'title', 'about', 'phones', 'address','mobile_phones','logo_path','lat', 'long'
    ];

    public function getLogoPathAttribute($value)
    {
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        return $value ? asset('storage/' . $value) : null;
    }
}
