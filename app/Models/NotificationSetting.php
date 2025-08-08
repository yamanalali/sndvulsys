<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assignment_notifications',
        'assignment_email',
        'assignment_database',
        'status_update_notifications',
        'status_update_email',
        'status_update_database',
        'deadline_reminder_notifications',
        'deadline_reminder_email',
        'deadline_reminder_database',
        'deadline_reminder_days',
        'dependency_notifications',
        'dependency_email',
        'dependency_database',
        'email_notifications',
        'database_notifications',
        'browser_notifications',
    ];

    protected $casts = [
        'assignment_notifications' => 'boolean',
        'assignment_email' => 'boolean',
        'assignment_database' => 'boolean',
        'status_update_notifications' => 'boolean',
        'status_update_email' => 'boolean',
        'status_update_database' => 'boolean',
        'deadline_reminder_notifications' => 'boolean',
        'deadline_reminder_email' => 'boolean',
        'deadline_reminder_database' => 'boolean',
        'deadline_reminder_days' => 'integer',
        'dependency_notifications' => 'boolean',
        'dependency_email' => 'boolean',
        'dependency_database' => 'boolean',
        'email_notifications' => 'boolean',
        'database_notifications' => 'boolean',
        'browser_notifications' => 'boolean',
    ];

    /**
     * علاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الحصول على إعدادات المستخدم أو إنشاء إعدادات افتراضية
     */
    public static function getOrCreateForUser($userId)
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'assignment_notifications' => true,
                'assignment_email' => true,
                'assignment_database' => true,
                'status_update_notifications' => true,
                'status_update_email' => true,
                'status_update_database' => true,
                'deadline_reminder_notifications' => true,
                'deadline_reminder_email' => true,
                'deadline_reminder_database' => true,
                'deadline_reminder_days' => 1,
                'dependency_notifications' => true,
                'dependency_email' => true,
                'dependency_database' => true,
                'email_notifications' => true,
                'database_notifications' => true,
                'browser_notifications' => false,
            ]
        );
    }

    /**
     * التحقق من تفعيل إشعارات التخصيص
     */
    public function isAssignmentNotificationsEnabled()
    {
        return $this->assignment_notifications;
    }

    /**
     * التحقق من تفعيل إشعارات تحديث الحالة
     */
    public function isStatusUpdateNotificationsEnabled()
    {
        return $this->status_update_notifications;
    }

    /**
     * التحقق من تفعيل إشعارات التذكيرات
     */
    public function isDeadlineReminderNotificationsEnabled()
    {
        return $this->deadline_reminder_notifications;
    }

    /**
     * التحقق من تفعيل إشعارات التبعيات
     */
    public function isDependencyNotificationsEnabled()
    {
        return $this->dependency_notifications;
    }

    /**
     * التحقق من تفعيل إشعارات البريد الإلكتروني
     */
    public function isEmailNotificationsEnabled()
    {
        return $this->email_notifications;
    }

    /**
     * التحقق من تفعيل إشعارات قاعدة البيانات
     */
    public function isDatabaseNotificationsEnabled()
    {
        return $this->database_notifications;
    }
}
