<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreviousExperience extends Model
{
    protected $fillable = [
        'volunteer_request_id',
        'description',
    ];

    public function volunteerRequest()
    {
        return $this->belongsTo(VolunteerRequest::class);
    }
} 