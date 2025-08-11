@extends('layouts.master')

@section('title', 'تتبع تقدم المشروع')

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
                                <i data-feather="folder" class="text-primary" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h1 class="h3 mb-1 text-dark">{{ $project->name }}</h1>
                                <p class="text-muted mb-0">تتبع التقدم التفصيلي للمشروع</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('progress.index') }}" class="btn btn-outline-primary">
                                <i data-feather="arrow-left" class="me-2" style="width: 16px; height: 16px;"></i>
                                رجوع للتقدم العام
                            </a>
                            <button onclick="refreshProjectData()" class="btn btn-primary">
                                <i data-feather="refresh-cw" class="me-2" style="width: 16px; height: 16px;"></i>
                                تحديث البيانات
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Overview -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark">نظرة عامة على المشروع</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">تفاصيل المشروع</h6>
                                    <div class="mb-3">
                                        <strong>الاسم:</strong> {{ $project->name }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>الوصف:</strong> {{ $project->description ?: 'لا يوجد وصف' }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>تاريخ البدء:</strong> {{ $project->start_date ? $project->start_date->format('Y-m-d') : 'غير محدد' }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>تاريخ الانتهاء:</strong> {{ $project->end_date ? $project->end_date->format('Y-m-d') : 'غير محدد' }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">إحصائيات المشروع</h6>
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="stat-item">
                                                <h4 class="text-primary">{{ $projectStats['total_tasks'] }}</h4>
                                                <small class="text-muted">إجمالي المهام</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="stat-item">
                                                <h4 class="text-success">{{ $projectStats['completed_tasks'] }}</h4>
                                                <small class="text-muted">مكتملة</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="stat-item">
                                                <h4 class="text-warning">{{ $projectStats['in_progress_tasks'] }}</h4>
                                                <small class="text-muted">قيد التنفيذ</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="stat-item">
                                                <h4 class="text-danger">{{ $projectStats['overdue_tasks'] }}</h4>
                                                <small class="text-muted">متأخرة</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark">التقدم العام</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="progress mb-3" style="height: 30px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $projectStats['completion_rate'] }}%" 
                                     aria-valuenow="{{ $projectStats['completion_rate'] }}" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    {{ $projectStats['completion_rate'] }}%
                                </div>
                            </div>
                            <h3 class="text-primary">{{ $projectStats['completion_rate'] }}%</h3>
                            <p class="text-muted mb-0">نسبة الإنجاز</p>
                            
                            @if($project->end_date)
                                <hr>
                                <div class="mt-3">
                                    <h6 class="text-muted">الوقت المتبقي</h6>
                                    @php
                                        $daysLeft = now()->diffInDays($project->end_date, false);
                                    @endphp
                                    @if($daysLeft > 0)
                                        <h4 class="text-success">{{ $daysLeft }} يوم</h4>
                                    @elseif($daysLeft == 0)
                                        <h4 class="text-warning">اليوم</h4>
                                    @else
                                        <h4 class="text-danger">متأخرة {{ abs($daysLeft) }} يوم</h4>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Task Progress Details -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark">تفاصيل تقدم المهام</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>المهمة</th>
                                    <th>الحالة</th>
                                    <th>التقدم</th>
                                    <th>المكلف</th>
                                    <th>الموعد النهائي</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projectTasks as $task)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $task->title }}</strong>
                                                @if($task->description)
                                                    <br><small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->status_color }}">{{ $task->status_label }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: {{ $task->progress }}%" 
                                                         aria-valuenow="{{ $task->progress }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $task->progress }}%</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($task->assignments->count() > 0)
                                                @foreach($task->assignments as $assignment)
                                                    <span class="badge bg-info">{{ $assignment->user->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">غير محدد</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->deadline)
                                                @php
                                                    $daysLeft = now()->diffInDays($task->deadline, false);
                                                @endphp
                                                <div>
                                                    <small>{{ $task->deadline->format('Y-m-d') }}</small>
                                                    @if($daysLeft > 0)
                                                        <br><span class="badge bg-success">{{ $daysLeft }} يوم متبقي</span>
                                                    @elseif($daysLeft == 0)
                                                        <br><span class="badge bg-warning">ينتهي اليوم</span>
                                                    @else
                                                        <br><span class="badge bg-danger">متأخرة {{ abs($daysLeft) }} يوم</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">غير محدد</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('tasks.show', $task->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="updateTaskProgress({{ $task->id }}, {{ $task->progress }})">
                                                    <i data-feather="edit-3" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i data-feather="inbox" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                                            <h6 class="text-muted">لا توجد مهام في هذا المشروع</h6>
                                            <p class="text-muted small">قم بإضافة مهام للمشروع لبدء تتبع التقدم</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Progress Charts -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark">توزيع المهام حسب الحالة</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark">التقدم الزمني</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="timelineChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['مكتملة', 'قيد التنفيذ', 'معلقة', 'جديدة', 'متأخرة'],
            datasets: [{
                data: [
                    {{ $projectStats['completed_tasks'] }},
                    {{ $projectStats['in_progress_tasks'] }},
                    {{ $projectStats['pending_tasks'] }},
                    {{ $projectStats['new_tasks'] }},
                    {{ $projectStats['overdue_tasks'] }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#007bff',
                    '#ffc107',
                    '#6c757d',
                    '#dc3545'
                ]
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

    // Timeline Chart
    const timelineCtx = document.getElementById('timelineChart').getContext('2d');
    new Chart(timelineCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($timelineData['labels']) !!},
            datasets: [{
                label: 'نسبة الإنجاز',
                data: {!! json_encode($timelineData['progress']) !!},
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
});

function updateTaskProgress(taskId, currentProgress) {
    document.getElementById('taskId').value = taskId;
    document.getElementById('progressSlider').value = currentProgress;
    document.getElementById('progressValue').textContent = currentProgress + '%';
    document.getElementById('progressNote').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('progressUpdateModal'));
    modal.show();
}

function submitProgressUpdate() {
    const taskId = document.getElementById('taskId').value;
    const progress = document.getElementById('progressSlider').value;
    const note = document.getElementById('progressNote').value;
    
    const submitBtn = document.querySelector('#progressUpdateModal .btn-success');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-feather="loader" style="width: 16px; height: 16px;"></i> جاري التحديث...';
    
    fetch(`/tasks/${taskId}/progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            progress: progress,
            note: note
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            location.reload();
        } else {
            showAlert('error', data.message || 'حدث خطأ أثناء التحديث');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'حدث خطأ في الاتصال بالخادم');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

document.getElementById('progressSlider').addEventListener('input', function() {
    document.getElementById('progressValue').textContent = this.value + '%';
});

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i data-feather="${type === 'success' ? 'check-circle' : 'alert-triangle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid');
    if (container) {
        container.insertAdjacentHTML('afterbegin', alertHtml);
    }
    
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert) {
                alert.remove();
            }
        });
    }, 5000);
}

function refreshProjectData() {
    location.reload();
}
</script>

<style>
.stat-item {
    padding: 10px 0;
}

.stat-item h4 {
    margin: 0;
    font-weight: bold;
}

.task-progress-item {
    transition: all 0.3s ease;
}

.task-progress-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>
@endsection 