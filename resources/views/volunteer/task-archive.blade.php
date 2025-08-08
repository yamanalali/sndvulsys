@extends('layouts.app')

@section('title', 'أرشيف المهام')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">
                    <i class="fas fa-archive text-primary"></i>
                    أرشيف المهام
                </h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('volunteer.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active">أرشيف المهام</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('task-history.archive') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">البحث</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ $search }}" placeholder="ابحث في عناوين المهام...">
                        </div>
                        <div class="col-md-3">
                            <label for="priority" class="form-label">الأولوية</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="">جميع الأولويات</option>
                                <option value="urgent" {{ $priority === 'urgent' ? 'selected' : '' }}>عاجلة</option>
                                <option value="high" {{ $priority === 'high' ? 'selected' : '' }}>عالية</option>
                                <option value="medium" {{ $priority === 'medium' ? 'selected' : '' }}>متوسطة</option>
                                <option value="low" {{ $priority === 'low' ? 'selected' : '' }}>منخفضة</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="project" class="form-label">المشروع</label>
                            <select class="form-select" id="project" name="project">
                                <option value="">جميع المشاريع</option>
                                @foreach($projects as $id => $name)
                                    <option value="{{ $id }}" {{ $project == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Archived Tasks -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-archive"></i>
                        المهام المؤرشفة ({{ $archivedTasks->total() }} مهمة)
                    </h5>
                </div>
                <div class="card-body">
                    @if($archivedTasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>العنوان</th>
                                        <th>المشروع</th>
                                        <th>الأولوية</th>
                                        <th>تاريخ الأرشفة</th>
                                        <th>التقدم</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($archivedTasks as $task)
                                        <tr>
                                            <td>
                                                <div class="task-info">
                                                    <h6 class="task-title">{{ $task->title }}</h6>
                                                    <small class="text-muted">{{ Str::limit($task->description, 100) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($task->project)
                                                    <span class="badge bg-info">{{ $task->project->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->priority_color }}">{{ $task->priority_label }}</span>
                                            </td>
                                            <td>
                                                <div class="archive-date">
                                                    <span>{{ $task->updated_at->format('Y-m-d') }}</span>
                                                    <small class="text-muted d-block">{{ $task->updated_at->diffForHumans() }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" 
                                                         style="width: {{ $task->progress }}%">
                                                        {{ $task->progress }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewTaskDetails({{ $task->id }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" 
                                                            onclick="restoreTask({{ $task->id }})">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                    <a href="{{ route('task-history.timeline', $task) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $archivedTasks->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                                <h5>لا توجد مهام مؤرشفة</h5>
                                <p class="text-muted">لم يتم العثور على أي مهام في الأرشيف.</p>
                                <a href="{{ route('volunteer.dashboard') }}" class="btn btn-primary">
                                    <i class="fas fa-home"></i> العودة للوحة التحكم
                                </a>
                            </div>
                        </div>
                    @endif
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
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.task-info {
    max-width: 300px;
}

.task-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
}

.archive-date {
    font-size: 12px;
}

.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
    font-size: 11px;
    line-height: 20px;
}

.empty-state {
    color: #6c757d;
}

.empty-state i {
    opacity: 0.5;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endpush

@push('scripts')
<script>
function restoreTask(taskId) {
    if (confirm('هل أنت متأكد من استعادة هذه المهمة؟')) {
        fetch(`/task-history/restore/${taskId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('success', 'تم استعادة المهمة بنجاح');
                // Reload page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showNotification('error', data.error || 'حدث خطأ أثناء استعادة المهمة');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'حدث خطأ أثناء استعادة المهمة');
        });
    }
}

function viewTaskDetails(taskId) {
    const modal = new bootstrap.Modal(document.getElementById('taskDetailsModal'));
    const content = document.getElementById('taskDetailsContent');
    
    // Show loading
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Fetch task details
    fetch(`/tasks/${taskId}`)
        .then(response => response.json())
        .then(task => {
            content.innerHTML = `
                <div class="task-details">
                    <h6>${task.title}</h6>
                    <p class="text-muted">${task.description || 'لا يوجد وصف'}</p>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>الحالة:</strong>
                            <span class="badge bg-secondary">مؤرشفة</span>
                        </div>
                        <div class="col-md-6">
                            <strong>الأولوية:</strong>
                            <span class="badge bg-${getPriorityColor(task.priority)}">${getPriorityLabel(task.priority)}</span>
                        </div>
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>تاريخ الإنشاء:</strong>
                            <span>${formatDate(task.created_at)}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>التقدم:</strong>
                            <span>${task.progress}%</span>
                        </div>
                    </div>
                    
                    ${task.deadline ? `
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <strong>الموعد النهائي:</strong>
                                <span>${formatDate(task.deadline)}</span>
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>حدث خطأ أثناء تحميل تفاصيل المهمة</p>
                </div>
            `;
        });
}

function getPriorityColor(priority) {
    const colors = {
        'urgent': 'danger',
        'high': 'warning',
        'medium': 'info',
        'low': 'success'
    };
    return colors[priority] || 'secondary';
}

function getPriorityLabel(priority) {
    const labels = {
        'urgent': 'عاجلة',
        'high': 'عالية',
        'medium': 'متوسطة',
        'low': 'منخفضة'
    };
    return labels[priority] || priority;
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('ar-SA');
}

function showNotification(type, message) {
    // You can implement your own notification system here
    alert(message);
}
</script>
@endpush 