<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalDecision extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer-request_id',
        'decision_status',
        'decision_reason',
        'decision_by',
        'decision_at'
    ];

    protected $casts = [
        'decision_at' => 'datetime'
    ];

    // العلاقات
    public function volunteerRequest()
    {
        return $this->belongsTo(VolunteerRequest::class, 'volunteer-request_id');
    }

    public function decisionBy()
    {
        return $this->belongsTo(User::class, 'decision_by');
    }

    // النطاقات (Scopes)
    public function scopeApproved($query)
    {
        return $query->where('decision_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('decision_status', 'rejected');
    }

    // التحقق من أن القرار موافقة
    public function isApproved()
    {
        return $this->decision_status === 'approved';
    }

    // التحقق من أن القرار رفض
    public function isRejected()
    {
        return $this->decision_status === 'rejected';
    }

    // التحقق من أن القرار موافقة (للتوافق مع الكود القديم)
    public function isApproval()
    {
        return $this->isApproved();
    }

    // التحقق من أن القرار رفض (للتوافق مع الكود القديم)
    public function isRejection()
    {
        return $this->isRejected();
    }

    // الحصول على حالة القرار بالعربية
    public function getDecisionStatusTextAttribute()
    {
        return $this->isApproved() ? 'مقبول' : 'مرفوض';
    }

    // إنشاء قرار موافقة
    public static function createApproval($volunteerRequestId, $decisionBy, $reason)
    {
        // التأكد من وجود معرف المستخدم
        if (!$decisionBy) {
            $defaultUser = \App\Models\User::firstOrCreate(
                ['email' => 'admin@system.com'],
                [
                    'name' => 'مدير النظام',
                    'password' => bcrypt('password'),
                    'role_name' => 'Admin',
                    'status' => 'Active',
                ]
            );
            $decisionBy = $defaultUser->id;
        }

        return self::create([
            'volunteer-request_id' => $volunteerRequestId,
            'decision_status' => 'approved',
            'decision_reason' => $reason,
            'decision_by' => $decisionBy,
            'decision_at' => now()
        ]);
    }

    // إنشاء قرار رفض
    public static function createRejection($volunteerRequestId, $decisionBy, $reason)
    {
        // التأكد من وجود معرف المستخدم
        if (!$decisionBy) {
            $defaultUser = \App\Models\User::firstOrCreate(
                ['email' => 'admin@system.com'],
                [
                    'name' => 'مدير النظام',
                    'password' => bcrypt('password'),
                    'role_name' => 'Admin',
                    'status' => 'Active',
                ]
            );
            $decisionBy = $defaultUser->id;
        }

        return self::create([
            'volunteer-request_id' => $volunteerRequestId,
            'decision_status' => 'rejected',
            'decision_reason' => $reason,
            'decision_by' => $decisionBy,
            'decision_at' => now()
        ]);
    }
} 