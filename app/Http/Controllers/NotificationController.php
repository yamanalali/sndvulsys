<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth');
    }

    /**
     * عرض صفحة الإشعارات
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(20);
        $stats = $this->notificationService->getUserNotificationStats($user);
        
        return view('notifications.index', compact('notifications', 'stats'));
    }

    /**
     * عرض إعدادات الإشعارات
     */
    public function settings()
    {
        $user = Auth::user();
        $settings = $user->getNotificationSettings();
        
        return view('notifications.settings', compact('settings'));
    }

    /**
     * تحديث إعدادات الإشعارات
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'assignment_notifications' => 'boolean',
            'assignment_email' => 'boolean',
            'assignment_database' => 'boolean',
            'status_update_notifications' => 'boolean',
            'status_update_email' => 'boolean',
            'status_update_database' => 'boolean',
            'deadline_reminder_notifications' => 'boolean',
            'deadline_reminder_email' => 'boolean',
            'deadline_reminder_database' => 'boolean',
            'deadline_reminder_days' => 'integer|min:0|max:30',
            'dependency_notifications' => 'boolean',
            'dependency_email' => 'boolean',
            'dependency_database' => 'boolean',
            'email_notifications' => 'boolean',
            'database_notifications' => 'boolean',
            'browser_notifications' => 'boolean',
        ]);

        $this->notificationService->updateUserNotificationSettings($user, $validated);
        
        return redirect()->route('notifications.settings')
            ->with('success', 'تم تحديث إعدادات الإشعارات بنجاح');
    }

    /**
     * إعادة تعيين إعدادات الإشعارات
     */
    public function resetSettings()
    {
        $user = Auth::user();
        $this->notificationService->resetUserNotificationSettings($user);
        
        return redirect()->route('notifications.settings')
            ->with('success', 'تم إعادة تعيين إعدادات الإشعارات إلى الافتراضية');
    }

    /**
     * تحديد الإشعار كمقروء
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }
        
        $user->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * حذف إشعار
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * حذف جميع الإشعارات
     */
    public function destroyAll()
    {
        $user = Auth::user();
        $user->notifications()->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * الحصول على عدد الإشعارات غير المقروءة
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['count' => 0]);
        }
        
        $count = $user->unreadNotifications->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * الحصول على الإشعارات غير المقروءة
     */
    public function getUnreadNotifications()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['notifications' => []]);
        }
        
        $notifications = $user->unreadNotifications()->take(10)->get();
        
        return response()->json(['notifications' => $notifications]);
    }

    /**
     * إرسال تذكيرات تجريبية
     */
    public function sendTestReminders()
    {
        try {
            $this->notificationService->sendDeadlineReminderNotifications();
            return response()->json(['success' => true, 'message' => 'تم إرسال التذكيرات التجريبية بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }
}
