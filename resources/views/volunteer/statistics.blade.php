@extends('layouts.master')

@section('title', 'الإحصائيات الشخصية')

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
                                        <i class="feather icon-bar-chart-2"></i>
                                        الإحصائيات الشخصية
                                    </h4>
                                    <span class="text-muted">تحليل أدائك وإنجازاتك</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="page-header-breadcrumb">
                                <ul class="breadcrumb-title">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('volunteer.dashboard') }}">
                                            <i class="feather icon-home"></i>
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <span>الإحصائيات الشخصية</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task Statistics Overview -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="mb-1">{{ $taskStats['total'] }}</h4>
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
                                        <h4 class="mb-1">{{ $taskStats['completed'] }}</h4>
                                        <span>مكتملة</span>
                                    </div>
                                    <div class="col-auto">
                                        <i class="feather icon-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="mb-1">{{ $taskStats['in_progress'] }}</h4>
                                        <span>قيد التنفيذ</span>
                                    </div>
                                    <div class="col-auto">
                                        <i class="feather icon-play-circle fa-2x"></i>
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
                                        <h4 class="mb-1">{{ $taskStats['pending'] }}</h4>
                                        <span>معلقة</span>
                                    </div>
                                    <div class="col-auto">
                                        <i class="feather icon-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="feather icon-trending-up text-primary"></i>
                                    الأداء الشهري
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="feather icon-pie-chart text-primary"></i>
                                    توزيع المهام
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="taskDistributionChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Time Statistics -->
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="feather icon-clock text-primary"></i>
                                    إحصائيات الوقت
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="stat-item">
                                            <h3 class="text-primary">{{ $timeStats['avg_completion_days'] }}</h3>
                                            <span class="text-muted">متوسط أيام الإنجاز</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-item">
                                            <h3 class="text-success">{{ $timeStats['tasks_completed_today'] }}</h3>
                                            <span class="text-muted">مكتملة اليوم</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-item">
                                            <h3 class="text-info">{{ $timeStats['tasks_completed_this_week'] }}</h3>
                                            <span class="text-muted">مكتملة هذا الأسبوع</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-item">
                                            <h3 class="text-warning">{{ $timeStats['tasks_completed_this_month'] }}</h3>
                                            <span class="text-muted">مكتملة هذا الشهر</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="feather icon-target text-primary"></i>
                                    معدل الإنجاز
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="progress-circle-large" data-percent="{{ $taskStats['completion_rate'] }}">
                                    <div class="progress-circle-inner">
                                        <h2 class="progress-circle-number">{{ $taskStats['completion_rate'] }}%</h2>
                                        <span class="progress-circle-label">معدل الإنجاز</span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">المهام المكتملة</small>
                                            <h6 class="text-success">{{ $taskStats['completed'] }}</h6>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">إجمالي المهام</small>
                                            <h6 class="text-primary">{{ $taskStats['total'] }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Statistics -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="feather icon-folder text-primary"></i>
                                    إحصائيات المشاريع
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($projectStats->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>المشروع</th>
                                                    <th>عدد المهام</th>
                                                    <th>المهام المكتملة</th>
                                                    <th>نسبة الإنجاز</th>
                                                    <th>الحالة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($projectStats as $project)
                                                    @php
                                                        $completedTasks = $project->tasks->where('status', 'completed')->count();
                                                        $totalTasks = $project->tasks->count();
                                                        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $project->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-info">{{ $totalTasks }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-success">{{ $completedTasks }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="progress" style="height: 6px;">
                                                                <div class="progress-bar bg-success" style="width: {{ $completionRate }}%"></div>
                                                            </div>
                                                            <small class="text-muted">{{ $completionRate }}%</small>
                                                        </td>
                                                        <td>
                                                            @if($completionRate == 100)
                                                                <span class="badge badge-success">مكتمل</span>
                                                            @elseif($completionRate > 50)
                                                                <span class="badge badge-info">قيد التنفيذ</span>
                                                            @else
                                                                <span class="badge badge-warning">معلق</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="feather icon-folder fa-3x text-muted mb-3"></i>
                                        <h5>لا توجد مشاريع</h5>
                                        <p class="text-muted">لم يتم تعيينك لأي مشاريع بعد</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Insights -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="feather icon-lightbulb text-primary"></i>
                                    رؤى الأداء
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="insight-item">
                                            <div class="insight-icon bg-success">
                                                <i class="feather icon-check-circle"></i>
                                            </div>
                                            <div class="insight-content">
                                                <h6>نقاط القوة</h6>
                                                <ul class="list-unstyled">
                                                    @if($taskStats['completion_rate'] > 80)
                                                        <li>✅ معدل إنجاز ممتاز</li>
                                                    @endif
                                                    @if($timeStats['tasks_completed_this_week'] > 5)
                                                        <li>✅ نشاط أسبوعي جيد</li>
                                                    @endif
                                                    @if($taskStats['overdue'] == 0)
                                                        <li>✅ لا توجد مهام متأخرة</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="insight-item">
                                            <div class="insight-icon bg-warning">
                                                <i class="feather icon-alert-triangle"></i>
                                            </div>
                                            <div class="insight-content">
                                                <h6>مجالات التحسين</h6>
                                                <ul class="list-unstyled">
                                                    @if($taskStats['completion_rate'] < 70)
                                                        <li>⚠️ تحسين معدل الإنجاز</li>
                                                    @endif
                                                    @if($taskStats['overdue'] > 0)
                                                        <li>⚠️ إكمال المهام المتأخرة</li>
                                                    @endif
                                                    @if($timeStats['avg_completion_days'] > 7)
                                                        <li>⚠️ تسريع إنجاز المهام</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress-circle-large {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 0 auto;
    border-radius: 50%;
    background: conic-gradient(#28a745 0deg, #28a745 {{ $taskStats['completion_rate'] * 3.6 }}deg, #e9ecef {{ $taskStats['completion_rate'] * 3.6 }}deg, #e9ecef 360deg);
}

.progress-circle-inner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    background: white;
    width: 160px;
    height: 160px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.progress-circle-number {
    font-size: 32px;
    font-weight: bold;
    color: #007bff;
    margin: 0;
}

.progress-circle-label {
    font-size: 14px;
    color: #6c757d;
}

.stat-item {
    padding: 20px 0;
}

.stat-item h3 {
    margin-bottom: 5px;
}

.insight-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
}

.insight-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 15px;
    flex-shrink: 0;
}

.insight-icon i {
    color: white;
    font-size: 20px;
}

.insight-content h6 {
    margin-bottom: 10px;
    font-weight: bold;
}

.insight-content ul li {
    margin-bottom: 5px;
    font-size: 14px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Performance Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json(collect($monthlyStats)->pluck('month')),
            datasets: [{
                label: 'المهام المكتملة',
                data: @json(collect($monthlyStats)->pluck('completed')),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Task Distribution Chart
    const distributionCtx = document.getElementById('taskDistributionChart').getContext('2d');
    const distributionChart = new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['مكتملة', 'قيد التنفيذ', 'معلقة', 'متأخرة'],
            datasets: [{
                data: [
                    {{ $taskStats['completed'] }},
                    {{ $taskStats['in_progress'] }},
                    {{ $taskStats['pending'] }},
                    {{ $taskStats['overdue'] }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#17a2b8',
                    '#ffc107',
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
});
</script>
@endsection 