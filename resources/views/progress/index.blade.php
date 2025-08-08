@extends('layouts.master')

@section('title', 'تتبع التقدم')

@section('styles')
<link rel="stylesheet" href="{{ asset('files/assets/css/progress-tracking.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 rounded p-3">
                                <i data-feather="trending-up" class="text-primary" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h1 class="h3 mb-1 text-dark">تتبع التقدم</h1>
                                <p class="text-muted mb-0">مراقبة تقدم المهام والمواعيد النهائية</p>
                                @if($progressStats['total_tasks'] > 0)
                                    <small class="text-success">
                                        <i data-feather="check-circle" style="width: 14px; height: 14px;"></i>
                                        تم تحميل {{ $progressStats['total_tasks'] }} مهمة بنجاح
                                    </small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('progress.calendar') }}" class="btn btn-outline-primary">
                                <i data-feather="calendar" class="me-2" style="width: 16px; height: 16px;"></i>
                                تقويم المواعيد
                            </a>
                            <button onclick="refreshProgressData()" class="btn btn-primary">
                                <i data-feather="refresh-cw" class="me-2" style="width: 16px; height: 16px;"></i>
                                تحديث البيانات
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overall Progress Bar -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    @if($progressStats['total_tasks'] > 0)
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-3 text-dark">التقدم العام</h5>
                                <div class="progress mb-3" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $progressStats['completion_rate'] }}%" 
                                         aria-valuenow="{{ $progressStats['completion_rate'] }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ $progressStats['completion_rate'] }}%
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between text-muted small">
                                    <span>0%</span>
                                    <span>50%</span>
                                    <span>100%</span>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="display-4 text-primary fw-bold">{{ $progressStats['completion_rate'] }}%</div>
                                <p class="text-muted mb-0">نسبة الإنجاز</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i data-feather="inbox" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                            <h6 class="text-muted">لا توجد مهام مكلف بها</h6>
                            <p class="text-muted small">لم يتم تعيين أي مهام لك بعد. يمكنك إنشاء مهام جديدة أو انتظار تعيين مهام من المدير.</p>
                            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-2" style="width: 16px; height: 16px;"></i>
                                إنشاء مهمة جديدة
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Cards -->
            @if($progressStats['total_tasks'] > 0)
            <div class="row mb-4">
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">إجمالي المهام</p>
                                    <p class="h4 mb-0 text-dark">{{ $progressStats['total_tasks'] }}</p>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <i data-feather="list" class="text-primary" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">مكتملة</p>
                                    <p class="h4 mb-0 text-success">{{ $progressStats['completed_tasks'] }}</p>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded p-2">
                                    <i data-feather="check-circle" class="text-success" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">قيد التنفيذ</p>
                                    <p class="h4 mb-0 text-primary">{{ $progressStats['in_progress_tasks'] }}</p>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <i data-feather="play" class="text-primary" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">متأخرة</p>
                                    <p class="h4 mb-0 text-danger">{{ $progressStats['overdue_tasks'] }}</p>
                                </div>
                                <div class="bg-danger bg-opacity-10 rounded p-2">
                                    <i data-feather="clock" class="text-danger" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">معلقة</p>
                                    <p class="h4 mb-0 text-warning">{{ $progressStats['pending_tasks'] }}</p>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <i data-feather="pause" class="text-warning" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">المشاريع النشطة</p>
                                    <p class="h4 mb-0 text-info">{{ $activeProjects->count() }}</p>
                                </div>
                                <div class="bg-info bg-opacity-10 rounded p-2">
                                    <i data-feather="folder" class="text-info" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($progressStats['total_tasks'] > 0)
            <div class="row">
                <!-- Upcoming Deadlines -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                                <i data-feather="calendar" class="text-warning" style="width: 20px; height: 20px;"></i>
                                المواعيد النهائية القريبة
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($upcomingDeadlines->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($upcomingDeadlines->take(5) as $deadline)
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $deadline['task']->title }}</h6>
                                                    <p class="text-muted small mb-0">
                                                        {{ $deadline['task']->project ? $deadline['task']->project->name : 'بدون مشروع' }}
                                                    </p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $deadline['urgency'] == 'critical' ? 'danger' : ($deadline['urgency'] == 'high' ? 'warning' : 'info') }}">
                                                        {{ $deadline['days_left'] }} يوم
                                                    </span>
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

                <!-- Detailed Task Progress -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                                <i data-feather="bar-chart-2" class="text-success" style="width: 20px; height: 20px;"></i>
                                تقدم المهام التفصيلي
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($userTasks->count() > 0)
                                <div class="task-progress-list">
                                    @foreach($userTasks->take(5) as $task)
                                        <div class="task-progress-item mb-3 p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-1">{{ $task->title }}</h6>
                                                <span class="badge bg-{{ $task->status_color }}">{{ $task->status_label }}</span>
                                            </div>
                                            
                                            <div class="progress mb-2" style="height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $task->progress }}%" 
                                                     aria-valuenow="{{ $task->progress }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">{{ $task->progress }}% مكتمل</small>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('tasks.show', $task->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-success" 
                                                            onclick="updateTaskProgress({{ $task->id }}, {{ $task->progress }})">
                                                        <i data-feather="edit-3" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            @if($task->deadline)
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i data-feather="clock" style="width: 12px; height: 12px;"></i>
                                                        @php
                                                            $daysLeft = now()->diffInDays($task->deadline, false);
                                                        @endphp
                                                        @if($daysLeft > 0)
                                                            <span class="text-success">{{ $daysLeft }} يوم متبقي</span>
                                                        @elseif($daysLeft == 0)
                                                            <span class="text-warning">ينتهي اليوم</span>
                                                        @else
                                                            <span class="text-danger">متأخرة {{ abs($daysLeft) }} يوم</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if($userTasks->count() > 5)
                                    <div class="text-center mt-3">
                                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary btn-sm">
                                            عرض جميع المهام
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i data-feather="check-circle" class="text-success mb-3" style="width: 48px; height: 48px;"></i>
                                    <h6 class="text-muted">لا توجد مهام مكلف بها</h6>
                                    <p class="text-muted small">لم يتم تعيين أي مهام لك بعد</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Weekly Progress Chart -->
                <div class="col-md-8 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                                <i data-feather="bar-chart-2" class="text-primary" style="width: 20px; height: 20px;"></i>
                                التقدم الأسبوعي
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="weeklyProgressChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Overdue Tasks -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                                <i data-feather="alert-triangle" class="text-danger" style="width: 20px; height: 20px;"></i>
                                المهام المتأخرة
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($overdueTasks->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($overdueTasks->take(5) as $overdue)
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $overdue['task']->title }}</h6>
                                                    <p class="text-muted small mb-0">
                                                        {{ $overdue['task']->project ? $overdue['task']->project->name : 'بدون مشروع' }}
                                                    </p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $overdue['severity'] == 'critical' ? 'danger' : ($overdue['severity'] == 'high' ? 'warning' : 'secondary') }}">
                                                        متأخرة {{ $overdue['days_overdue'] }} يوم
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

            <!-- Status Distribution -->
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                                <i data-feather="pie-chart" class="text-info" style="width: 20px; height: 20px;"></i>
                                توزيع الحالات
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="statusDistributionChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Projects -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-feather="folder" class="text-info" style="width: 20px; height: 20px;"></i>
                        المشاريع النشطة
                    </h5>
                </div>
                <div class="card-body">
                    @if($activeProjects->count() > 0)
                        <div class="row">
                            @foreach($activeProjects as $projectData)
                                <div class="col-md-4 mb-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="mb-0">{{ $projectData['project']->name }}</h6>
                                                <span class="badge bg-primary">{{ $projectData['completion_rate'] }}%</span>
                                            </div>
                                            
                                            <div class="progress mb-3" style="height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $projectData['completion_rate'] }}%" 
                                                     aria-valuenow="{{ $projectData['completion_rate'] }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between text-muted small">
                                                <span>{{ $projectData['completed_tasks'] }}/{{ $projectData['total_tasks'] }} مكتملة</span>
                                                <a href="{{ route('progress.project', $projectData['project']) }}" class="text-primary">
                                                    <i data-feather="arrow-right" style="width: 14px; height: 14px;"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i data-feather="folder" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                            <h6 class="text-muted">لا توجد مشاريع نشطة</h6>
                            <p class="text-muted small">لم يتم تخصيص أي مهام لك في المشاريع الحالية</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Progress Update Modal -->
<div class="modal fade" id="progressUpdateModal" tabindex="-1" aria-labelledby="progressUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="progressUpdateModalLabel">تحديث تقدم المهمة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quickProgressForm">
                    <input type="hidden" id="taskId" name="task_id">
                    <div class="mb-3">
                        <label for="progressSlider" class="form-label">نسبة التقدم</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range flex-grow-1" 
                                   id="progressSlider" min="0" max="100" step="5">
                            <span class="badge bg-success" id="progressValue">0%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="progressNote" class="form-label">ملاحظة (اختياري)</label>
                        <textarea class="form-control" id="progressNote" rows="2" 
                                  placeholder="أضف ملاحظة حول التقدم..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success" onclick="submitProgressUpdate()">
                    <i data-feather="save" style="width: 16px; height: 16px;"></i>
                    تحديث التقدم
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($progressStats['total_tasks'] > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Weekly Progress Chart
const weeklyCtx = document.getElementById('weeklyProgressChart').getContext('2d');
const weeklyChart = new Chart(weeklyCtx, {
    type: 'bar',
    data: {
        labels: @json($weeklyProgress->pluck('day')),
        datasets: [{
            label: 'المهام المكتملة',
            data: @json($weeklyProgress->pluck('completed')),
            backgroundColor: 'rgba(40, 167, 69, 0.8)',
            borderColor: 'rgba(40, 167, 69, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Status Distribution Chart
const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['مكتملة', 'قيد التنفيذ', 'معلقة', 'جديدة'],
        datasets: [{
            data: [
                {{ $statusDistribution['completed'] ?? 0 }},
                {{ $statusDistribution['in_progress'] ?? 0 }},
                {{ $statusDistribution['pending'] ?? 0 }},
                {{ $statusDistribution['new'] ?? 0 }}
            ],
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(0, 123, 255, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(108, 117, 125, 0.8)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Progress Range Slider in Modal
document.addEventListener('DOMContentLoaded', function() {
    const progressSlider = document.getElementById('progressSlider');
    const progressValue = document.getElementById('progressValue');
    
    if (progressSlider && progressValue) {
        progressSlider.addEventListener('input', function() {
            progressValue.textContent = this.value + '%';
        });
    }
});

// Refresh Progress Data
function refreshProgressData() {
    location.reload();
}

// Update Task Progress Modal
function updateTaskProgress(taskId, currentProgress) {
    document.getElementById('taskId').value = taskId;
    document.getElementById('progressSlider').value = currentProgress;
    document.getElementById('progressValue').textContent = currentProgress + '%';
    
    const modal = new bootstrap.Modal(document.getElementById('progressUpdateModal'));
    modal.show();
}

// Submit Progress Update
function submitProgressUpdate() {
    const taskId = document.getElementById('taskId').value;
    const progress = document.getElementById('progressSlider').value;
    const note = document.getElementById('progressNote').value;
    
    // إرسال طلب التحديث
    fetch(`/tasks/${taskId}/progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            progress: parseInt(progress),
            note: note
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // إغلاق Modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('progressUpdateModal'));
            modal.hide();
            
            // تحديث الصفحة
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            alert('حدث خطأ أثناء تحديث التقدم: ' + (data.message || 'خطأ غير معروف'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال بالخادم');
    });
}

// Auto-refresh every 5 minutes
setInterval(() => {
    // تحديث البيانات عن طريق إعادة تحميل الصفحة
    console.log('Auto-refreshing progress data...');
    location.reload();
}, 300000); // 5 minutes
</script>
@endif
@endpush 