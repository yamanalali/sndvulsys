@extends('layouts.master')

@section('content')
<div class="page-wrapper" dir="rtl">
    <div class="page-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1">
                                        <i class="feather-bar-chart-2 me-2"></i>
                                        تحليلات أداء المهام
                                    </h2>
                                    <p class="text-muted mb-0">تقارير الأداء ومقاييس الإنجاز والتحليلات الشاملة</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('analytics.reports') }}" class="btn btn-outline-info">
                                        <i class="feather-file-text me-2"></i>
                                        التقارير المفصلة
                                    </a>
                                    <a href="{{ route('analytics.efficiency') }}" class="btn btn-outline-success">
                                        <i class="feather-activity me-2"></i>
                                        مقاييس الكفاءة
                                    </a>
                                    <button class="btn btn-outline-primary" onclick="exportData('general')">
                                        <i class="feather-download me-2"></i>
                                        تصدير التقرير
                                    </button>
                                    <button class="btn btn-primary" onclick="refreshCharts()">
                                        <i class="feather-refresh-cw me-2"></i>
                                        تحديث البيانات
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الإحصائيات العامة -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-1">{{ $generalStats['total_tasks'] }}</h4>
                                    <p class="mb-0">إجمالي المهام</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather-list" style="font-size: 48px; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-1">{{ $generalStats['completed_tasks'] }}</h4>
                                    <p class="mb-0">المهام المكتملة</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather-check-circle" style="font-size: 48px; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-1">{{ $generalStats['in_progress_tasks'] }}</h4>
                                    <p class="mb-0">قيد التنفيذ</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather-clock" style="font-size: 48px; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-1">{{ $generalStats['overdue_tasks'] }}</h4>
                                    <p class="mb-0">المهام المتأخرة</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather-alert-triangle" style="font-size: 48px; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معدل الإنجاز العام -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-percent me-2"></i>
                                معدل الإنجاز العام
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="text-center">
                                        <div class="position-relative d-inline-block">
                                            <canvas id="completionRateChart" width="200" height="200"></canvas>
                                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                                <h3 class="mb-0">{{ $generalStats['completion_rate'] }}%</h3>
                                                <small class="text-muted">معدل الإنجاز</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <div class="text-center p-3 border rounded">
                                                <h4 class="text-success mb-1">{{ $efficiencyMetrics['on_time_rate'] }}%</h4>
                                                <small class="text-muted">في الوقت المحدد</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="text-center p-3 border rounded">
                                                <h4 class="text-info mb-1">{{ $efficiencyMetrics['tasks_per_day'] }}</h4>
                                                <small class="text-muted">مهام/يوم</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="text-center p-3 border rounded">
                                                <h4 class="text-warning mb-1">{{ $trends['avg_completion_time'] }}</h4>
                                                <small class="text-muted">أيام متوسط الإنجاز</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="text-center p-3 border rounded">
                                                <h4 class="text-danger mb-1">{{ $efficiencyMetrics['avg_delay_days'] }}</h4>
                                                <small class="text-muted">أيام متوسط التأخير</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الرسوم البيانية -->
            <div class="row mb-4">
                <!-- اتجاه الإنجاز -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-trending-up me-2"></i>
                                اتجاه الإنجاز الشهري
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyTrendChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- معدل الإنجاز حسب المشروع -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-pie-chart me-2"></i>
                                معدل الإنجاز حسب المشروع
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="projectCompletionChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- أداء المشاريع -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-folder me-2"></i>
                                أداء المشاريع
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المشروع</th>
                                            <th>إجمالي المهام</th>
                                            <th>المكتملة</th>
                                            <th>المتأخرة</th>
                                            <th>معدل الإنجاز</th>
                                            <th>الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($projectPerformance as $project)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                                                        <span class="fw-medium">{{ $project['name'] }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary fs-6">{{ $project['total_tasks'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success fs-6">{{ $project['completed_tasks'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger fs-6">{{ $project['overdue_tasks'] }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-2" style="width: 100px; height: 8px;">
                                                            <div class="progress-bar bg-success" style="width: {{ $project['completion_rate'] }}%"></div>
                                                        </div>
                                                        <span class="small">{{ $project['completion_rate'] }}%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColor = match($project['status']) {
                                                            'active' => 'bg-success',
                                                            'completed' => 'bg-primary',
                                                            'on_hold' => 'bg-warning',
                                                            'cancelled' => 'bg-danger',
                                                            default => 'bg-secondary'
                                                        };
                                                        $statusLabel = match($project['status']) {
                                                            'active' => 'نشط',
                                                            'completed' => 'مكتمل',
                                                            'on_hold' => 'معلق',
                                                            'cancelled' => 'ملغي',
                                                            default => 'غير محدد'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $statusColor }}">{{ $statusLabel }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="feather-folder" style="font-size: 48px;"></i>
                                                        <p class="mt-2">لا توجد مشاريع متاحة</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- أداء الفريق -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-users me-2"></i>
                                أداء أعضاء الفريق
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>العضو</th>
                                            <th>إجمالي المهام</th>
                                            <th>المكتملة</th>
                                            <th>معدل الإنجاز</th>
                                            <th>التقييم</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($teamPerformance as $member)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px;">
                                                            {{ strtoupper(substr($member['name'], 0, 1)) }}
                                                        </div>
                                                        <span class="fw-medium">{{ $member['name'] }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary fs-6">{{ $member['total_tasks'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success fs-6">{{ $member['completed_tasks'] }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-2" style="width: 100px; height: 8px;">
                                                            <div class="progress-bar bg-success" style="width: {{ $member['completion_rate'] }}%"></div>
                                                        </div>
                                                        <span class="small">{{ $member['completion_rate'] }}%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $rating = match(true) {
                                                            $member['completion_rate'] >= 90 => ['color' => 'text-success', 'icon' => 'feather-star', 'text' => 'ممتاز'],
                                                            $member['completion_rate'] >= 75 => ['color' => 'text-primary', 'icon' => 'feather-thumbs-up', 'text' => 'جيد'],
                                                            $member['completion_rate'] >= 60 => ['color' => 'text-warning', 'icon' => 'feather-check', 'text' => 'مقبول'],
                                                            default => ['color' => 'text-danger', 'icon' => 'feather-alert-circle', 'text' => 'يحتاج تحسين']
                                                        };
                                                    @endphp
                                                    <span class="{{ $rating['color'] }}">
                                                        <i class="{{ $rating['icon'] }} me-1"></i>
                                                        {{ $rating['text'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="feather-users" style="font-size: 48px;"></i>
                                                        <p class="mt-2">لا يوجد أعضاء فريق متاحين</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// البيانات للرسوم البيانية
const monthlyData = @json($completionRates['monthly']);
const projectData = @json($completionRates['by_project']);
const trendsData = @json($trends);

// رسم معدل الإنجاز العام
const completionRateCtx = document.getElementById('completionRateChart').getContext('2d');
const completionRateChart = new Chart(completionRateCtx, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [{{ $generalStats['completion_rate'] }}, {{ 100 - $generalStats['completion_rate'] }}],
            backgroundColor: ['#28a745', '#e9ecef'],
            borderWidth: 0
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
        cutout: '70%'
    }
});

// رسم الاتجاه الشهري
const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
const monthlyTrendChart = new Chart(monthlyTrendCtx, {
    type: 'line',
    data: {
        labels: monthlyData.map(item => {
            const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
            return months[item.month - 1];
        }),
        datasets: [{
            label: 'المهام المكتملة',
            data: monthlyData.map(item => item.completed_count),
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

// رسم معدل الإنجاز حسب المشروع
const projectCompletionCtx = document.getElementById('projectCompletionChart').getContext('2d');
const projectCompletionChart = new Chart(projectCompletionCtx, {
    type: 'bar',
    data: {
        labels: projectData.map(item => item.project ? item.project.name : 'بدون مشروع'),
        datasets: [{
            label: 'معدل الإنجاز (%)',
            data: projectData.map(item => item.completion_rate),
            backgroundColor: [
                '#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d',
                '#17a2b8', '#fd7e14', '#6f42c1', '#e83e8c', '#20c997'
            ]
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
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});

// دالة تصدير البيانات
function exportData(type) {
    fetch(`/analytics/export?type=${type}`)
        .then(response => response.json())
        .then(data => {
            const dataStr = JSON.stringify(data, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `analytics_${type}_${new Date().toISOString().split('T')[0]}.json`;
            link.click();
            URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error exporting data:', error);
            alert('حدث خطأ أثناء تصدير البيانات');
        });
}

// دالة تحديث الرسوم البيانية
function refreshCharts() {
    location.reload();
}
</script>
@endsection 