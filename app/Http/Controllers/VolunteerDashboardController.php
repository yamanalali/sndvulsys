<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Assignment;
use App\Models\Project;
use App\Models\VolunteerRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VolunteerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * عرض لوحة تحكم المتطوع الرئيسية
     */
    public function index()
    {
        $user = Auth::user();
        
        // المهام المخصصة للمتطوع
        $assignedTasks = Task::whereHas('assignments', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['project', 'assignments'])->get();

        // المهام القادمة (خلال الأسبوع القادم)
        $upcomingTasks = $assignedTasks->filter(function($task) {
            return $task->deadline && $task->deadline->isBetween(
                Carbon::now(), 
                Carbon::now()->addWeek()
            );
        });

        // المهام المتأخرة
        $overdueTasks = $assignedTasks->filter(function($task) {
            return $task->deadline && $task->deadline->isPast() && $task->status !== 'completed';
        });

        // المهام المكتملة هذا الشهر
        $completedThisMonth = $assignedTasks->filter(function($task) {
            return $task->status === 'completed' && 
                   $task->updated_at->isCurrentMonth();
        });

        // Get recent activities count
        $recentActivities = \App\Models\TaskHistory::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // إحصائيات سريعة
        $stats = [
            'total_tasks' => $assignedTasks->count(),
            'completed_tasks' => $assignedTasks->where('status', 'completed')->count(),
            'pending_tasks' => $assignedTasks->where('status', 'pending')->count(),
            'in_progress_tasks' => $assignedTasks->where('status', 'in_progress')->count(),
            'overdue_tasks' => $overdueTasks->count(),
            'upcoming_tasks' => $upcomingTasks->count(),
            'completion_rate' => $assignedTasks->count() > 0 ? 
                round(($assignedTasks->where('status', 'completed')->count() / $assignedTasks->count()) * 100, 1) : 0
        ];

        return view('volunteer.dashboard', compact(
            'assignedTasks', 
            'upcomingTasks', 
            'overdueTasks', 
            'completedThisMonth', 
            'stats',
            'recentActivities'
        ));
    }

    /**
     * عرض التقويم التفاعلي للمتطوع
     */
    public function calendar(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        
        // جلب المهام للمتطوع في الشهر المحدد
        $tasks = Task::whereHas('assignments', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where(function($query) use ($month) {
            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth = Carbon::parse($month)->endOfMonth();
            $query->whereBetween('deadline', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('created_at', [$startOfMonth, $endOfMonth]);
        })->with(['project', 'assignments'])->get();

        // تنظيم المهام حسب التاريخ
        $calendarData = [];
        foreach ($tasks as $task) {
            $date = $task->deadline ? $task->deadline->format('Y-m-d') : $task->created_at->format('Y-m-d');
            if (!isset($calendarData[$date])) {
                $calendarData[$date] = [];
            }
            $calendarData[$date][] = $task;
        }

        return view('volunteer.calendar', compact('calendarData', 'month', 'tasks'));
    }

    /**
     * عرض المهام القادمة
     */
    public function upcomingTasks()
    {
        $user = Auth::user();
        
        $upcomingTasks = Task::whereHas('assignments', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', '!=', 'completed')
          ->where('deadline', '>=', Carbon::now())
          ->orderBy('deadline')
          ->with(['project', 'assignments'])
          ->paginate(10);

        return view('volunteer.upcoming-tasks', compact('upcomingTasks'));
    }

    /**
     * عرض الإحصائيات الشخصية
     */
    public function statistics()
    {
        $user = Auth::user();
        
        // إحصائيات المهام
        $taskStats = $this->getTaskStatistics($user);
        
        // إحصائيات الأداء الشهري
        $monthlyStats = $this->getMonthlyStatistics($user);
        
        // إحصائيات المشاريع
        $projectStats = $this->getProjectStatistics($user);
        
        // إحصائيات الوقت
        $timeStats = $this->getTimeStatistics($user);

        return view('volunteer.statistics', compact(
            'taskStats', 
            'monthlyStats', 
            'projectStats', 
            'timeStats'
        ));
    }

    /**
     * جلب إحصائيات المهام
     */
    private function getTaskStatistics($user)
    {
        $assignedTasks = Task::whereHas('assignments', function($query) use ($user) {
            $query->where('user_id', $user->id);
        });

        return [
            'total' => $assignedTasks->count(),
            'completed' => $assignedTasks->where('status', 'completed')->count(),
            'pending' => $assignedTasks->where('status', 'pending')->count(),
            'in_progress' => $assignedTasks->where('status', 'in_progress')->count(),
            'overdue' => $assignedTasks->where('deadline', '<', Carbon::now())
                                     ->where('status', '!=', 'completed')->count(),
            'completion_rate' => $assignedTasks->count() > 0 ? 
                round(($assignedTasks->where('status', 'completed')->count() / $assignedTasks->count()) * 100, 1) : 0
        ];
    }

    /**
     * جلب الإحصائيات الشهرية
     */
    private function getMonthlyStatistics($user)
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $completedTasks = Task::whereHas('assignments', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'completed')
              ->whereBetween('updated_at', [$monthStart, $monthEnd])
              ->count();

            $months[] = [
                'month' => $month->format('M Y'),
                'completed' => $completedTasks
            ];
        }
        
        return $months;
    }

    /**
     * جلب إحصائيات المشاريع
     */
    private function getProjectStatistics($user)
    {
        return Project::whereHas('tasks.assignments', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->withCount(['tasks' => function($query) use ($user) {
            $query->whereHas('assignments', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }])->get();
    }

    /**
     * جلب إحصائيات الوقت
     */
    private function getTimeStatistics($user)
    {
        $assignedTasks = Task::whereHas('assignments', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'completed');

        $avgCompletionTime = $assignedTasks->get()->avg(function($task) {
            if ($task->created_at && $task->updated_at) {
                return $task->created_at->diffInDays($task->updated_at);
            }
            return 0;
        });

        return [
            'avg_completion_days' => round($avgCompletionTime, 1),
            'tasks_completed_today' => $assignedTasks->whereDate('updated_at', Carbon::today())->count(),
            'tasks_completed_this_week' => $assignedTasks->whereBetween('updated_at', [
                Carbon::now()->startOfWeek(), 
                Carbon::now()->endOfWeek()
            ])->count(),
            'tasks_completed_this_month' => $assignedTasks->whereMonth('updated_at', Carbon::now()->month)->count()
        ];
    }
} 