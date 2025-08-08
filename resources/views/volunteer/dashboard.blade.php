@extends('layouts.master')

@section('title', 'لوحة تحكم المتطوع')

@section('content')
<div class="pcoded-content">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <!-- Header -->
                <div class="page-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title">
                                <div class="d-inline">
                                    <h4 class="text-primary">
                                        <i class="feather icon-user"></i>
                                        لوحة تحكم المتطوع
                                    </h4>
                                    <span class="text-muted">مرحباً {{ Auth::user()->name }}، هذه لوحة تحكمك الشخصية</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="page-header-breadcrumb">
                                <ul class="breadcrumb-title">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('home') }}">
                                            <i class="feather icon-home"></i>
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <span>لوحة تحكم المتطوع</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="mb-1">{{ $stats['total_tasks'] }}</h4>
                                        <span>إجمالي المهام</span>
                                    </div>
                                    <div class="col-auto">
                                        <i class="feather icon-file-text fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="mb-1">{{ $stats['completed_tasks'] }}</h4>
                                        <span>المهام المكتملة</span>
                                    </div>
                                    <div class="col-auto">
                                        <i class="feather icon-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="mb-1">{{ $stats['upcoming_tasks'] }}</h4>
                                        <span>المهام القادمة</span>
                                    </div>
                                    <div class="col-auto">
                                        <i class="feather icon-calendar fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="mb-1">{{ $stats['overdue_tasks'] }}</h4>
                                        <span>المهام المتأخرة</span>
                                    </div>
                                    <div class="col-auto">
                                        <i class="feather icon-alert-triangle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Overview -->
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="feather icon-trending-up text-primary"></i>
                                    نظرة عامة على التقدم
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <div class="progress-circle" data-percent="{{ $stats['completion_rate'] }}">
                                            <div class="progress-circle-inner">
                                                <span class="progress-circle-number">{{ $stats['completion_rate'] }}%</span>
                                                <span class="progress-circle-label">معدل الإنجاز</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="stat-item">
                                                    <h6 class="text-muted">قيد التنفيذ</h6>
                                                    <h4 class="text-info">{{ $stats['in_progress_tasks'] }}</h4>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="stat-item">
                                                    <h6 class="text-muted">معلقة</h6>
                                                    <h4 class="text-warning">{{ $stats['pending_tasks'] }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted">المهام المكتملة هذا الشهر</span>
                                                    <span class="badge badge-success">{{ $completedThisMonth->count() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="feather icon-navigation text-primary"></i>
                                    التنقل السريع
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('volunteer.calendar') }}" class="btn btn-outline-primary">
                                        <i class="feather icon-calendar me-2"></i>
                                        التقويم التفاعلي
                                    </a>
                                    <a href="{{ route('volunteer.upcoming-tasks') }}" class="btn btn-outline-success">
                                        <i class="feather icon-clock me-2"></i>
                                        المهام القادمة
                                    </a>
                                    <a href="{{ route('volunteer.statistics') }}" class="btn btn-outline-info">
                                        <i class="feather icon-bar-chart-2 me-2"></i>
                                        الإحصائيات الشخصية
                                    </a>
                                    <a href="{{ route('tasks.index') }}" class="btn btn-outline-warning">
                                        <i class="feather icon-list me-2"></i>
                                        جميع المهام
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Tasks -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="feather icon-clock text-warning"></i>
                                    المهام القادمة
                                </h5>
                                <div class="card-header-right">
                                    <a href="{{ route('volunteer.upcoming-tasks') }}" class="btn btn-sm btn-primary">
                                        عرض الكل
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($upcomingTasks->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>المهمة</th>
                                                    <th>المشروع</th>
                                                    <th>الموعد النهائي</th>
                                                    <th>الأولوية</th>
                                                    <th>الحالة</th>
                                                    <th>الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($upcomingTasks->take(5) as $task)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $task->title }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                                        </td>
                                                        <td>
                                                            @if($task->project)
                                                                <span class="badge badge-info">{{ $task->project->name }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($task->deadline)
                                                                <span class="text-{{ $task->deadline->isPast() ? 'danger' : 'success' }}">
                                                                    {{ $task->deadline->format('Y-m-d') }}
                                                                </span>
                                                                <br>
                                                                <small class="text-muted">
                                                                    {{ $task->deadline->diffForHumans() }}
                                                                </small>
                                                            @else
                                                                <span class="text-muted">غير محدد</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($task->priority)
                                                                <span class="badge badge-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info') }}">
                                                                    {{ $task->priority }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $statusColors = [
                                                                    'completed' => 'success',
                                                                    'pending' => 'warning',
                                                                    'in_progress' => 'info',
                                                                    'cancelled' => 'secondary',
                                                                    'new' => 'primary',
                                                                ];
                                                                $statusLabel = [
                                                                    'completed' => 'منجزة',
                                                                    'pending' => 'معلقة',
                                                                    'in_progress' => 'قيد التنفيذ',
                                                                    'cancelled' => 'ملغاة',
                                                                    'new' => 'جديدة',
                                                                ][$task->status] ?? $task->status;
                                                            @endphp
                                                            <span class="badge badge-{{ $statusColors[$task->status] ?? 'primary' }}">
                                                                {{ $statusLabel }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="feather icon-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="feather icon-check-circle fa-3x text-success mb-3"></i>
                                        <h5>لا توجد مهام قادمة</h5>
                                        <p class="text-muted">جميع مهامك محدثة ومكتملة!</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overdue Tasks Alert -->
                @if($overdueTasks->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="feather icon-alert-triangle"></i>
                                        تنبيه: مهام متأخرة
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <strong>تحذير!</strong> لديك {{ $overdueTasks->count() }} مهام متأخرة. يرجى مراجعتها وإكمالها في أقرب وقت ممكن.
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>المهمة</th>
                                                    <th>الموعد النهائي</th>
                                                    <th>الأيام المتأخرة</th>
                                                    <th>الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($overdueTasks->take(3) as $task)
                                                    <tr>
                                                        <td>{{ $task->title }}</td>
                                                        <td>{{ $task->deadline->format('Y-m-d') }}</td>
                                                        <td>
                                                            <span class="badge badge-danger">
                                                                {{ $task->deadline->diffInDays(now()) }} يوم
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-danger">
                                                                <i class="feather icon-eye"></i>
                                                                عرض
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.progress-circle {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto;
}

.progress-circle-inner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.progress-circle-number {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
}

.progress-circle-label {
    display: block;
    font-size: 12px;
    color: #6c757d;
}

.stat-item {
    text-align: center;
    padding: 15px 0;
}

.stat-item h6 {
    margin-bottom: 5px;
}

.stat-item h4 {
    margin-bottom: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // إضافة تأثيرات بصرية للبطاقات
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
});
</script>
@endsection 