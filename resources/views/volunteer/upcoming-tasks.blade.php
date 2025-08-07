@extends('layouts.master')

@section('title', 'المهام القادمة')

@section('content')
<div class="container-fluid">
    <!-- Reminder Alerts Container -->
    <div id="reminder-alerts" class="mb-4"></div>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card volunteer-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="feather icon-clock text-warning"></i>
                            المهام القادمة ({{ $upcomingTasks->total() }})
                        </h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('volunteer.dashboard') }}" class="btn btn-outline-primary">
                                <i class="feather icon-arrow-left me-2"></i>
                                العودة للوحة التحكم
                            </a>
                            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                                <i class="feather icon-plus me-2"></i>
                                مهمة جديدة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card volunteer-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="feather icon-filter text-primary"></i>
                        تصفية حسب الأولوية
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="priority-filter active" data-priority="all">
                            <i class="feather icon-list"></i>
                            جميع المهام ({{ $upcomingTasks->total() }})
                        </button>
                        <button class="priority-filter" data-priority="urgent">
                            <i class="feather icon-alert-triangle text-danger"></i>
                            عاجلة
                        </button>
                        <button class="priority-filter" data-priority="high">
                            <i class="feather icon-flag text-warning"></i>
                            عالية
                        </button>
                        <button class="priority-filter" data-priority="medium">
                            <i class="feather icon-clock text-info"></i>
                            متوسطة
                        </button>
                        <button class="priority-filter" data-priority="low">
                            <i class="feather icon-check-circle text-success"></i>
                            منخفضة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Grid -->
    <div class="row">
        @forelse($upcomingTasks as $task)
            <div class="col-lg-6 col-xl-4 mb-4 task-card-container" 
                 data-priority="{{ $task->priority }}"
                 data-status="{{ $task->status }}">
                <div class="task-card" 
                     data-task-id="{{ $task->id }}"
                     data-priority="{{ $task->priority }}"
                     data-status="{{ $task->status }}"
                     data-progress="{{ $task->progress ?? 0 }}">
                    
                    <!-- Task Header -->
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 task-title">{{ $task->title }}</h6>
                                @if($task->project)
                                    <span class="badge badge-info">{{ $task->project->name }}</span>
                                @endif
                            </div>
                            <div class="task-priority" data-priority="{{ $task->priority }}">
                                <span class="badge badge-{{ $task->priority_color }}">
                                    {{ $task->priority_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Task Body -->
                    <div class="card-body">
                        <!-- Task Meta -->
                        <div class="task-meta">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge badge-{{ $task->status_color }}">
                                    {{ $task->status_label }}
                                </span>
                                <div class="task-deadline" data-deadline="{{ $task->deadline?->toISOString() }}">
                                    @if($task->deadline)
                                        <div class="text-end">
                                            <i class="feather icon-calendar text-muted"></i>
                                            <span class="text-{{ $task->deadline->isPast() ? 'danger' : 'success' }} fw-bold">
                                                {{ $task->deadline->format('Y-m-d') }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $task->deadline->diffForHumans() }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Task Description -->
                        @if($task->description)
                            <p class="text-muted mb-3">{{ Str::limit($task->description, 120) }}</p>
                        @endif

                        <!-- Completion Interface -->
                        @if($task->status !== 'completed')
                            <div class="completion-interface">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">تقدم المهمة</h6>
                                    <span class="completion-status {{ $task->completion_status }}">
                                        {{ $task->completion_status === 'completed' ? 'مكتملة' : 
                                           ($task->completion_status === 'near_completion' ? 'قريبة من الإنجاز' : 
                                           ($task->completion_status === 'half_completed' ? 'نصف مكتملة' : 
                                           ($task->completion_status === 'started' ? 'مبدوءة' : 'لم تبدأ'))) }}
                                    </span>
                                </div>
                                <div class="completion-progress">
                                    <div class="completion-progress-bar" 
                                         style="width: {{ $task->completion_percentage }}%; background-color: {{ $task->completion_color }}"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="text-muted small">{{ $task->completion_percentage }}% مكتمل</span>
                                    @if($task->canBeCompleted())
                                        <button class="completion-button" data-task-id="{{ $task->id }}">
                                            <i class="feather icon-check-circle me-2"></i>
                                            {{ $task->completion_percentage >= 100 ? 'إكمال المهمة' : 'تحديث التقدم' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Task Actions -->
                        <div class="task-actions mt-3">
                            <a href="{{ route('tasks.show', $task->id) }}" 
                               class="task-action-btn primary">
                                <i class="feather icon-eye"></i>
                                عرض التفاصيل
                            </a>
                            @if($task->canBeCompleted())
                                <button class="task-action-btn success completion-button" 
                                        data-task-id="{{ $task->id }}">
                                    <i class="feather icon-check-circle"></i>
                                    إكمال المهمة
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card volunteer-card">
                    <div class="card-body text-center py-5">
                        <i class="feather icon-check-circle fa-4x text-success mb-3"></i>
                        <h4>لا توجد مهام قادمة</h4>
                        <p class="text-muted mb-4">جميع مهامك محدثة ومكتملة!</p>
                        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                            <i class="feather icon-plus me-2"></i>
                            إنشاء مهمة جديدة
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($upcomingTasks->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $upcomingTasks->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Priority Filter Styles -->
<style>
.priority-filter {
    padding: 8px 16px;
    border: 2px solid #e9ecef;
    border-radius: 25px;
    background: white;
    color: #6c757d;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.priority-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    text-decoration: none;
}

.priority-filter.active {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border-color: #007bff;
}

.priority-filter[data-priority="urgent"]:hover {
    border-color: #dc3545;
    color: #dc3545;
}

.priority-filter[data-priority="high"]:hover {
    border-color: #fd7e14;
    color: #fd7e14;
}

.priority-filter[data-priority="medium"]:hover {
    border-color: #17a2b8;
    color: #17a2b8;
}

.priority-filter[data-priority="low"]:hover {
    border-color: #28a745;
    color: #28a745;
}

.task-card-container {
    transition: all 0.3s ease;
}

.task-card-container.hidden {
    display: none;
}
</style>

@endsection 