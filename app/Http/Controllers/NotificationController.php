<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    /**
     * عرض قائمة إشعارات المستخدم
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id());

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->where('is_read', true);
            } else {
                $query->where('is_read', false);
            }
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        $types = Notification::getTypes();
        $deliveryMethods = Notification::getDeliveryMethods();

        return view('notifications.index', compact('notifications', 'types', 'deliveryMethods'));
    }

    /**
     * عرض تفاصيل الإشعار
     */
    public function show($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);

        // تحديد الإشعار كمقروء
        $notification->markAsRead();

        return view('notifications.show', compact('notification'));
    }

    /**
     * تحديد الإشعار كمقروء
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'تم تحديد الإشعار كمقروء');
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        Notification::markAllAsRead(Auth::id());

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة');
    }

    /**
     * حذف الإشعار
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'تم حذف الإشعار بنجاح');
    }

    /**
     * حذف جميع الإشعارات المقروءة
     */
    public function destroyRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', true)
            ->delete();

        return redirect()->back()->with('success', 'تم حذف جميع الإشعارات المقروءة');
    }

    /**
     * الحصول على عدد الإشعارات غير المقروءة (API)
     */
    public function getUnreadCount()
    {
        $count = Notification::getUnreadCount(Auth::id());
        return response()->json(['count' => $count]);
    }

    /**
     * الحصول على الإشعارات غير المقروءة (API)
     */
    public function getUnreadNotifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($notifications);
    }

    /**
     * إرسال إشعار تجريبي
     */
    public function sendTestNotification(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:approval_decision,rejection_decision,request_review,status_update,reminder,system_alert',
            'delivery_method' => 'required|in:email,sms,in_app,all'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $notification = Notification::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'delivery_method' => $request->delivery_method,
            'data' => [
                'test' => true,
                'sent_at' => now()->toISOString()
            ]
        ]);

        // إرسال الإشعار
        $notification->send();

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الإشعار التجريبي بنجاح',
            'notification' => $notification
        ]);
    }

    /**
     * إحصائيات الإشعارات
     */
    public function statistics()
    {
        $userId = Auth::id();

        $stats = [
            'total_notifications' => Notification::where('user_id', $userId)->count(),
            'unread_notifications' => Notification::where('user_id', $userId)->where('is_read', false)->count(),
            'read_notifications' => Notification::where('user_id', $userId)->where('is_read', true)->count(),
            'notifications_by_type' => Notification::where('user_id', $userId)
                ->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get(),
            'recent_notifications' => Notification::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        ];

        return view('notifications.statistics', compact('stats'));
    }

    /**
     * إعدادات الإشعارات
     */
    public function settings()
    {
        $user = Auth::user();
        $types = Notification::getTypes();
        $deliveryMethods = Notification::getDeliveryMethods();

        return view('notifications.settings', compact('user', 'types', 'deliveryMethods'));
    }

    /**
     * حفظ إعدادات الإشعارات
     */
    public function updateSettings(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'notification_preferences' => 'array',
            'notification_preferences.*' => 'in:email,sms,in_app,all',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'in_app_notifications' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        
        // حفظ إعدادات الإشعارات في ملف المستخدم أو جدول منفصل
        $user->update([
            'notification_preferences' => $request->notification_preferences ?? [],
            'email_notifications' => $request->email_notifications ?? true,
            'sms_notifications' => $request->sms_notifications ?? false,
            'in_app_notifications' => $request->in_app_notifications ?? true
        ]);

        return redirect()->back()->with('success', 'تم حفظ إعدادات الإشعارات بنجاح');
    }

    /**
     * تصدير الإشعارات
     */
    public function export(Request $request)
    {
        $query = Notification::where('user_id', Auth::id());

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->where('is_read', true);
            } else {
                $query->where('is_read', false);
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->get();

        // تصدير كـ CSV
        $filename = 'notifications_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($notifications) {
            $file = fopen('php://output', 'w');
            
            // رأس الجدول
            fputcsv($file, [
                'العنوان',
                'الرسالة',
                'النوع',
                'الحالة',
                'تاريخ الإنشاء',
                'تاريخ القراءة'
            ]);

            // البيانات
            foreach ($notifications as $notification) {
                fputcsv($file, [
                    $notification->title,
                    $notification->message,
                    $notification->type_text,
                    $notification->is_read ? 'مقروء' : 'غير مقروء',
                    $notification->created_at->format('Y-m-d H:i:s'),
                    $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 

