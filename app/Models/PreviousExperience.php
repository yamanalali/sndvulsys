<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PreviousExperience extends Model
{
    use HasFactory;

    protected $table = 'previous_experiences';
    
    protected $fillable = [
        'volunteer-request_id',
        'title',
        'description',
        'organization',
        'position',
        'start_date',
        'end_date',
        'is_current'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function volunteerRequest()
    {
        return $this->belongsTo(VolunteerRequest::class, 'volunteer-request_id');
    }

    // نطاق للخبرات الحالية
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    // نطاق للخبرات السابقة
    public function scopePast($query)
    {
        return $query->where('is_current', false);
    }

    // حساب مدة الخبرة
    public function getDurationAttribute()
    {
        if ($this->is_current) {
            return $this->start_date->diffForHumans();
        }
        
        return $this->start_date->diffForHumans($this->end_date);
    }

    // الحصول على مدة الخبرة بالأيام
    public function getDurationInDaysAttribute()
    {
        if ($this->is_current) {
            return $this->start_date->diffInDays(now());
        }
        
        return $this->start_date->diffInDays($this->end_date);
    }

    // التحقق من صحة التواريخ
    public function getIsValidDateRangeAttribute()
    {
        if ($this->is_current) {
            return $this->start_date <= now();
        }
        
        return $this->start_date <= $this->end_date;
    }
} 