<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskHistory;
use App\Services\TaskHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskHistoryController extends Controller
{
    protected $historyService;

    public function __construct(TaskHistoryService $historyService)
    {
        $this->historyService = $historyService;
    }

    /**
     * Display task history page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'all');
        $search = $request->get('search', '');
        
        $query = TaskHistory::with(['task', 'task.project', 'user'])
            ->where('user_id', $user->id);
        
        // Apply filters
        if ($filter !== 'all') {
            $query->where('action_type', $filter);
        }
        
        // Apply search
        if ($search) {
            $query->whereHas('task', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }
        
        $history = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $actionTypes = TaskHistory::select('action_type')
            ->distinct()
            ->pluck('action_type');
        
        return view('volunteer.task-history', compact('history', 'actionTypes', 'filter', 'search'));
    }

    /**
     * Display task timeline
     */
    public function timeline(Task $task)
    {
        // Check if user has access to this task
        if (!$this->userHasAccessToTask($task)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه المهمة');
        }
        
        $timeline = $this->historyService->getTaskTimeline($task);
        
        return view('volunteer.task-timeline', compact('task', 'timeline'));
    }

    /**
     * Display task archive
     */
    public function archive(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search', '');
        $priority = $request->get('priority', '');
        $project = $request->get('project', '');
        
        $query = Task::where('status', 'archived')
            ->whereHas('assignments', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['project', 'category', 'assignments.user']);
        
        // Apply search
        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }
        
        // Apply priority filter
        if ($priority) {
            $query->where('priority', $priority);
        }
        
        // Apply project filter
        if ($project) {
            $query->where('project_id', $project);
        }
        
        $archivedTasks = $query->orderBy('updated_at', 'desc')
            ->paginate(15);
        
        $projects = \App\Models\Project::pluck('name', 'id');
        
        return view('volunteer.task-archive', compact('archivedTasks', 'projects', 'search', 'priority', 'project'));
    }

    /**
     * Restore task from archive
     */
    public function restore(Task $task)
    {
        if (!$this->userHasAccessToTask($task)) {
            return response()->json(['error' => 'غير مصرح لك بالوصول إلى هذه المهمة'], 403);
        }
        
        if (!$task->isArchived()) {
            return response()->json(['error' => 'المهمة ليست في الأرشيف'], 400);
        }
        
        try {
            $task->restore();
            
            return response()->json([
                'success' => true,
                'message' => 'تم استعادة المهمة بنجاح',
                'task' => $task->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء استعادة المهمة'], 500);
        }
    }

    /**
     * Archive a task
     */
    public function archiveTask(Task $task)
    {
        if (!$this->userHasAccessToTask($task)) {
            return response()->json(['error' => 'غير مصرح لك بالوصول إلى هذه المهمة'], 403);
        }
        
        if ($task->isArchived()) {
            return response()->json(['error' => 'المهمة مؤرشفة بالفعل'], 400);
        }
        
        try {
            $task->archive();
            
            return response()->json([
                'success' => true,
                'message' => 'تم أرشفة المهمة بنجاح',
                'task' => $task->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء أرشفة المهمة'], 500);
        }
    }

    /**
     * Get task history as JSON (for AJAX requests)
     */
    public function getTaskHistory(Task $task)
    {
        if (!$this->userHasAccessToTask($task)) {
            return response()->json(['error' => 'غير مصرح لك بالوصول إلى هذه المهمة'], 403);
        }
        
        $history = $task->history()->with('user')->get();
        
        $formattedHistory = $history->map(function ($record) {
            return [
                'id' => $record->id,
                'action_type' => $record->action_type,
                'action_description' => $record->action_description,
                'field_name' => $record->field_name,
                'old_value' => $record->formatted_old_value,
                'new_value' => $record->formatted_new_value,
                'description' => $record->description,
                'user_name' => $record->user ? $record->user->name : 'النظام',
                'time_ago' => $record->time_ago,
                'created_at' => $record->created_at->format('Y-m-d H:i:s'),
                'icon' => $record->action_icon,
                'color' => $record->action_color,
                'metadata' => $record->metadata
            ];
        });
        
        return response()->json($formattedHistory);
    }

    /**
     * Get user's activity summary
     */
    public function activitySummary()
    {
        $user = Auth::user();
        $days = request()->get('days', 30);
        
        $summary = $this->historyService->getUserTaskHistory($user, $days);
        
        return response()->json($summary);
    }

    /**
     * Export task history
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'json');
        $taskId = $request->get('task_id');
        
        if ($taskId) {
            $task = Task::findOrFail($taskId);
            if (!$this->userHasAccessToTask($task)) {
                abort(403, 'غير مصرح لك بالوصول إلى هذه المهمة');
            }
            $history = $task->history()->with('user')->get();
        } else {
            $history = TaskHistory::where('user_id', $user->id)
                ->with(['task', 'task.project', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        $data = $history->map(function ($record) {
            return [
                'task_title' => $record->task->title,
                'action_type' => $record->action_type,
                'action_description' => $record->action_description,
                'field_name' => $record->field_name,
                'old_value' => $record->formatted_old_value,
                'new_value' => $record->formatted_new_value,
                'description' => $record->description,
                'user_name' => $record->user ? $record->user->name : 'النظام',
                'created_at' => $record->created_at->format('Y-m-d H:i:s'),
                'project_name' => $record->task->project ? $record->task->project->name : null
            ];
        });
        
        if ($format === 'csv') {
            return $this->exportToCsv($data);
        }
        
        return response()->json($data);
    }

    /**
     * Check if user has access to task
     */
    private function userHasAccessToTask(Task $task): bool
    {
        $user = Auth::user();
        
        // Check if user is assigned to the task
        $isAssigned = $task->assignments()->where('user_id', $user->id)->exists();
        
        // Check if user is the creator
        $isCreator = $task->created_by === $user->id;
        
        // Check if user is admin (you can modify this based on your admin logic)
        $isAdmin = $user->hasRole('admin') || $user->is_admin;
        
        return $isAssigned || $isCreator || $isAdmin;
    }

    /**
     * Export data to CSV
     */
    private function exportToCsv($data)
    {
        $filename = 'task_history_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Arabic text
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, [
                'عنوان المهمة',
                'نوع الإجراء',
                'وصف الإجراء',
                'اسم الحقل',
                'القيمة القديمة',
                'القيمة الجديدة',
                'الوصف',
                'اسم المستخدم',
                'التاريخ',
                'اسم المشروع'
            ]);
            
            // Add data
            foreach ($data as $row) {
                fputcsv($file, [
                    $row['task_title'],
                    $row['action_type'],
                    $row['action_description'],
                    $row['field_name'],
                    $row['old_value'],
                    $row['new_value'],
                    $row['description'],
                    $row['user_name'],
                    $row['created_at'],
                    $row['project_name']
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
} 