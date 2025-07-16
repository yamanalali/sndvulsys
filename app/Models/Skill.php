<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{

    protected $fillable = ['name'];

    public function volunteerRequests()
    {
        return $this->belongsToMany(VolunteerRequest::class);
    }
    
}


