@extends('layouts.master')

@section('title', 'تقويم المواعيد النهائية')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-warning bg-opacity-10 rounded p-3">
                                <i data-feather="calendar" class="text-warning" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h1 class="h3 mb-1 text-dark">تقويم المواعيد النهائية</h1>
                                <p class="text-muted mb-0">مراقبة المواعيد النهائية للمهام</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('progress.index') }}" class="btn btn-outline-primary">
                                <i data-feather="trending-up" class="me-2" style="width: 16px; height: 16px;"></i>
                                تتبع التقدم
                            </a>
                            <a href="{{ route('progress.calendar') }}?month={{ \Carbon\Carbon::now()->format('Y-m') }}" class="btn btn-outline-info">
                                <i data-feather="calendar" class="me-2" style="width: 16px; height: 16px;"></i>
                                الشهر الحالي
                            </a>
                            <button type="button" class="btn btn-outline-secondary" onclick="refreshCalendar()" title="تحديث البيانات">
                                <i data-feather="refresh-cw" style="width: 16px; height: 16px;"></i>
                            </button>
                            <div class="btn-group" role="group">
                                <a href="?month={{ \Carbon\Carbon::parse($month)->subMonth()->format('Y-m') }}&show_all={{ $showAll ? '1' : '0' }}&project_id={{ $projectId }}&status={{ $status }}" class="btn btn-outline-secondary">
                                    <i data-feather="chevron-left" style="width: 16px; height: 16px;"></i>
                                </a>
                                <button class="btn btn-outline-secondary" disabled>
                                    {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                                </button>
                                <a href="?month={{ \Carbon\Carbon::parse($month)->addMonth()->format('Y-m') }}&show_all={{ $showAll ? '1' : '0' }}&project_id={{ $projectId }}&status={{ $status }}" class="btn btn-outline-secondary">
                                    <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Options -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('progress.calendar') }}" class="row align-items-end">
                        <input type="hidden" name="month" value="{{ $month }}">
                        
                        <div class="col-md-3">
                            <label class="form-label">عرض المهام</label>
                            <select name="show_all" class="form-select">
                                <option value="0" {{ !$showAll ? 'selected' : '' }}>المهام المكلف بها فقط</option>
                                <option value="1" {{ $showAll ? 'selected' : '' }}>جميع المهام</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">المشروع</label>
                            <select name="project_id" class="form-select">
                                <option value="">جميع المشاريع</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">جميع الحالات</option>
                                <option value="new" {{ $status == 'new' ? 'selected' : '' }}>جديدة</option>
                                <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>معلقة</option>
                                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="filter" style="width: 16px; height: 16px;"></i>
                                    تطبيق الفلترة
                                </button>
                                <a href="{{ route('progress.calendar') }}?month={{ $month }}" class="btn btn-outline-secondary">
                                    <i data-feather="x" style="width: 16px; height: 16px;"></i>
                                    مسح
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Calendar Legend -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-success rounded" style="width: 20px; height: 20px;"></div>
                                <span class="text-muted">مكتملة</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary rounded" style="width: 20px; height: 20px;"></div>
                                <span class="text-muted">قيد التنفيذ</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-warning rounded" style="width: 20px; height: 20px;"></div>
                                <span class="text-muted">معلقة</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-danger rounded" style="width: 20px; height: 20px;"></div>
                                <span class="text-muted">متأخرة</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-info rounded-circle border border-info" style="width: 20px; height: 20px;"></div>
                                <span class="text-muted">متكررة</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-secondary border-left border-success" style="width: 20px; height: 20px; border-left: 3px solid #28a745 !important;"></div>
                                <span class="text-muted">تبدأ اليوم</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="calendar-container">
                        <!-- Calendar Header -->
                        <div class="calendar-header mb-3">
                            <div class="row text-center">
                                <div class="col">الأحد</div>
                                <div class="col">الاثنين</div>
                                <div class="col">الثلاثاء</div>
                                <div class="col">الأربعاء</div>
                                <div class="col">الخميس</div>
                                <div class="col">الجمعة</div>
                                <div class="col">السبت</div>
                            </div>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="calendar-grid">
                            @php
                                $startOfMonth = \Carbon\Carbon::parse($month)->startOfMonth();
                                $endOfMonth = \Carbon\Carbon::parse($month)->endOfMonth();
                                $startOfWeek = $startOfMonth->copy()->startOfWeek();
                                $endOfWeek = $endOfMonth->copy()->endOfWeek();
                                $currentDate = $startOfWeek->copy();
                            @endphp

                            @while($currentDate <= $endOfWeek)
                                <div class="calendar-week">
                                    @for($i = 0; $i < 7; $i++)
                                        @php
                                            $dayTasks = $calendarData->where('date', $currentDate->format('Y-m-d'))->first();
                                            $isCurrentMonth = $dayTasks ? $dayTasks['is_current_month'] : ($currentDate->month == $startOfMonth->month);
                                            $isToday = $dayTasks ? $dayTasks['is_today'] : $currentDate->isToday();
                                            $isPast = $currentDate->isPast();
                                        @endphp
                                        
                                        <div class="calendar-day {{ $isCurrentMonth ? '' : 'other-month' }} {{ $isToday ? 'today' : '' }} {{ $isPast ? 'past' : '' }}">
                                            <div class="day-header">
                                                <span class="day-number">{{ $currentDate->day }}</span>
                                                @if($isToday)
                                                    <span class="today-indicator"></span>
                                                @endif
                                            </div>
                                            
                                            @if($dayTasks && $dayTasks['tasks']->count() > 0)
                                                <div class="day-tasks">
                                                    @foreach($dayTasks['tasks']->take(3) as $taskData)
                                                        @php
                                                            $task = $taskData['task'];
                                                            $isOverdue = $taskData['is_overdue'];
                                                            $isRecurring = $taskData['is_recurring_instance'] ?? false;
                                                            $isDeadline = $taskData['is_deadline'] ?? true;
                                                            $isStarting = $taskData['is_starting'] ?? false;
                                                            $assignees = $taskData['assignees'] ?? '';
                                                            
                                                            $statusColors = [
                                                                'completed' => 'success',
                                                                'in_progress' => 'primary',
                                                                'pending' => 'warning',
                                                                'new' => 'secondary',
                                                                'cancelled' => 'dark'
                                                            ];
                                                            $statusColor = $statusColors[$task->status] ?? 'secondary';
                                                            
                                                            // تحديد نوع الحدث
                                                            $eventIcon = 'check';
                                                            if ($isOverdue) {
                                                                $eventIcon = 'alert-triangle';
                                                            } elseif ($isStarting) {
                                                                $eventIcon = 'play';
                                                            } elseif ($isRecurring) {
                                                                $eventIcon = 'repeat';
                                                            }
                                                            
                                                            // إنشاء النص الوصفي
                                                            $tooltipText = $task->title;
                                                            if ($task->project) {
                                                                $tooltipText .= ' - ' . $task->project->name;
                                                            }
                                                            if ($assignees) {
                                                                $tooltipText .= ' - المكلف: ' . $assignees;
                                                            }
                                                            if ($isRecurring) {
                                                                $tooltipText .= ' (مهمة متكررة)';
                                                            }
                                                            if ($isStarting) {
                                                                $tooltipText .= ' - تبدأ اليوم';
                                                            }
                                                        @endphp
                                                        
                                                        <div class="task-indicator {{ $isOverdue ? 'overdue' : '' }} {{ $isRecurring ? 'recurring' : '' }} {{ $isStarting ? 'starting' : '' }} bg-{{ $statusColor }}" 
                                             data-bs-toggle="tooltip" 
                                             data-bs-placement="top" 
                                             data-task-id="{{ $task->id }}"
                                             title="{{ $tooltipText }}">
                                            <i data-feather="{{ $eventIcon }}" style="width: 10px; height: 10px;"></i>
                                        </div>
                                                    @endforeach
                                                    
                                                    @if($dayTasks['tasks']->count() > 3)
                                                        <div class="more-tasks">
                                                            +{{ $dayTasks['tasks']->count() - 3 }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @php
                                            $currentDate->addDay();
                                        @endphp
                                    @endfor
                                </div>
                            @endwhile
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Deadlines Summary -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                                <i data-feather="clock" class="text-warning" style="width: 20px; height: 20px;"></i>
                                المواعيد القريبة (7 أيام)
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $upcomingTasks = collect();
                                foreach($calendarData as $dayData) {
                                    foreach($dayData['tasks'] as $taskData) {
                                        $daysLeft = \Carbon\Carbon::now()->diffInDays($taskData['task']->deadline, false);
                                        if($daysLeft >= 0 && $daysLeft <= 7) {
                                            $upcomingTasks->push([
                                                'task' => $taskData['task'],
                                                'days_left' => $daysLeft,
                                                'is_overdue' => $taskData['is_overdue']
                                            ]);
                                        }
                                    }
                                }
                                $upcomingTasks = $upcomingTasks->sortBy('days_left');
                            @endphp
                            
                            @if($upcomingTasks->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($upcomingTasks->take(5) as $taskData)
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $taskData['task']->title }}</h6>
                                                    <p class="text-muted small mb-0">
                                                        {{ $taskData['task']->project ? $taskData['task']->project->name : 'بدون مشروع' }}
                                                    </p>
                                                </div>
                                                <div class="text-end">
                                                    @if($taskData['is_overdue'])
                                                        <span class="badge bg-danger">متأخرة</span>
                                                    @else
                                                        <span class="badge bg-{{ $taskData['days_left'] <= 1 ? 'danger' : ($taskData['days_left'] <= 3 ? 'warning' : 'info') }}">
                                                            {{ $taskData['days_left'] }} يوم
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i data-feather="check-circle" class="text-success mb-3" style="width: 48px; height: 48px;"></i>
                                    <h6 class="text-muted">لا توجد مواعيد نهائية قريبة</h6>
                                    <p class="text-muted small">جميع المهام محدثة ومكتملة في الوقت المحدد</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                                <i data-feather="alert-triangle" class="text-danger" style="width: 20px; height: 20px;"></i>
                                المهام المتأخرة
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $overdueTasks = collect();
                                foreach($calendarData as $dayData) {
                                    foreach($dayData['tasks'] as $taskData) {
                                        if($taskData['is_overdue']) {
                                            $daysOverdue = \Carbon\Carbon::now()->diffInDays($taskData['task']->deadline);
                                            $overdueTasks->push([
                                                'task' => $taskData['task'],
                                                'days_overdue' => $daysOverdue
                                            ]);
                                        }
                                    }
                                }
                                $overdueTasks = $overdueTasks->sortByDesc('days_overdue');
                            @endphp
                            
                            @if($overdueTasks->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($overdueTasks->take(5) as $taskData)
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $taskData['task']->title }}</h6>
                                                    <p class="text-muted small mb-0">
                                                        {{ $taskData['task']->project ? $taskData['task']->project->name : 'بدون مشروع' }}
                                                    </p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $taskData['days_overdue'] > 7 ? 'danger' : ($taskData['days_overdue'] > 3 ? 'warning' : 'secondary') }}">
                                                        متأخرة {{ $taskData['days_overdue'] }} يوم
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i data-feather="check-circle" class="text-success mb-3" style="width: 48px; height: 48px;"></i>
                                    <h6 class="text-muted">لا توجد مهام متأخرة</h6>
                                    <p class="text-muted small">جميع المهام محدثة ومكتملة في الوقت المحدد</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Details Modal -->
<div class="modal fade" id="taskDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل المهمة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="taskDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <a href="#" class="btn btn-primary" id="viewTaskLink">عرض المهمة</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.calendar-container {
    max-width: 100%;
    overflow-x: auto;
}

.calendar-header {
    font-weight: 600;
    color: #6c757d;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 10px;
}

.calendar-grid {
    display: flex;
    flex-direction: column;
}

.calendar-week {
    display: flex;
    min-height: 120px;
}

.calendar-day {
    flex: 1;
    border: 1px solid #dee2e6;
    padding: 8px;
    position: relative;
    background: #fff;
    transition: all 0.3s ease;
}

.calendar-day:hover {
    background: #f8f9fa;
    transform: scale(1.02);
    z-index: 1;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.calendar-day.other-month {
    background: #f8f9fa;
    color: #adb5bd;
}

.calendar-day.today {
    background: #e3f2fd;
    border-color: #2196f3;
}

.calendar-day.today .today-indicator {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 8px;
    height: 8px;
    background: #2196f3;
    border-radius: 50%;
}

.calendar-day.past {
    background: #f8f9fa;
}

.day-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.day-number {
    font-weight: 600;
    font-size: 14px;
}

.day-tasks {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.task-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 20px;
    border-radius: 3px;
    font-size: 10px;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
}

.task-indicator:hover {
    transform: scale(1.05);
}

.task-indicator.overdue {
    background: #dc3545 !important;
    animation: pulse 2s infinite;
}

.task-indicator.recurring {
    border: 2px solid #17a2b8;
    border-radius: 50% !important;
}

.task-indicator.starting {
    border-left: 3px solid #28a745;
    border-radius: 3px 0 0 3px;
}

.task-indicator.recurring.starting {
    border: 2px solid #28a745;
    border-left: 4px solid #28a745;
}

.more-tasks {
    font-size: 10px;
    color: #6c757d;
    text-align: center;
    padding: 2px;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

@media (max-width: 768px) {
    .calendar-day {
        min-height: 80px;
        padding: 4px;
    }
    
    .day-number {
        font-size: 12px;
    }
    
    .task-indicator {
        height: 16px;
        font-size: 8px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Task indicator click handler
document.addEventListener('click', function(e) {
    if (e.target.closest('.task-indicator')) {
        const taskIndicator = e.target.closest('.task-indicator');
        const taskId = taskIndicator.getAttribute('data-task-id');
        const taskTitle = taskIndicator.getAttribute('title');
        
        if (taskId) {
            // Redirect to task details page
            window.location.href = `/tasks/${taskId}`;
        } else {
            // Fallback: Show modal with basic info
            const modal = new bootstrap.Modal(document.getElementById('taskDetailsModal'));
            document.getElementById('taskDetailsContent').innerHTML = `
                <div class="text-center">
                    <i data-feather="info" class="text-primary mb-3" style="width: 48px; height: 48px;"></i>
                    <h6>${taskTitle}</h6>
                    <p class="text-muted">انقر للانتقال إلى تفاصيل المهمة</p>
                </div>
            `;
            document.getElementById('viewTaskLink').href = '#';
            modal.show();
        }
    }
});

// Add hover effect to task indicators
document.addEventListener('DOMContentLoaded', function() {
    const taskIndicators = document.querySelectorAll('.task-indicator');
    taskIndicators.forEach(indicator => {
        indicator.style.cursor = 'pointer';
        indicator.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.zIndex = '10';
        });
        indicator.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.zIndex = '1';
        });
    });
});

// Refresh calendar function
function refreshCalendar() {
    const refreshBtn = document.querySelector('[onclick="refreshCalendar()"]');
    const originalContent = refreshBtn.innerHTML;
    
    // Show loading state
    refreshBtn.innerHTML = '<i data-feather="loader" style="width: 16px; height: 16px; animation: spin 1s linear infinite;"></i>';
    refreshBtn.disabled = true;
    
    // Reload the page with current parameters
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Auto-refresh calendar data every 10 minutes
setInterval(() => {
    console.log('Auto-refreshing calendar data...');
    location.reload();
}, 600000); // 10 minutes

// Add CSS for spin animation
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>
@endpush 