<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_permission',
        'reason',
        'image',
        'is_approved',
        'permit_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}