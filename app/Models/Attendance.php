<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_attendance',
        'time_in',
        'time_out',
        'latlon_in',
        'latlon_out',
    ];
    
    public function user() {        
        return $this->belongsTo(User::class, 'user_id');
    }
}