<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerPoint extends Model
{
    protected $fillable = [
        'volunteer_id',
        'total_points',
        'last_updated_at',
    ];

    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }
} 