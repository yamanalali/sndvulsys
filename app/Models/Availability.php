<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;
    protected $foreignKey = 'volunteer-request_id';
    protected $fillable = [
        'volunteer-request_id', 
        'day', 
        'time_slot',
        'start_time',
        'end_time',
        'is_available',
        'notes',
        'preferred_hours_per_week'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_available' => 'boolean',
    ];

    public function volunteerRequest()
    {
        return $this->belongsTo(VolunteerRequest::class, 'volunteer-request_id');
    }

    // نطاق للأوقات المتاحة
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    // نطاق حسب اليوم
    public function scopeByDay($query, $day)
    {
        return $query->where('day', $day);
    }

    // الحصول على أيام الأسبوع
    public static function getDays()
    {
        return [
            'saturday' => 'السبت',
            'sunday' => 'الأحد',
            'monday' => 'الإثنين',
            'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
            'thursday' => 'الخميس',
            'friday' => 'الجمعة'
        ];
    }

    // الحصول على فترات الوقت
    public static function getTimeSlots()
    {
        return [
            'morning' => 'صباحاً (8:00 - 12:00)',
            'afternoon' => 'ظهراً (12:00 - 16:00)',
            'evening' => 'مساءً (16:00 - 20:00)',
            'night' => 'ليلاً (20:00 - 24:00)',
            'flexible' => 'مرن'
        ];
    }

    // حساب ساعات التوفر
    public function getHoursAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->diffInHours($this->end_time);
        }
        return 0;
    }

    // التحقق من التوفر في وقت معين
    public function isAvailableAt($time)
    {
        if (!$this->is_available) {
            return false;
        }

        if ($this->start_time && $this->end_time) {
            return $time->between($this->start_time, $this->end_time);
        }

        return true;
    }
}