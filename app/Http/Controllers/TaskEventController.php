<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskEventController extends Controller
{
    /**
     * عرض سجل أحداث المهام
     */
    public function index(Request $request)
    {
        $query = Task::with(['project', 'assignments.user', 'category', 'taskDependencies.prerequisiteTask']);

        // فلترة حسب المشروع
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $tasks = $query->orderByDesc('updated_at')->paginate(20);
        $projects = \App\Models\Project::all();

        // إحصائيات الأحداث
        $stats = $this->getEventStats();

        return view('task-events.index', compact('tasks', 'projects', 'stats'));
    }

    /**
     * عرض تفاصيل أحداث مهمة محددة
     */
    public function show(Task $task)
    {
        $task->load(['project', 'assignments.user', 'category', 'taskDependencies.prerequisiteTask', 'dependents.dependentTask']);

        // الحصول على سجل التحديثات
        $updates = $this->getTaskUpdates($task);

        return view('task-events.show', compact('task', 'updates'));
    }

    /**
     * الحصول على إحصائيات الأحداث
     */
    private function getEventStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_tasks' => Task::count(),
            'completed_today' => Task::where('status', 'completed')
                ->whereDate('updated_at', $today)->count(),
            'overdue_tasks' => Task::where('status', '!=', 'completed')
                ->where('status', '!=', 'cancelled')
                ->where('deadline', '<', now())->count(),
            'due_this_week' => Task::where('status', '!=', 'completed')
                ->where('status', '!=', 'cancelled')
                ->whereBetween('deadline', [$thisWeek, $thisWeek->copy()->endOfWeek()])->count(),
            'completed_this_month' => Task::where('status', 'completed')
                ->whereMonth('updated_at', $thisMonth->month)->count(),
        ];
    }

    /**
     * الحصول على سجل تحديثات المهمة
     */
    private function getTaskUpdates(Task $task)
    {
        // يمكن إضافة جدول منفصل لتتبع التحديثات في المستقبل
        // حالياً نستخدم معلومات المهمة الأساسية
        return [
            'created_at' => $task->created_at,
            'updated_at' => $task->updated_at,
            'status_changes' => [
                [
                    'status' => $task->status,
                    'updated_at' => $task->updated_at,
                    'updated_by' => 'System', // يمكن إضافة معلومات المستخدم في المستقبل
                ]
            ],
            'assignments' => $task->assignments->map(function ($assignment) {
                return [
                    'user' => $assignment->user->name,
                    'assigned_at' => $assignment->assigned_at,
                    'status' => $assignment->status,
                ];
            }),
        ];
    }

    /**
     * تصدير سجل الأحداث
     */
    public function export(Request $request)
    {
        $query = Task::with(['project', 'assignments.user', 'category', 'taskDependencies.prerequisiteTask']);

        // تطبيق نفس الفلاتر
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $tasks = $query->get();

        $filename = 'task_events_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->json([
            'tasks' => $tasks,
            'exported_at' => now()->toISOString(),
            'total_tasks' => $tasks->count(),
        ])->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
} 