@extends('layouts.master')

@section('title', 'تفاصيل أحداث المهمة')

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
                                <i data-feather="activity" class="text-primary" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h1 class="h3 mb-1 text-dark">تفاصيل أحداث المهمة</h1>
                                <p class="text-muted mb-0">سجل الأحداث والتحديثات للمهمة: {{ $task->title }}</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('task-events.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left" class="me-2" style="width: 16px; height: 16px;"></i>
                                العودة للقائمة
                            </a>
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-primary">
                                <i data-feather="eye" class="me-2" style="width: 16px; height: 16px;"></i>
                                عرض المهمة
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Task Information -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i data-feather="info" class="me-2" style="width: 18px; height: 18px;"></i>
                                معلومات المهمة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted small">عنوان المهمة</label>
                                <p class="mb-0 fw-bold">{{ $task->title }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">الوصف</label>
                                <p class="mb-0">{{ $task->description ?: 'لا يوجد وصف' }}</p>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label text-muted small">الحالة</label>
                                    <div>
                                        <span class="badge bg-{{ $task->status_color }}">{{ $task->status_label }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small">الأولوية</label>
                                    <div>
                                        <span class="badge bg-{{ $task->priority_color }}">{{ $task->priority_label }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">المشروع</label>
                                <p class="mb-0">
                                    @if($task->project)
                                        <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                                            {{ $task->project->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">الفئة</label>
                                <p class="mb-0">
                                    @if($task->category)
                                        {{ $task->category->name }}
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label text-muted small">تاريخ الإنشاء</label>
                                    <p class="mb-0 small">{{ $task->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small">آخر تحديث</label>
                                    <p class="mb-0 small">{{ $task->updated_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($task->deadline)
                            <div class="mb-3">
                                <label class="form-label text-muted small">الموعد النهائي</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $task->deadline < now() && $task->status !== 'completed' ? 'danger' : 'success' }}">
                                        {{ $task->deadline->format('Y-m-d H:i') }}
                                    </span>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Event Timeline -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i data-feather="clock" class="me-2" style="width: 18px; height: 18px;"></i>
                                سجل الأحداث
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <!-- Task Created -->
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success">
                                        <i data-feather="plus" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">تم إنشاء المهمة</h6>
                                                <p class="text-muted small mb-0">{{ $updates['created_at']->format('Y-m-d H:i:s') }}</p>
                                            </div>
                                            <span class="badge bg-success">إنشاء</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Changes -->
                                @foreach($updates['status_changes'] as $change)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info">
                                        <i data-feather="refresh-cw" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">تغيير حالة المهمة</h6>
                                                <p class="text-muted small mb-0">
                                                    تم تغيير الحالة إلى: 
                                                    <span class="badge bg-{{ $task->status_color }}">{{ $task->status_label }}</span>
                                                </p>
                                                <p class="text-muted small mb-0">{{ $change['updated_at']->format('Y-m-d H:i:s') }}</p>
                                            </div>
                                            <span class="badge bg-info">تحديث</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <!-- Assignments -->
                                @foreach($updates['assignments'] as $assignment)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning">
                                        <i data-feather="user-plus" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">تكليف المستخدم</h6>
                                                <p class="text-muted small mb-0">
                                                    تم تكليف: <strong>{{ $assignment['user'] }}</strong>
                                                </p>
                                                <p class="text-muted small mb-0">{{ $assignment['assigned_at']->format('Y-m-d H:i:s') }}</p>
                                            </div>
                                            <span class="badge bg-warning">تكليف</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <!-- Last Update -->
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary">
                                        <i data-feather="edit" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">آخر تحديث</h6>
                                                <p class="text-muted small mb-0">{{ $updates['updated_at']->format('Y-m-d H:i:s') }}</p>
                                            </div>
                                            <span class="badge bg-primary">تحديث</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Task Dependencies -->
            @if($task->taskDependencies->isNotEmpty() || $task->dependents->isNotEmpty())
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i data-feather="link" class="me-2" style="width: 18px; height: 18px;"></i>
                                تبعيات المهمة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($task->taskDependencies->isNotEmpty())
                                <div class="col-md-6">
                                    <h6 class="mb-3">المهام المطلوبة</h6>
                                    <div class="list-group list-group-flush">
                                        @foreach($task->taskDependencies as $dependency)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <a href="{{ route('tasks.show', $dependency->prerequisiteTask) }}" class="text-decoration-none">
                                                    {{ $dependency->prerequisiteTask->title }}
                                                </a>
                                                <small class="text-muted d-block">{{ $dependency->prerequisiteTask->status_label }}</small>
                                            </div>
                                            <span class="badge bg-{{ $dependency->prerequisiteTask->status_color }}">
                                                {{ $dependency->prerequisiteTask->status_label }}
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if($task->dependents->isNotEmpty())
                                <div class="col-md-6">
                                    <h6 class="mb-3">المهام المعتمدة على هذه المهمة</h6>
                                    <div class="list-group list-group-flush">
                                        @foreach($task->dependents as $dependent)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <a href="{{ route('tasks.show', $dependent->dependentTask) }}" class="text-decoration-none">
                                                    {{ $dependent->dependentTask->title }}
                                                </a>
                                                <small class="text-muted d-block">{{ $dependent->dependentTask->status_label }}</small>
                                            </div>
                                            <span class="badge bg-{{ $dependent->dependentTask->status_color }}">
                                                {{ $dependent->dependentTask->status_label }}
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection 