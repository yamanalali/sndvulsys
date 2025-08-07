<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        // إحصائيات عامة
        $generalStats = $this->getGeneralStats($userId);
        
        // معدل الإنجاز
        $completionRates = $this->getCompletionRates($userId);
        
        // تحليل الاتجاهات
        $trends = $this->getTrends($userId);
        
        // مقاييس الكفاءة
        $efficiencyMetrics = $this->getEfficiencyMetrics($userId);
        
        // أداء المشاريع
        $projectPerformance = $this->getProjectPerformance($userId);
        
        // أداء الفريق
        $teamPerformance = $this->getTeamPerformance($userId);
        
        return view('analytics.index', compact(
            'generalStats',
            'completionRates',
            'trends',
            'efficiencyMetrics',
            'projectPerformance',
            'teamPerformance'
        ));
    }

    private function getGeneralStats($userId)
    {
        // المهام الإجمالية
        $totalTasks = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        // المهام المكتملة
        $completedTasks = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('status', 'completed')->count();

        // المهام المتأخرة
        $overdueTasks = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('deadline', '<', now())
          ->where('status', '!=', 'completed')
          ->count();

        // المهام قيد التنفيذ
        $inProgressTasks = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('status', 'in_progress')->count();

        // معدل الإنجاز العام
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'overdue_tasks' => $overdueTasks,
            'in_progress_tasks' => $inProgressTasks,
            'completion_rate' => $completionRate,
        ];
    }

    private function getCompletionRates($userId)
    {
        // معدل الإنجاز الشهري
        $monthlyCompletion = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->selectRaw('MONTH(completed_at) as month, COUNT(*) as completed_count')
        ->whereNotNull('completed_at')
        ->whereYear('completed_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // معدل الإنجاز حسب المشروع
        $projectCompletion = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with('project')
        ->selectRaw('project_id, COUNT(*) as total_tasks, SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_tasks')
        ->groupBy('project_id')
        ->get()
        ->map(function($item) {
            $item->completion_rate = $item->total_tasks > 0 ? round(($item->completed_tasks / $item->total_tasks) * 100, 2) : 0;
            return $item;
        });

        // معدل الإنجاز حسب الأولوية
        $priorityCompletion = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->selectRaw('priority, COUNT(*) as total_tasks, SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_tasks')
        ->groupBy('priority')
        ->get()
        ->map(function($item) {
            $item->completion_rate = $item->total_tasks > 0 ? round(($item->completed_tasks / $item->total_tasks) * 100, 2) : 0;
            return $item;
        });

        return [
            'monthly' => $monthlyCompletion,
            'by_project' => $projectCompletion,
            'by_priority' => $priorityCompletion,
        ];
    }

    private function getTrends($userId)
    {
        // اتجاه المهام المكتملة خلال 6 أشهر
        $completionTrend = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereNotNull('completed_at')
        ->where('completed_at', '>=', now()->subMonths(6))
        ->selectRaw('DATE(completed_at) as date, COUNT(*) as count')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // اتجاه المهام الجديدة
        $newTasksTrend = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('created_at', '>=', now()->subMonths(6))
        ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // متوسط وقت الإنجاز
        $avgCompletionTime = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereNotNull('completed_at')
        ->whereNotNull('created_at')
        ->selectRaw('AVG(DATEDIFF(completed_at, created_at)) as avg_days')
        ->first();

        return [
            'completion_trend' => $completionTrend,
            'new_tasks_trend' => $newTasksTrend,
            'avg_completion_time' => round($avgCompletionTime->avg_days ?? 0, 1),
        ];
    }

    private function getEfficiencyMetrics($userId)
    {
        // كفاءة الإنجاز في الوقت المحدد
        $onTimeCompletion = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('status', 'completed')
        ->whereNotNull('deadline')
        ->where('completed_at', '<=', DB::raw('deadline'))
        ->count();

        $totalCompletedWithDeadline = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('status', 'completed')
        ->whereNotNull('deadline')
        ->count();

        $onTimeRate = $totalCompletedWithDeadline > 0 ? round(($onTimeCompletion / $totalCompletedWithDeadline) * 100, 2) : 0;

        // متوسط التأخير
        $avgDelay = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('status', 'completed')
        ->whereNotNull('deadline')
        ->where('completed_at', '>', DB::raw('deadline'))
        ->selectRaw('AVG(DATEDIFF(completed_at, deadline)) as avg_delay')
        ->first();

        // معدل الإنتاجية (مهام في اليوم)
        $tasksPerDay = Task::whereHas('assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereNotNull('completed_at')
        ->where('completed_at', '>=', now()->subDays(30))
        ->count() / 30;

        return [
            'on_time_rate' => $onTimeRate,
            'avg_delay_days' => round($avgDelay->avg_delay ?? 0, 1),
            'tasks_per_day' => round($tasksPerDay, 2),
            'on_time_completed' => $onTimeCompletion,
            'total_with_deadline' => $totalCompletedWithDeadline,
        ];
    }

    private function getProjectPerformance($userId)
    {
        return Project::where(function($query) use ($userId) {
            $query->where('manager_id', $userId)
                  ->orWhereHas('teamMembers', function($q) use ($userId) {
                      $q->where('user_id', $userId);
                  });
        })
        ->with(['tasks' => function($query) use ($userId) {
            $query->whereHas('assignments', function($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }])
        ->get()
        ->map(function($project) {
            $totalTasks = $project->tasks->count();
            $completedTasks = $project->tasks->where('status', 'completed')->count();
            $overdueTasks = $project->tasks->where('deadline', '<', now())
                                         ->where('status', '!=', 'completed')
                                         ->count();
            
            return [
                'id' => $project->id,
                'name' => $project->name,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'overdue_tasks' => $overdueTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
                'status' => $project->status,
            ];
        });
    }

    private function getTeamPerformance($userId)
    {
        // أداء أعضاء الفريق في المشاريع المشتركة
        $teamMembers = User::whereHas('assignments.task.project', function($query) use ($userId) {
            $query->where(function($q) use ($userId) {
                $q->where('manager_id', $userId)
                  ->orWhereHas('teamMembers', function($teamQ) use ($userId) {
                      $teamQ->where('user_id', $userId);
                  });
            });
        })
        ->with(['assignments' => function($query) use ($userId) {
            $query->whereHas('task.project', function($q) use ($userId) {
                $q->where(function($projectQ) use ($userId) {
                    $projectQ->where('manager_id', $userId)
                             ->orWhereHas('teamMembers', function($teamQ) use ($userId) {
                                 $teamQ->where('user_id', $userId);
                             });
                });
            });
        }])
        ->get()
        ->map(function($user) {
            $totalTasks = $user->assignments->count();
            $completedTasks = $user->assignments->where('task.status', 'completed')->count();
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
            ];
        })
        ->sortByDesc('completion_rate');

        return $teamMembers;
    }

    public function reports()
    {
        return view('analytics.reports');
    }

    public function efficiency()
    {
        return view('analytics.efficiency');
    }

    public function export(Request $request)
    {
        $userId = auth()->id();
        $type = $request->get('type', 'general');
        $timeRange = $request->get('time_range', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // تطبيق الفلاتر الزمنية
        $this->applyTimeFilters($timeRange, $startDate, $endDate);
        
        switch ($type) {
            case 'completion_rates':
            case 'completion':
                $data = $this->getCompletionRates($userId);
                break;
            case 'trends':
                $data = $this->getTrends($userId);
                break;
            case 'efficiency':
                $data = $this->getEfficiencyMetrics($userId);
                break;
            case 'projects':
                $data = $this->getProjectPerformance($userId);
                break;
            case 'team':
                $data = $this->getTeamPerformance($userId);
                break;
            case 'performance':
            default:
                $data = $this->getGeneralStats($userId);
        }

        return response()->json($data);
    }

    private function applyTimeFilters($timeRange, $startDate, $endDate)
    {
        // تطبيق الفلاتر الزمنية على الاستعلامات
        if ($timeRange !== 'all') {
            $days = (int) $timeRange;
            $this->timeFilter = now()->subDays($days);
        } elseif ($startDate && $endDate) {
            $this->timeFilter = [
                'start' => $startDate,
                'end' => $endDate
            ];
        }
    }
}
