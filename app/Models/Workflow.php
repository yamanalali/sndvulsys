<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_request_id',
        'reviewed_by',
        'status',
        'reviewed_at',
        'notes'
    ];

    public function volunteerRequest()
    {
        return $this->belongsTo(VolunteerRequest::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}