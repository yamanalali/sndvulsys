@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('recurring-tasks.index') }}">المهام المتكررة</a></li>
                        <li class="breadcrumb-item active">{{ $task->title }}</li>
                    </ol>
                </div>
                <h4 class="page-title">تفاصيل المهمة المتكررة</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Task Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ $task->title }}</h4>
                    <div class="header-action">
                        <div class="btn-group">
                            <a href="{{ route('recurring-tasks.edit', $task) }}" class="btn btn-primary btn-sm">
                                <i class="feather-edit mr-1"></i> تعديل الإعدادات
                            </a>
                            <button type="button" class="btn btn-{{ $task->recurring_active ? 'warning' : 'success' }} btn-sm" 
                                    onclick="toggleActive({{ $task->id }})">
                                <i class="feather-{{ $task->recurring_active ? 'pause' : 'play' }} mr-1"></i>
                                {{ $task->recurring_active ? 'إيقاف التكرار' : 'تفعيل التكرار' }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>معلومات المهمة</h6>
                            <p><strong>الوصف:</strong> {{ $task->description ?? 'لا يوجد وصف' }}</p>
                            <p><strong>الفئة:</strong> {{ $task->category->name ?? 'بدون فئة' }}</p>
                            <p><strong>الأولوية:</strong> 
                                <span class="badge badge-{{ $task->priority_color }}">{{ $task->priority_label }}</span>
                            </p>
                            <p><strong>منشئ المهمة:</strong> {{ $task->creator->name ?? 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>إعدادات التكرار</h6>
                            <p><strong>نمط التكرار:</strong> 
                                <span class="badge badge-info">
                                    {{ \App\Models\Task::getRecurrencePatterns()[$task->recurrence_pattern] ?? $task->recurrence_pattern }}
                                </span>
                            </p>
                            <p><strong>تاريخ البدء:</strong> {{ $task->recurrence_start_date?->format('Y-m-d') ?? 'غير محدد' }}</p>
                            <p><strong>تاريخ الانتهاء:</strong> {{ $task->recurrence_end_date?->format('Y-m-d') ?? 'غير محدود' }}</p>
                            <p><strong>العدد الأقصى:</strong> {{ $task->recurrence_max_occurrences ?? 'غير محدود' }}</p>
                            <p><strong>عدد المهام المنشأة:</strong> {{ $task->recurrence_current_count }}</p>
                            <p><strong>التكرار التالي:</strong> 
                                {{ $task->next_occurrence_date?->format('Y-m-d H:i') ?? 'غير محدد' }}
                            </p>
                            <p><strong>حالة التكرار:</strong> 
                                @if($task->recurring_active)
                                    <span class="badge badge-success">نشط</span>
                                @else
                                    <span class="badge badge-secondary">متوقف</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Task Instances -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">المهام المنشأة</h4>
                    <div class="header-action">
                        <button type="button" class="btn btn-primary btn-sm" onclick="generateTasks()">
                            <i class="feather-plus mr-1"></i> إنشاء مهام جديدة
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($instances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>تاريخ البدء</th>
                                        <th>الموعد النهائي</th>
                                        <th>الحالة</th>
                                        <th>التقدم</th>
                                        <th>المكلف</th>
                                        <th>العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($instances as $instance)
                                    <tr>
                                        <td>{{ $instance->start_date?->format('Y-m-d') }}</td>
                                        <td>{{ $instance->deadline?->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $instance->status_color }}">
                                                {{ $instance->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $instance->status_color }}" 
                                                     style="width: {{ $instance->progress }}%"></div>
                                            </div>
                                            <small>{{ $instance->progress }}%</small>
                                        </td>
                                        <td>
                                            @if($instance->assignments->count() > 0)
                                                @foreach($instance->assignments->take(2) as $assignment)
                                                    <span class="badge badge-soft-primary">{{ $assignment->user->name }}</span>
                                                @endforeach
                                                @if($instance->assignments->count() > 2)
                                                    <span class="badge badge-soft-secondary">+{{ $instance->assignments->count() - 2 }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">غير مكلف</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('tasks.show', $instance) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="feather-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $instances->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="feather-copy text-muted" style="font-size: 48px;"></i>
                            <h5 class="mt-3">لا توجد مهام منشأة</h5>
                            <p class="text-muted">لم يتم إنشاء أي مهام من هذا النمط المتكرر بعد</p>
                            <button type="button" class="btn btn-primary" onclick="generateTasks()">
                                <i class="feather-plus mr-1"></i> إنشاء مهام جديدة
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Upcoming Occurrences -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">التكرارات القادمة</h4>
                    <div class="header-action">
                        <a href="{{ route('recurring-tasks.exceptions', $task) }}" class="btn btn-outline-primary btn-sm">
                            <i class="feather-settings mr-1"></i> إدارة الاستثناءات
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(!empty($upcomingOccurrences))
                        <div class="timeline-alt">
                            @foreach($upcomingOccurrences as $occurrence)
                            <div class="timeline-item">
                                <i class="feather-calendar bg-{{ $occurrence['has_exception'] ? 'warning' : 'info' }}-lighten 
                                         text-{{ $occurrence['has_exception'] ? 'warning' : 'info' }} timeline-icon"></i>
                                <div class="timeline-item-info">
                                    <h6 class="mt-0 mb-1">{{ \Carbon\Carbon::parse($occurrence['date'])->format('Y-m-d') }}</h6>
                                    @if($occurrence['has_exception'])
                                        <p class="font-12 text-warning mb-0">
                                            <i class="feather-alert-triangle mr-1"></i>
                                            {{ $occurrence['exception_label'] }}
                                        </p>
                                    @else
                                        <p class="font-12 text-muted mb-0">
                                            <i class="feather-check mr-1"></i>
                                            تكرار عادي
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="feather-calendar text-muted" style="font-size: 32px;"></i>
                            <p class="text-muted mt-2">لا توجد تكرارات قادمة</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">إجراءات سريعة</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="generateTasks()">
                            <i class="feather-plus mr-1"></i> إنشاء مهام جديدة
                        </button>
                        <a href="{{ route('recurring-tasks.exceptions', $task) }}" class="btn btn-outline-warning">
                            <i class="feather-settings mr-1"></i> إدارة الاستثناءات
                        </a>
                        <a href="{{ route('recurring-tasks.edit', $task) }}" class="btn btn-outline-secondary">
                            <i class="feather-edit mr-1"></i> تعديل الإعدادات
                        </a>
                        <button type="button" class="btn btn-outline-{{ $task->recurring_active ? 'warning' : 'success' }}" 
                                onclick="toggleActive({{ $task->id }})">
                            <i class="feather-{{ $task->recurring_active ? 'pause' : 'play' }} mr-1"></i>
                            {{ $task->recurring_active ? 'إيقاف التكرار' : 'تفعيل التكرار' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleActive(taskId) {
    if (!confirm('هل أنت متأكد من تغيير حالة التكرار؟')) {
        return;
    }

    $.ajax({
        url: `/recurring-tasks/${taskId}/toggle-active`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            location.reload();
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert(response.error || 'حدث خطأ غير متوقع');
        }
    });
}

function generateTasks() {
    const days = prompt('عدد الأيام التي تريد إنشاء مهام لها:', '30');
    
    if (!days || days <= 0) {
        return;
    }

    $.ajax({
        url: `/recurring-tasks/{{ $task->id }}/generate`,
        method: 'POST',
        data: { days: days },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            alert(response.message);
            location.reload();
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert(response.error || 'حدث خطأ غير متوقع');
        }
    });
}
</script>
@endpush
@endsection