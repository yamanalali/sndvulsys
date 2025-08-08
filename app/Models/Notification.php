<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'is_read',
        'read_at',
        'delivery_method',
        'email_sent',
        'sms_sent',
        'sent_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'email_sent' => 'boolean',
        'sms_sent' => 'boolean'
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    // النطاقات (Scopes)
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // الحصول على أنواع الإشعارات
    public static function getTypes()
    {
        return [
            'approval_decision' => 'قرار موافقة',
            'rejection_decision' => 'قرار رفض',
            'request_review' => 'طلب مراجعة',
            'status_update' => 'تحديث الحالة',
            'reminder' => 'تذكير',
            'system_alert' => 'تنبيه النظام'
        ];
    }

    // الحصول على طرق الإرسال
    public static function getDeliveryMethods()
    {
        return [
            'email' => 'البريد الإلكتروني',
            'sms' => 'الرسالة النصية',
            'in_app' => 'في التطبيق',
            'all' => 'جميع الطرق'
        ];
    }

    // التحقق من أن الإشعار مقروء
    public function isRead()
    {
        return $this->is_read;
    }

    // التحقق من أن الإشعار غير مقروء
    public function isUnread()
    {
        return !$this->is_read;
    }

    // تحديد الإشعار كمقروء
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    // تحديد الإشعار كغير مقروء
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    // الحصول على نوع الإشعار بالعربية
    public function getTypeTextAttribute()
    {
        $types = self::getTypes();
        return $types[$this->type] ?? 'غير محدد';
    }

    // الحصول على طريقة الإرسال بالعربية
    public function getDeliveryMethodTextAttribute()
    {
        $methods = self::getDeliveryMethods();
        return $methods[$this->delivery_method] ?? 'غير محدد';
    }

    // إنشاء إشعار قرار موافقة
    public static function createApprovalNotification($userId, $decisionId, $volunteerRequestId, $reviewerName)
    {
        return self::create([
            'user_id' => $userId,
            'title' => 'تمت الموافقة على طلبك',
            'message' => 'تمت الموافقة على طلب التطوع الخاص بك. يمكنك التواصل معنا للمتابعة.',
            'type' => 'approval_decision',
            'notifiable_type' => ApprovalDecision::class,
            'notifiable_id' => $decisionId,
            'data' => [
                'decision_id' => $decisionId,
                'volunteer-request_id' => $volunteerRequestId,
                'reviewer_name' => $reviewerName
            ]
        ]);
    }

    // إنشاء إشعار قرار رفض
    public static function createRejectionNotification($userId, $decisionId, $volunteerRequestId, $reason, $reviewerName)
    {
        return self::create([
            'user_id' => $userId,
            'title' => 'تم رفض طلبك',
            'message' => "تم رفض طلب التطوع الخاص بك. السبب: {$reason}",
            'type' => 'rejection_decision',
            'notifiable_type' => ApprovalDecision::class,
            'notifiable_id' => $decisionId,
            'data' => [
                'decision_id' => $decisionId,
                'volunteer-request_id' => $volunteerRequestId,
                'reason' => $reason,
                'reviewer_name' => $reviewerName
            ]
        ]);
    }

    // إرسال الإشعار
    public function send()
    {
        if ($this->sent_at) {
            return false;
        }

        $sent = false;

        // إرسال عبر البريد الإلكتروني
        if (in_array($this->delivery_method, ['email', 'all']) && !$this->email_sent) {
            $this->sendEmail();
            $sent = true;
        }

        // إرسال عبر الرسالة النصية
        if (in_array($this->delivery_method, ['sms', 'all']) && !$this->sms_sent) {
            $this->sendSMS();
            $sent = true;
        }

        if ($sent) {
            $this->update([
                'sent_at' => now(),
                'email_sent' => in_array($this->delivery_method, ['email', 'all']),
                'sms_sent' => in_array($this->delivery_method, ['sms', 'all'])
            ]);
        }

        return $sent;
    }

    // إرسال البريد الإلكتروني
    private function sendEmail()
    {
        // هنا يمكن إضافة منطق إرسال البريد الإلكتروني
        // يمكن استخدام Laravel Mail أو أي خدمة بريد إلكتروني أخرى
        \Log::info("Sending email notification: {$this->title} to user {$this->user_id}");
    }

    // إرسال الرسالة النصية
    private function sendSMS()
    {
        // هنا يمكن إضافة منطق إرسال الرسائل النصية
        // يمكن استخدام Twilio أو أي خدمة رسائل نصية أخرى
        \Log::info("Sending SMS notification: {$this->title} to user {$this->user_id}");
    }

    // الحصول على عدد الإشعارات غير المقروءة للمستخدم
    public static function getUnreadCount($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    // تحديد جميع إشعارات المستخدم كمقروءة
    public static function markAllAsRead($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }
} 