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
                                        <i class="feather-activity me-2"></i>
                                        مقاييس الكفاءة والإنتاجية
                                    </h2>
                                    <p class="text-muted mb-0">تحليل مفصل لكفاءة العمل والإنتاجية</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-success" onclick="exportEfficiencyData()">
                                        <i class="feather-download me-2"></i>
                                        تصدير البيانات
                                    </button>
                                    <button class="btn btn-primary" onclick="refreshEfficiencyData()">
                                        <i class="feather-refresh-cw me-2"></i>
                                        تحديث البيانات
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- مؤشرات الأداء الرئيسية -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-gradient-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-1" id="onTimeRate">0%</h4>
                                    <p class="mb-0">معدل الإنجاز في الوقت المحدد</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather-clock" style="font-size: 48px; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-gradient-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-1" id="tasksPerDay">0</h4>
                                    <p class="mb-0">معدل الإنتاجية (مهام/يوم)</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather-activity" style="font-size: 48px; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-gradient-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-1" id="avgCompletionTime">0</h4>
                                    <p class="mb-0">متوسط أيام الإنجاز</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather-calendar" style="font-size: 48px; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-gradient-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-1" id="avgDelayDays">0</h4>
                                    <p class="mb-0">متوسط أيام التأخير</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather-alert-triangle" style="font-size: 48px; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تحليل الكفاءة التفصيلي -->
            <div class="row mb-4">
                <!-- رسم بياني للكفاءة الزمنية -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-trending-up me-2"></i>
                                تحليل الكفاءة الزمنية
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="efficiencyTrendChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- مؤشرات الكفاءة -->
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-target me-2"></i>
                                مؤشرات الكفاءة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">دقة الإنجاز</span>
                                    <span class="small" id="accuracyRate">0%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" id="accuracyBar" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">سرعة الإنجاز</span>
                                    <span class="small" id="speedRate">0%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" id="speedBar" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">جودة العمل</span>
                                    <span class="small" id="qualityRate">0%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" id="qualityBar" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">الالتزام بالمواعيد</span>
                                    <span class="small" id="timelinessRate">0%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" id="timelinessBar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تحليل الأداء حسب الفترات -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-bar-chart me-2"></i>
                                تحليل الأداء حسب الفترات الزمنية
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>الفترة</th>
                                            <th>المهام المكتملة</th>
                                            <th>معدل الإنجاز</th>
                                            <th>متوسط الوقت</th>
                                            <th>معدل التأخير</th>
                                            <th>التقييم</th>
                                        </tr>
                                    </thead>
                                    <tbody id="performanceTableBody">
                                        <!-- سيتم تحميل البيانات هنا -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تحليل الكفاءة حسب المشاريع -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-folder me-2"></i>
                                كفاءة الأداء حسب المشاريع
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row" id="projectEfficiencyCards">
                                <!-- سيتم تحميل بطاقات المشاريع هنا -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- توصيات تحسين الكفاءة -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-lightbulb me-2"></i>
                                توصيات تحسين الكفاءة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row" id="recommendationsContainer">
                                <!-- سيتم تحميل التوصيات هنا -->
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
// تحميل البيانات عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    loadEfficiencyData();
});

// دالة تحميل بيانات الكفاءة
function loadEfficiencyData() {
    fetch('/analytics/export?type=efficiency')
        .then(response => response.json())
        .then(data => {
            updateEfficiencyKPIs(data);
            updateEfficiencyIndicators(data);
            generateEfficiencyChart(data);
            generatePerformanceTable(data);
            generateProjectEfficiencyCards(data);
            generateRecommendations(data);
        })
        .catch(error => {
            console.error('Error loading efficiency data:', error);
        });
}

// تحديث مؤشرات الأداء الرئيسية
function updateEfficiencyKPIs(data) {
    document.getElementById('onTimeRate').textContent = data.on_time_rate + '%';
    document.getElementById('tasksPerDay').textContent = data.tasks_per_day;
    document.getElementById('avgCompletionTime').textContent = data.avg_completion_time || 0;
    document.getElementById('avgDelayDays').textContent = data.avg_delay_days;
}

// تحديث مؤشرات الكفاءة
function updateEfficiencyIndicators(data) {
    // حساب المؤشرات بناءً على البيانات
    const accuracyRate = data.on_time_rate || 0;
    const speedRate = Math.min(100, (data.tasks_per_day || 0) * 10); // تحويل إلى نسبة
    const qualityRate = Math.min(100, accuracyRate + 10); // تقدير جودة العمل
    const timelinessRate = accuracyRate;

    // تحديث النسب
    document.getElementById('accuracyRate').textContent = accuracyRate + '%';
    document.getElementById('speedRate').textContent = Math.round(speedRate) + '%';
    document.getElementById('qualityRate').textContent = Math.round(qualityRate) + '%';
    document.getElementById('timelinessRate').textContent = timelinessRate + '%';

    // تحديث أشرطة التقدم
    document.getElementById('accuracyBar').style.width = accuracyRate + '%';
    document.getElementById('speedBar').style.width = speedRate + '%';
    document.getElementById('qualityBar').style.width = qualityRate + '%';
    document.getElementById('timelinessBar').style.width = timelinessRate + '%';
}

// إنشاء رسم بياني للكفاءة
function generateEfficiencyChart(data) {
    const ctx = document.getElementById('efficiencyTrendChart').getContext('2d');
    
    // بيانات وهمية للرسم البياني (يمكن استبدالها ببيانات حقيقية)
    const chartData = {
        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
        datasets: [{
            label: 'معدل الإنجاز في الوقت المحدد',
            data: [85, 88, 92, 87, 90, 89],
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'معدل الإنتاجية',
            data: [2.1, 2.3, 2.5, 2.2, 2.4, 2.6],
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y1'
        }]
    };

    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    max: 5,
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return value + ' مهام/يوم';
                        }
                    }
                }
            }
        }
    });
}

// إنشاء جدول الأداء
function generatePerformanceTable(data) {
    const tableBody = document.getElementById('performanceTableBody');
    
    // بيانات وهمية للجدول
    const performanceData = [
        { period: 'آخر أسبوع', completed: 15, rate: 85, avgTime: 2.1, delayRate: 15, rating: 'ممتاز' },
        { period: 'آخر شهر', completed: 45, rate: 78, avgTime: 2.8, delayRate: 22, rating: 'جيد' },
        { period: 'آخر 3 أشهر', completed: 120, rate: 82, avgTime: 2.5, delayRate: 18, rating: 'ممتاز' },
        { period: 'آخر 6 أشهر', completed: 240, rate: 79, avgTime: 2.9, delayRate: 21, rating: 'جيد' }
    ];

    tableBody.innerHTML = performanceData.map(item => `
        <tr>
            <td><strong>${item.period}</strong></td>
            <td><span class="badge bg-success">${item.completed}</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="progress me-2" style="width: 100px; height: 8px;">
                        <div class="progress-bar bg-success" style="width: ${item.rate}%"></div>
                    </div>
                    <span class="small">${item.rate}%</span>
                </div>
            </td>
            <td><span class="text-info">${item.avgTime} يوم</span></td>
            <td><span class="text-warning">${item.delayRate}%</span></td>
            <td>
                <span class="badge ${getRatingBadgeClass(item.rating)}">${item.rating}</span>
            </td>
        </tr>
    `).join('');
}

// إنشاء بطاقات كفاءة المشاريع
function generateProjectEfficiencyCards(data) {
    const container = document.getElementById('projectEfficiencyCards');
    
    // بيانات وهمية للمشاريع
    const projects = [
        { name: 'مشروع تطوير الموقع', efficiency: 92, tasks: 25, avgTime: 1.8 },
        { name: 'مشروع تطبيق الجوال', efficiency: 87, tasks: 18, avgTime: 2.3 },
        { name: 'مشروع قاعدة البيانات', efficiency: 95, tasks: 12, avgTime: 1.5 },
        { name: 'مشروع الأمان السيبراني', efficiency: 83, tasks: 20, avgTime: 2.7 }
    ];

    container.innerHTML = projects.map(project => `
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border">
                <div class="card-body text-center">
                    <h6 class="card-title mb-3">${project.name}</h6>
                    <div class="mb-3">
                        <h4 class="text-primary mb-1">${project.efficiency}%</h4>
                        <small class="text-muted">معدل الكفاءة</small>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="text-muted">المهام</small>
                            <div class="fw-bold">${project.tasks}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">متوسط الوقت</small>
                            <div class="fw-bold">${project.avgTime} يوم</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// إنشاء التوصيات
function generateRecommendations(data) {
    const container = document.getElementById('recommendationsContainer');
    
    const recommendations = [
        {
            icon: 'feather-clock',
            title: 'تحسين إدارة الوقت',
            description: 'زيادة معدل الإنجاز في الوقت المحدد من خلال تحسين تخطيط المهام',
            priority: 'high',
            impact: 'عالي'
        },
        {
            icon: 'feather-users',
            title: 'تعزيز العمل الجماعي',
            description: 'تحسين التعاون بين أعضاء الفريق لزيادة الإنتاجية',
            priority: 'medium',
            impact: 'متوسط'
        },
        {
            icon: 'feather-target',
            title: 'تحسين تحديد الأولويات',
            description: 'إعادة تقييم نظام تحديد أولويات المهام لتحسين الكفاءة',
            priority: 'high',
            impact: 'عالي'
        },
        {
            icon: 'feather-trending-up',
            title: 'تحليل الاتجاهات',
            description: 'مراقبة وتحليل اتجاهات الأداء لتحديد نقاط التحسين',
            priority: 'low',
            impact: 'منخفض'
        }
    ];

    container.innerHTML = recommendations.map(rec => `
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="${rec.icon} text-primary me-2"></i>
                        <h6 class="mb-0">${rec.title}</h6>
                    </div>
                    <p class="small text-muted mb-3">${rec.description}</p>
                    <div class="d-flex justify-content-between">
                        <span class="badge ${getPriorityBadgeClass(rec.priority)}">${rec.priority === 'high' ? 'عالية' : rec.priority === 'medium' ? 'متوسطة' : 'منخفضة'}</span>
                        <span class="badge bg-info">تأثير: ${rec.impact}</span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// دوال مساعدة
function getRatingBadgeClass(rating) {
    const classes = {
        'ممتاز': 'bg-success',
        'جيد': 'bg-primary',
        'مقبول': 'bg-warning',
        'ضعيف': 'bg-danger'
    };
    return classes[rating] || 'bg-secondary';
}

function getPriorityBadgeClass(priority) {
    const classes = {
        'high': 'bg-danger',
        'medium': 'bg-warning',
        'low': 'bg-success'
    };
    return classes[priority] || 'bg-secondary';
}

// دوال التصدير والتحديث
function exportEfficiencyData() {
    fetch('/analytics/export?type=efficiency')
        .then(response => response.json())
        .then(data => {
            const dataStr = JSON.stringify(data, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `efficiency_report_${new Date().toISOString().split('T')[0]}.json`;
            link.click();
            URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error exporting data:', error);
            alert('حدث خطأ أثناء تصدير البيانات');
        });
}

function refreshEfficiencyData() {
    loadEfficiencyData();
}
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
}

.bg-gradient-success {
    background: linear-gradient(45deg, #28a745, #1e7e34);
}

.bg-gradient-warning {
    background: linear-gradient(45deg, #ffc107, #e0a800);
}

.bg-gradient-danger {
    background: linear-gradient(45deg, #dc3545, #c82333);
}
</style>
@endsection 