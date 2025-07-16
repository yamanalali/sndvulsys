<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'national_id',
        'birth_date',
        'gender',
        'address',
        'city',
        'country',
        'education_level',
        'occupation',
        'skills',
        'motivation',
        'previous_experience',
        'preferred_area',
        'availability',
        'has_previous_volunteering',
        'preferred_organization_type',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
        'reviewed_at',
        'reviewed_by',
        'admin_notes',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function skills()
{
    return $this->belongsToMany(Skill::class);
}
public function availabilities()
{
    return $this->hasMany(Availability::class);
}
public function workflows()
{
    return $this->hasMany(Workflow::class);
}

public function previousExperiences()
{
    return $this->hasMany(PreviousExperience::class);
}


}

