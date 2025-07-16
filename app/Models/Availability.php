<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Availability extends Model
{
    use HasFactory;

    protected $fillable = ['volunteer_request_id', 'day', 'time'];

    public function volunteerRequest()
    {
        return $this->belongsTo(VolunteerRequest::class);
    }
}