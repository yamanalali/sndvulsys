@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active">المهام المتكررة</li>
                    </ol>
                </div>
                <h4 class="page-title">إدارة المهام المتكررة</h4>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="feather-repeat text-muted"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0" title="المهام المتكررة النشطة">المهام المتكررة النشطة</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['active_recurring_tasks'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="feather-copy text-muted"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0" title="إجمالي المهام المنشأة">إجمالي المهام المنشأة</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['total_instances'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="feather-clock text-muted"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0" title="المهام القادمة">المهام القادمة</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['upcoming_instances'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="feather-alert-triangle text-danger"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0" title="المهام المتأخرة">المهام المتأخرة</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['overdue_instances'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recurring Tasks List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">المهام المتكررة</h4>
                    <div class="header-action">
                        <div class="btn-group" role="group">
                            <a href="{{ route('recurring-tasks.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">الكل</a>
                            <a href="{{ route('recurring-tasks.index', ['status' => 'active']) }}" class="btn btn-sm {{ request('status') === 'active' ? 'btn-primary' : 'btn-outline-secondary' }}">نشط</a>
                            <a href="{{ route('recurring-tasks.index', ['status' => 'inactive']) }}" class="btn btn-sm {{ request('status') === 'inactive' ? 'btn-primary' : 'btn-outline-secondary' }}">متوقف</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($recurringTasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>عنوان المهمة</th>
                                        <th>نمط التكرار</th>
                                        <th>التكرار التالي</th>
                                        <th>الحالة</th>
                                        <th>المهام المنشأة</th>
                                        <th>العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recurringTasks as $task)
                                    <tr>
                                        <td>
                                            <h5 class="m-0">
                                                <a href="{{ route('recurring-tasks.show', $task) }}" class="text-dark">
                                                    {{ $task->title }}
                                                </a>
                                            </h5>
                                            <small class="text-muted">{{ $task->category->name ?? 'بدون فئة' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ \App\Models\Task::getRecurrencePatterns()[$task->recurrence_pattern] ?? $task->recurrence_pattern }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($task->next_occurrence_date)
                                                <span class="text-muted">{{ $task->next_occurrence_date->format('Y-m-d') }}</span>
                                            @else
                                                <span class="text-muted">غير محدد</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->recurring_active)
                                                <span class="badge badge-success">نشط</span>
                                            @else
                                                <span class="badge badge-secondary">متوقف</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $task->recurringInstances->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('recurring-tasks.show', $task) }}" class="btn btn-outline-primary btn-sm" title="عرض التفاصيل">
                                                    <i class="feather-eye"></i>
                                                </a>
                                                <a href="{{ route('recurring-tasks.edit', $task) }}" class="btn btn-outline-secondary btn-sm" title="تعديل">
                                                    <i class="feather-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-{{ $task->recurring_active ? 'warning' : 'success' }} btn-sm" 
                                                        onclick="toggleActive({{ $task->id }})" title="{{ $task->recurring_active ? 'إيقاف' : 'تفعيل' }}">
                                                    <i class="feather-{{ $task->recurring_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $recurringTasks->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="feather-repeat text-muted" style="font-size: 48px;"></i>
                            <h5 class="mt-3">لا توجد مهام متكررة</h5>
                            <p class="text-muted">لم يتم إنشاء أي مهام متكررة بعد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Tasks Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">المهام القادمة (7 أيام)</h4>
                </div>
                <div class="card-body">
                    @if($upcomingTasks->count() > 0)
                        <div class="timeline-alt">
                            @foreach($upcomingTasks as $task)
                            <div class="timeline-item">
                                <i class="feather-clock bg-info-lighten text-info timeline-icon"></i>
                                <div class="timeline-item-info">
                                    <h5 class="mt-0 mb-1">{{ $task->title }}</h5>
                                    <p class="font-14 text-muted mb-1">
                                        <small>{{ $task->start_date->format('Y-m-d') }}</small>
                                    </p>
                                    <p class="text-muted mb-0">
                                        @if($task->parentTask)
                                            <small>من: {{ $task->parentTask->title }}</small>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="feather-clock text-muted" style="font-size: 32px;"></i>
                            <p class="text-muted mt-2">لا توجد مهام قادمة</p>
                        </div>
                    @endif
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
</script>
@endpush
@endsection