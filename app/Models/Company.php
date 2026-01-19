<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'address',
        'latitude',
        'longitude',
        'longitude',
        'radius_km',
        'time_in',
        'time_out',
        'late_fee_per_minute',
        'late_fee_interval_minutes',
        'late_threshold_1',
        'late_threshold_2',
        'late_threshold_3',
        'gph_late_1_percent',
        'gph_late_2_percent',
        'gph_late_3_percent',
        'gph_late_4_percent',
    ];
}
