<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    /**
     * عرض لوحة تتبع التقدم الرئيسية
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول للوصول إلى هذه الصفحة');
        }
        
        // المهام المكلف بها المستخدم
        $userTasks = $user->assignments()
            ->with(['task.project', 'task.category'])
            ->whereHas('task', function ($query) {
                $query->where('status', '!=', 'completed')
                      ->where('status', '!=', 'cancelled');
            })
            ->get()
            ->pluck('task')
            ->filter();

        // إحصائيات التقدم
        $progressStats = $this->getProgressStats($user);
        
        // المهام المستحقة قريباً
        $upcomingDeadlines = $this->getUpcomingDeadlines($user);
        
        // المهام المتأخرة
        $overdueTasks = $this->getOverdueTasks($user);
        
        // توزيع المهام حسب الحالة
        $statusDistribution = $this->getStatusDistribution($user);
        
        // توزيع المهام حسب الأولوية
        $priorityDistribution = $this->getPriorityDistribution($user);
        
        // التقدم الأسبوعي
        $weeklyProgress = $this->getWeeklyProgress($user);
        
        // المشاريع النشطة
        $activeProjects = $this->getActiveProjects($user);

        return view('progress.index', compact(
            'userTasks',
            'progressStats',
            'upcomingDeadlines',
            'overdueTasks',
            'statusDistribution',
            'priorityDistribution',
            'weeklyProgress',
            'activeProjects'
        ));
    }

    /**
     * عرض تقويم المواعيد النهائية
     */
    public function calendar(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول للوصول إلى هذه الصفحة');
        }
        
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $showAll = $request->get('show_all', false); // إضافة خيار لعرض جميع المهام
        $projectId = $request->get('project_id', null); // فلترة حسب المشروع
        $status = $request->get('status', null); // فلترة حسب الحالة
        
        $calendarData = $this->getCalendarData($user, $month, $showAll, $projectId, $status);
        
        // الحصول على قائمة المشاريع للفلترة
        $projects = Project::whereHas('tasks')->get();
        
        return view('progress.calendar', compact('calendarData', 'month', 'showAll', 'projects', 'projectId', 'status'));
    }

    /**
     * عرض تفاصيل تقدم مشروع محدد
     */
    public function projectProgress(Project $project)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول للوصول إلى هذه الصفحة');
        }
        
        // التحقق من أن المستخدم مكلف بمهام في هذا المشروع
        $userProjectTasks = $user->assignments()
            ->with(['task'])
            ->whereHas('task', function ($query) use ($project) {
                $query->where('project_id', $project->id);
            })
            ->get()
            ->pluck('task')
            ->filter();

        if ($userProjectTasks->isEmpty()) {
            return redirect()->route('progress.index')
                ->with('error', 'لا توجد مهام مكلف بها في هذا المشروع');
        }

        $projectStats = $this->getProjectProgressStats($project, $user);
        $taskProgress = $this->getTaskProgress($project, $user);
        $timelineData = $this->getProjectTimeline($project);

        return view('progress.project', compact(
            'project',
            'userProjectTasks',
            'projectStats',
            'taskProgress',
            'timelineData'
        ));
    }

    /**
     * API للحصول على بيانات التقدم
     */
    public function getProgressData(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'يجب تسجيل الدخول للوصول إلى هذه البيانات'], 401);
        }
        
        $type = $request->get('type', 'overview');
        
        switch ($type) {
            case 'overview':
                $data = $this->getProgressStats($user);
                break;
            case 'weekly':
                $data = $this->getWeeklyProgress($user);
                break;
            case 'monthly':
                $data = $this->getMonthlyProgress($user);
                break;
            case 'deadlines':
                $data = $this->getUpcomingDeadlines($user);
                break;
            case 'calendar':
                $month = $request->get('month', Carbon::now()->format('Y-m'));
                $showAll = $request->get('show_all', false);
                $projectId = $request->get('project_id', null);
                $status = $request->get('status', null);
                $data = $this->getCalendarData($user, $month, $showAll, $projectId, $status);
                break;
            default:
                $data = [];
        }
        
        return response()->json($data);
    }

    /**
     * الحصول على إحصائيات التقدم
     */
    private function getProgressStats($user)
    {
        if (!$user) {
            return [
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'in_progress_tasks' => 0,
                'overdue_tasks' => 0,
                'completion_rate' => 0,
                'pending_tasks' => 0,
            ];
        }
        
        $totalTasks = $user->assignments()
            ->whereHas('task', function ($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->count();

        $completedTasks = $user->assignments()
            ->whereHas('task', function ($query) {
                $query->where('status', 'completed');
            })
            ->count();

        $inProgressTasks = $user->assignments()
            ->whereHas('task', function ($query) {
                $query->where('status', 'in_progress');
            })
            ->count();

        $overdueTasks = $user->assignments()
            ->whereHas('task', function ($query) {
                $query->where('status', '!=', 'completed')
                      ->where('status', '!=', 'cancelled')
                      ->where('deadline', '<', Carbon::now());
            })
            ->count();

        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'in_progress_tasks' => $inProgressTasks,
            'overdue_tasks' => $overdueTasks,
            'completion_rate' => $completionRate,
            'pending_tasks' => $totalTasks - $completedTasks - $inProgressTasks,
        ];
    }

    /**
     * الحصول على المواعيد النهائية القريبة
     */
    private function getUpcomingDeadlines($user)
    {
        return $user->assignments()
            ->with(['task.project'])
            ->whereHas('task', function ($query) {
                $query->where('status', '!=', 'completed')
                      ->where('status', '!=', 'cancelled')
                      ->where('deadline', '>=', Carbon::now())
                      ->where('deadline', '<=', Carbon::now()->addDays(7));
            })
            ->get()
            ->map(function ($assignment) {
                $daysLeft = Carbon::now()->diffInDays($assignment->task->deadline, false);
                return [
                    'task' => $assignment->task,
                    'days_left' => $daysLeft,
                    'urgency' => $daysLeft <= 1 ? 'critical' : ($daysLeft <= 3 ? 'high' : 'medium'),
                ];
            })
            ->sortBy('days_left');
    }

    /**
     * الحصول على المهام المتأخرة
     */
    private function getOverdueTasks($user)
    {
        return $user->assignments()
            ->with(['task.project'])
            ->whereHas('task', function ($query) {
                $query->where('status', '!=', 'completed')
                      ->where('status', '!=', 'cancelled')
                      ->where('deadline', '<', Carbon::now());
            })
            ->get()
            ->map(function ($assignment) {
                $daysOverdue = Carbon::now()->diffInDays($assignment->task->deadline);
                return [
                    'task' => $assignment->task,
                    'days_overdue' => $daysOverdue,
                    'severity' => $daysOverdue > 7 ? 'critical' : ($daysOverdue > 3 ? 'high' : 'medium'),
                ];
            })
            ->sortByDesc('days_overdue');
    }

    /**
     * الحصول على توزيع المهام حسب الحالة
     */
    private function getStatusDistribution($user)
    {
        return $user->assignments()
            ->whereHas('task', function ($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->get()
            ->groupBy(function ($assignment) {
                return $assignment->task->status;
            })
            ->map(function ($group) {
                return $group->count();
            });
    }

    /**
     * الحصول على توزيع المهام حسب الأولوية
     */
    private function getPriorityDistribution($user)
    {
        return $user->assignments()
            ->whereHas('task', function ($query) {
                $query->where('status', '!=', 'completed')
                      ->where('status', '!=', 'cancelled');
            })
            ->get()
            ->groupBy(function ($assignment) {
                return $assignment->task->priority;
            })
            ->map(function ($group) {
                return $group->count();
            });
    }

    /**
     * الحصول على التقدم الأسبوعي
     */
    private function getWeeklyProgress($user)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $weeklyData = [];
        
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            
            $completedOnDay = $user->assignments()
                ->whereHas('task', function ($query) use ($date) {
                    $query->where('status', 'completed')
                          ->whereDate('updated_at', $date);
                })
                ->count();
            
            $weeklyData[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'completed' => $completedOnDay,
            ];
        }
        
        return collect($weeklyData);
    }

    /**
     * الحصول على التقدم الشهري
     */
    private function getMonthlyProgress($user)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $monthlyData = [];
        
        for ($i = 0; $i < $endOfMonth->day; $i++) {
            $date = $startOfMonth->copy()->addDays($i);
            
            $completedOnDay = $user->assignments()
                ->whereHas('task', function ($query) use ($date) {
                    $query->where('status', 'completed')
                          ->whereDate('updated_at', $date);
                })
                ->count();
            
            $monthlyData[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->day,
                'completed' => $completedOnDay,
            ];
        }
        
        return collect($monthlyData);
    }

    /**
     * الحصول على المشاريع النشطة
     */
    private function getActiveProjects($user)
    {
        return $user->assignments()
            ->with(['task.project'])
            ->whereHas('task', function ($query) {
                $query->where('status', '!=', 'completed')
                      ->where('status', '!=', 'cancelled');
            })
            ->get()
            ->groupBy(function ($assignment) {
                return $assignment->task->project_id;
            })
            ->map(function ($assignments, $projectId) {
                $project = $assignments->first()->task->project;
                $totalTasks = $assignments->count();
                $completedTasks = $assignments->where('task.status', 'completed')->count();
                
                return [
                    'project' => $project,
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('completion_rate');
    }

    /**
     * الحصول على بيانات التقويم
     */
    private function getCalendarData($user, $month, $showAll = false, $projectId = null, $status = null)
    {
        if (!$user) {
            return collect([]);
        }
        
        $date = Carbon::createFromFormat('Y-m', $month);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        // إنشاء query للمهام العادية
        if ($showAll) {
            // عرض جميع المهام
            $tasksQuery = Task::with(['project', 'category', 'assignments.user', 'parentTask'])
                ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('deadline', [$startOfMonth, $endOfMonth])
                          ->orWhereBetween('start_date', [$startOfMonth, $endOfMonth]);
                });
        } else {
            // عرض المهام المكلف بها المستخدم فقط
            $tasksQuery = Task::with(['project', 'category', 'assignments.user', 'parentTask'])
                ->whereHas('assignments', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('deadline', [$startOfMonth, $endOfMonth])
                          ->orWhereBetween('start_date', [$startOfMonth, $endOfMonth]);
                });
        }
        
        // فلترة حسب المشروع
        if ($projectId) {
            $tasksQuery->where('project_id', $projectId);
        }
        
        // فلترة حسب الحالة
        if ($status) {
            $tasksQuery->where('status', $status);
        }
        
        $tasks = $tasksQuery->get();
        
        $calendarData = [];
        
        // إنشاء نطاق التاريخ للتقويم (من بداية الأسبوع إلى نهاية الأسبوع)
        $startOfWeek = $startOfMonth->copy()->startOfWeek();
        $endOfWeek = $endOfMonth->copy()->endOfWeek();
        $currentDate = $startOfWeek->copy();
        
        while ($currentDate <= $endOfWeek) {
            // المهام التي تنتهي في هذا اليوم
            $deadlineTasks = $tasks->filter(function ($task) use ($currentDate) {
                return $task->deadline && $task->deadline->format('Y-m-d') === $currentDate->format('Y-m-d');
            });
            
            // المهام التي تبدأ في هذا اليوم
            $startingTasks = $tasks->filter(function ($task) use ($currentDate) {
                return $task->start_date && $task->start_date->format('Y-m-d') === $currentDate->format('Y-m-d') && 
                       (!$task->deadline || $task->deadline->format('Y-m-d') !== $currentDate->format('Y-m-d'));
            });
            
            // دمج المهام
            $allDayTasks = $deadlineTasks->merge($startingTasks)->unique('id');
            
            $calendarData[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day' => $currentDate->day,
                'is_current_month' => $currentDate->month === $startOfMonth->month,
                'is_today' => $currentDate->isToday(),
                'tasks' => $allDayTasks->map(function ($task) use ($currentDate) {
                    $isDeadline = $task->deadline && $task->deadline->format('Y-m-d') === $currentDate->format('Y-m-d');
                    $isStarting = $task->start_date && $task->start_date->format('Y-m-d') === $currentDate->format('Y-m-d');
                    
                    return [
                        'task' => $task,
                        'is_overdue' => $task->deadline < Carbon::now() && $task->status !== 'completed',
                        'is_deadline' => $isDeadline,
                        'is_starting' => $isStarting,
                        'is_recurring_instance' => $task->is_recurring_instance,
                        'parent_task_title' => $task->parentTask ? $task->parentTask->title : null,
                        'assignees' => $task->assignments->pluck('user.name')->implode(', '),
                        'event_type' => $isDeadline ? 'deadline' : 'start',
                    ];
                }),
            ];
            
            $currentDate->addDay();
        }
        
        return collect($calendarData);
    }

    /**
     * الحصول على إحصائيات تقدم المشروع
     */
    private function getProjectProgressStats($project, $user)
    {
        $userTasks = $user->assignments()
            ->whereHas('task', function ($query) use ($project) {
                $query->where('project_id', $project->id);
            })
            ->get()
            ->pluck('task')
            ->filter();

        $totalTasks = $userTasks->count();
        $completedTasks = $userTasks->where('status', 'completed')->count();
        $inProgressTasks = $userTasks->where('status', 'in_progress')->count();
        $overdueTasks = $userTasks->where('deadline', '<', Carbon::now())
            ->where('status', '!=', 'completed')
            ->count();

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'in_progress_tasks' => $inProgressTasks,
            'overdue_tasks' => $overdueTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
        ];
    }

    /**
     * الحصول على تقدم المهام في المشروع
     */
    private function getTaskProgress($project, $user)
    {
        return $user->assignments()
            ->with(['task'])
            ->whereHas('task', function ($query) use ($project) {
                $query->where('project_id', $project->id);
            })
            ->get()
            ->map(function ($assignment) {
                $task = $assignment->task;
                $daysToDeadline = $task->deadline ? Carbon::now()->diffInDays($task->deadline, false) : null;
                $isOverdue = $task->deadline && $task->deadline < Carbon::now() && $task->status !== 'completed';
                
                return [
                    'task' => $task,
                    'days_to_deadline' => $daysToDeadline,
                    'is_overdue' => $isOverdue,
                    'progress_percentage' => $this->calculateTaskProgress($task),
                ];
            })
            ->sortBy('days_to_deadline');
    }

    /**
     * حساب نسبة تقدم المهمة
     */
    private function calculateTaskProgress($task)
    {
        $statusProgress = [
            'new' => 0,
            'in_progress' => 50,
            'pending' => 25,
            'completed' => 100,
            'cancelled' => 0,
        ];
        
        return $statusProgress[$task->status] ?? 0;
    }

    /**
     * الحصول على الجدول الزمني للمشروع
     */
    private function getProjectTimeline($project)
    {
        $tasks = $project->tasks()
            ->with(['assignments.user'])
            ->orderBy('deadline')
            ->get();

        return $tasks->map(function ($task) {
            return [
                'task' => $task,
                'start_date' => $task->start_date,
                'deadline' => $task->deadline,
                'status' => $task->status,
                'assignees' => $task->assignments ? $task->assignments->pluck('user.name')->implode(', ') : '',
            ];
        });
    }
} 