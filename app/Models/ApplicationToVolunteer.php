<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * ApplicationToVolunteer Model
 * 
 * This model handles volunteer applications with all data stored in JSON format
 * 
 * Available fields in details JSON:
 * - full_name, last_name, email, phone
 * - national_id, birth_date, gender
 * - address, city, country
 * - education_level, occupation, skills
 * - motivation, previous_experience
 * - preferred_area, availability
 * - has_previous_volunteering
 * - preferred_organization_type
 * - emergency_contact_name, emergency_contact_phone
 * 
 * Usage examples:
 * $application->full_name // Access via accessor
 * $application->full_name_with_last_name // Combined name
 * $application->status_text // Arabic status
 * $application->age // Calculated age
 * ApplicationToVolunteer::getStatistics() // Get statistics
 */

class ApplicationToVolunteer extends Model
{
    use HasFactory;

    protected $table = 'applicationtovolunteer';

    protected $fillable = [
        'uuid',
        'details',
        'status',
        'reviewed_at',
        'reviewed_by',
        'admin_notes',
    ];

    protected $casts = [
        'details' => 'array',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    // Accessor methods for easy access to details
    public function getFullNameAttribute()
    {
        return $this->details['full_name'] ?? null;
    }

    public function getLastNameAttribute()
    {
        return $this->details['last_name'] ?? null;
    }

    public function getEmailAttribute()
    {
        return $this->details['email'] ?? null;
    }

    public function getPhoneAttribute()
    {
        return $this->details['phone'] ?? null;
    }

    public function getNationalIdAttribute()
    {
        return $this->details['national_id'] ?? null;
    }

    public function getBirthDateAttribute()
    {
        return $this->details['birth_date'] ?? null;
    }

    public function getGenderAttribute()
    {
        return $this->details['gender'] ?? null;
    }

    public function getAddressAttribute()
    {
        return $this->details['address'] ?? null;
    }

    public function getCityAttribute()
    {
        return $this->details['city'] ?? null;
    }

    public function getCountryAttribute()
    {
        return $this->details['country'] ?? null;
    }

    public function getEducationLevelAttribute()
    {
        return $this->details['education_level'] ?? null;
    }

    public function getOccupationAttribute()
    {
        return $this->details['occupation'] ?? null;
    }

    public function getSkillsAttribute()
    {
        return $this->details['skills'] ?? null;
    }

    public function getMotivationAttribute()
    {
        return $this->details['motivation'] ?? null;
    }

    public function getPreviousExperienceAttribute()
    {
        return $this->details['previous_experience'] ?? null;
    }

    public function getPreferredAreaAttribute()
    {
        return $this->details['preferred_area'] ?? null;
    }

    public function getAvailabilityAttribute()
    {
        return $this->details['availability'] ?? null;
    }

    public function getHasPreviousVolunteeringAttribute()
    {
        return $this->details['has_previous_volunteering'] ?? false;
    }

    public function getPreferredOrganizationTypeAttribute()
    {
        return $this->details['preferred_organization_type'] ?? null;
    }

    public function getEmergencyContactNameAttribute()
    {
        return $this->details['emergency_contact_name'] ?? null;
    }

    public function getEmergencyContactPhoneAttribute()
    {
        return $this->details['emergency_contact_phone'] ?? null;
    }

    // Mutator methods for easy setting of details
    public function setFullNameAttribute($value)
    {
        $details = $this->details ?? [];
        $details['full_name'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setLastNameAttribute($value)
    {
        $details = $this->details ?? [];
        $details['last_name'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setEmailAttribute($value)
    {
        $details = $this->details ?? [];
        $details['email'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setPhoneAttribute($value)
    {
        $details = $this->details ?? [];
        $details['phone'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setNationalIdAttribute($value)
    {
        $details = $this->details ?? [];
        $details['national_id'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setBirthDateAttribute($value)
    {
        $details = $this->details ?? [];
        $details['birth_date'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setGenderAttribute($value)
    {
        $details = $this->details ?? [];
        $details['gender'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setAddressAttribute($value)
    {
        $details = $this->details ?? [];
        $details['address'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setCityAttribute($value)
    {
        $details = $this->details ?? [];
        $details['city'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setCountryAttribute($value)
    {
        $details = $this->details ?? [];
        $details['country'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setEducationLevelAttribute($value)
    {
        $details = $this->details ?? [];
        $details['education_level'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setOccupationAttribute($value)
    {
        $details = $this->details ?? [];
        $details['occupation'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setSkillsAttribute($value)
    {
        $details = $this->details ?? [];
        $details['skills'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setMotivationAttribute($value)
    {
        $details = $this->details ?? [];
        $details['motivation'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setPreviousExperienceAttribute($value)
    {
        $details = $this->details ?? [];
        $details['previous_experience'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setPreferredAreaAttribute($value)
    {
        $details = $this->details ?? [];
        $details['preferred_area'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setAvailabilityAttribute($value)
    {
        $details = $this->details ?? [];
        $details['availability'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setHasPreviousVolunteeringAttribute($value)
    {
        $details = $this->details ?? [];
        $details['has_previous_volunteering'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setPreferredOrganizationTypeAttribute($value)
    {
        $details = $this->details ?? [];
        $details['preferred_organization_type'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setEmergencyContactNameAttribute($value)
    {
        $details = $this->details ?? [];
        $details['emergency_contact_name'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    public function setEmergencyContactPhoneAttribute($value)
    {
        $details = $this->details ?? [];
        $details['emergency_contact_phone'] = $value;
        $this->attributes['details'] = json_encode($details);
    }

    // Relationships
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeWithdrawn($query)
    {
        return $query->where('status', 'withdrawn');
    }

    public function scopeReviewed($query)
    {
        return $query->whereNotNull('reviewed_at');
    }

    public function scopeUnreviewed($query)
    {
        return $query->whereNull('reviewed_at');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Methods
    public function canBeReviewed()
    {
        return $this->status === 'pending';
    }

    public function approve($reviewerId = null, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
            'admin_notes' => $notes,
        ]);
    }

    public function reject($reviewerId = null, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
            'admin_notes' => $notes,
        ]);
    }

    public function withdraw()
    {
        $this->update(['status' => 'withdrawn']);
    }

    // Helper methods for getting full information
    public function getFullNameWithLastNameAttribute()
    {
        return trim($this->full_name . ' ' . $this->last_name);
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            'withdrawn' => 'منسحب',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    public function getGenderTextAttribute()
    {
        $genders = [
            'male' => 'ذكر',
            'female' => 'أنثى',
            'other' => 'آخر',
        ];
        
        return $genders[$this->gender] ?? $this->gender;
    }

    public function getAgeAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }
        
        return now()->diffInYears($this->birth_date);
    }

    public function getIsReviewedAttribute()
    {
        return !is_null($this->reviewed_at);
    }

    public function getDaysSinceCreatedAttribute()
    {
        return $this->created_at ? now()->diffInDays($this->created_at) : 0;
    }

    // Additional scopes for filtering
    public function scopeByCity($query, $city)
    {
        return $query->whereJsonContains('details->city', $city);
    }

    public function scopeByPreferredArea($query, $area)
    {
        return $query->whereJsonContains('details->preferred_area', $area);
    }

    public function scopeByGender($query, $gender)
    {
        return $query->whereJsonContains('details->gender', $gender);
    }

    public function scopeByEducationLevel($query, $level)
    {
        return $query->whereJsonContains('details->education_level', $level);
    }

    public function scopeWithPreviousVolunteering($query)
    {
        return $query->whereJsonContains('details->has_previous_volunteering', true);
    }

    public function scopeWithoutPreviousVolunteering($query)
    {
        return $query->whereJsonContains('details->has_previous_volunteering', false);
    }

    // Validation rules
    public static function getValidationRules()
    {
        return [
            'full_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'national_id' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'education_level' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'skills' => 'nullable|string|max:500',
            'motivation' => 'nullable|string|max:1000',
            'previous_experience' => 'nullable|string|max:1000',
            'preferred_area' => 'nullable|string|max:100',
            'availability' => 'nullable|string|max:200',
            'has_previous_volunteering' => 'nullable|boolean',
            'preferred_organization_type' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ];
    }

    // Get all available cities
    public static function getAvailableCities()
    {
        return self::selectRaw('JSON_EXTRACT(details, "$.city") as city')
            ->whereNotNull('details->city')
            ->distinct()
            ->pluck('city')
            ->filter()
            ->sort()
            ->values();
    }

    // Get all available preferred areas
    public static function getAvailablePreferredAreas()
    {
        return self::selectRaw('JSON_EXTRACT(details, "$.preferred_area") as preferred_area')
            ->whereNotNull('details->preferred_area')
            ->distinct()
            ->pluck('preferred_area')
            ->filter()
            ->sort()
            ->values();
    }

    // Get statistics
    public static function getStatistics()
    {
        return [
            'total' => self::count(),
            'pending' => self::pending()->count(),
            'approved' => self::approved()->count(),
            'rejected' => self::rejected()->count(),
            'withdrawn' => self::withdrawn()->count(),
            'recent' => self::recent(7)->count(),
            'reviewed' => self::reviewed()->count(),
            'unreviewed' => self::unreviewed()->count(),
        ];
    }
} 