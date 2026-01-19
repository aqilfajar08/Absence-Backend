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
        'is_late',
        'note',
        'status',
    ];

    protected $casts = [
        'date_attendance' => 'date',
        // Don't cast TIME fields as datetime - they're just time values
    ];
    
    public function user() {        
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Accessor to format time_in (already in correct timezone from database)
    public function getTimeInFormattedAttribute()
    {
        if (!$this->time_in) return null;
        // Since time_in is a TIME field, it doesn't need timezone conversion
        // Just format it properly
        return \Carbon\Carbon::parse($this->time_in)->format('H:i:s');
    }
    
    // Accessor to format time_out (already in correct timezone from database)
    public function getTimeOutFormattedAttribute()
    {
        if (!$this->time_out) return null;
        // Since time_out is a TIME field, it doesn't need timezone conversion
        // Just format it properly
        return \Carbon\Carbon::parse($this->time_out)->format('H:i:s');
    }
    
    // Accessor to format date in WITA timezone
    public function getDateAttendanceFormattedAttribute()
    {
        return \Carbon\Carbon::parse($this->date_attendance)->setTimezone('Asia/Makassar')->format('Y-m-d');
    }
    
    // Get raw time values as stored in database (no timezone conversion needed)
    public function getTimeInRawAttribute()
    {
        return $this->attributes['time_in'];
    }
    
    public function getTimeOutRawAttribute()
    {
        return $this->attributes['time_out'];
    }
}