<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\Assignment;
use App\Notifications\TaskAssignmentNotification;
use App\Notifications\TaskStatusUpdateNotification;
use App\Notifications\TaskDeadlineReminderNotification;
use App\Notifications\TaskDependencyNotification;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * إرسال إشعار تخصيص مهمة
     */
    public function sendTaskAssignmentNotification(Task $task, User $assignedUser, User $assignedBy = null)
    {
        $settings = $assignedUser->getNotificationSettings();
        
        if ($settings->isAssignmentNotificationsEnabled()) {
            $assignedUser->notify(new TaskAssignmentNotification($task, $assignedBy));
        }
    }

    /**
     * إرسال إشعار تحديث حالة المهمة
     */
    public function sendTaskStatusUpdateNotification(Task $task, $oldStatus, $newStatus, User $updatedBy = null)
    {
        // إرسال الإشعار لجميع المكلفين بالمهمة
        foreach ($task->assignments as $assignment) {
            $user = $assignment->user;
            $settings = $user->getNotificationSettings();
            
            if ($settings->isStatusUpdateNotificationsEnabled()) {
                $user->notify(new TaskStatusUpdateNotification($task, $oldStatus, $newStatus, $updatedBy));
            }
        }
    }

    /**
     * إرسال إشعار تذكير بالموعد النهائي
     */
    public function sendDeadlineReminderNotifications()
    {
        $tasks = Task::where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('deadline', '>=', Carbon::now())
            ->where('deadline', '<=', Carbon::now()->addDays(7))
            ->get();

        foreach ($tasks as $task) {
            foreach ($task->assignments as $assignment) {
                $user = $assignment->user;
                $settings = $user->getNotificationSettings();
                
                if ($settings->isDeadlineReminderNotificationsEnabled()) {
                    $daysLeft = Carbon::now()->diffInDays($task->deadline, false);
                    
                    // إرسال الإشعار فقط إذا كان عدد الأيام المتبقية يساوي أو أقل من الإعداد
                    if ($daysLeft <= $settings->deadline_reminder_days) {
                        $user->notify(new TaskDeadlineReminderNotification($task, $daysLeft));
                    }
                }
            }
        }
    }

    /**
     * إرسال إشعار إضافة تبعية
     */
    public function sendTaskDependencyNotification(Task $task, Task $dependencyTask, $dependencyType, User $addedBy = null)
    {
        // إرسال الإشعار لجميع المكلفين بالمهمة
        foreach ($task->assignments as $assignment) {
            $user = $assignment->user;
            $settings = $user->getNotificationSettings();
            
            if ($settings->isDependencyNotificationsEnabled()) {
                $user->notify(new TaskDependencyNotification($task, $dependencyTask, $dependencyType, $addedBy));
            }
        }
    }

    /**
     * إرسال إشعارات التذكيرات اليومية
     */
    public function sendDailyReminders()
    {
        $this->sendDeadlineReminderNotifications();
    }

    /**
     * إرسال إشعارات التذكيرات الأسبوعية
     */
    public function sendWeeklyReminders()
    {
        // يمكن إضافة منطق إضافي للتذكيرات الأسبوعية
        $this->sendDeadlineReminderNotifications();
    }

    /**
     * الحصول على إحصائيات الإشعارات للمستخدم
     */
    public function getUserNotificationStats(User $user)
    {
        $notifications = $user->notifications();
        
        return [
            'total' => $notifications->count(),
            'unread' => $notifications->where('read_at', null)->count(),
            'read' => $notifications->where('read_at', '!=', null)->count(),
            'today' => $notifications->whereDate('created_at', Carbon::today())->count(),
            'this_week' => $notifications->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
        ];
    }

    /**
     * تحديث إعدادات الإشعارات للمستخدم
     */
    public function updateUserNotificationSettings(User $user, array $settings)
    {
        $notificationSettings = $user->getNotificationSettings();
        $notificationSettings->update($settings);
        
        return $notificationSettings;
    }

    /**
     * إعادة تعيين إعدادات الإشعارات إلى الافتراضية
     */
    public function resetUserNotificationSettings(User $user)
    {
        $notificationSettings = $user->getNotificationSettings();
        $notificationSettings->delete();
        
        return $user->getNotificationSettings();
    }
}
