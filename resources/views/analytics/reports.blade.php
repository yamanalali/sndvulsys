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
                                        <i class="feather-file-text me-2"></i>
                                        تقارير الأداء المفصلة
                                    </h2>
                                    <p class="text-muted mb-0">تقارير شاملة ومفصلة لأداء المهام والمشاريع</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-success" onclick="exportToExcel()">
                                        <i class="feather-download me-2"></i>
                                        تصدير Excel
                                    </button>
                                    <button class="btn btn-outline-primary" onclick="exportToPDF()">
                                        <i class="feather-file me-2"></i>
                                        تصدير PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- فلاتر التقرير -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-filter me-2"></i>
                                فلاتر التقرير
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">نوع التقرير</label>
                                    <select class="form-select" id="reportType" onchange="loadReport()">
                                        <option value="performance">تقرير الأداء العام</option>
                                        <option value="completion">تقرير معدل الإنجاز</option>
                                        <option value="efficiency">تقرير الكفاءة</option>
                                        <option value="trends">تقرير الاتجاهات</option>
                                        <option value="projects">تقرير المشاريع</option>
                                        <option value="team">تقرير الفريق</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">الفترة الزمنية</label>
                                    <select class="form-select" id="timeRange" onchange="loadReport()">
                                        <option value="30">آخر 30 يوم</option>
                                        <option value="90">آخر 3 أشهر</option>
                                        <option value="180">آخر 6 أشهر</option>
                                        <option value="365">آخر سنة</option>
                                        <option value="all">جميع الفترات</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">من تاريخ</label>
                                    <input type="date" class="form-control" id="startDate" onchange="loadReport()">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">إلى تاريخ</label>
                                    <input type="date" class="form-control" id="endDate" onchange="loadReport()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- محتوى التقرير -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-bar-chart-2 me-2"></i>
                                <span id="reportTitle">تقرير الأداء العام</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="reportContent">
                                <!-- سيتم تحميل محتوى التقرير هنا -->
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">جاري التحميل...</span>
                                    </div>
                                    <p class="mt-3 text-muted">جاري تحميل التقرير...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// تحميل التقرير عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    loadReport();
});

// دالة تحميل التقرير
function loadReport() {
    const reportType = document.getElementById('reportType').value;
    const timeRange = document.getElementById('timeRange').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    // تحديث عنوان التقرير
    const reportTitles = {
        'performance': 'تقرير الأداء العام',
        'completion': 'تقرير معدل الإنجاز',
        'efficiency': 'تقرير الكفاءة',
        'trends': 'تقرير الاتجاهات',
        'projects': 'تقرير المشاريع',
        'team': 'تقرير الفريق'
    };
    document.getElementById('reportTitle').textContent = reportTitles[reportType];

    // إظهار مؤشر التحميل
    document.getElementById('reportContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
            <p class="mt-3 text-muted">جاري تحميل التقرير...</p>
        </div>
    `;

    // جلب بيانات التقرير
    fetch(`/analytics/export?type=${reportType}&time_range=${timeRange}&start_date=${startDate}&end_date=${endDate}`)
        .then(response => response.json())
        .then(data => {
            displayReport(reportType, data);
        })
        .catch(error => {
            console.error('Error loading report:', error);
            document.getElementById('reportContent').innerHTML = `
                <div class="text-center py-5">
                    <i class="feather-alert-triangle text-danger" style="font-size: 48px;"></i>
                    <p class="mt-3 text-danger">حدث خطأ أثناء تحميل التقرير</p>
                </div>
            `;
        });
}

// دالة عرض التقرير
function displayReport(type, data) {
    let content = '';

    switch (type) {
        case 'performance':
            content = generatePerformanceReport(data);
            break;
        case 'completion':
            content = generateCompletionReport(data);
            break;
        case 'efficiency':
            content = generateEfficiencyReport(data);
            break;
        case 'trends':
            content = generateTrendsReport(data);
            break;
        case 'projects':
            content = generateProjectsReport(data);
            break;
        case 'team':
            content = generateTeamReport(data);
            break;
    }

    document.getElementById('reportContent').innerHTML = content;
}

// تقرير الأداء العام
function generatePerformanceReport(data) {
    return `
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">إحصائيات المهام</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary">${data.total_tasks || 0}</h4>
                                <small class="text-muted">إجمالي المهام</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">${data.completed_tasks || 0}</h4>
                                <small class="text-muted">المكتملة</small>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-warning">${data.in_progress_tasks || 0}</h4>
                                <small class="text-muted">قيد التنفيذ</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-danger">${data.overdue_tasks || 0}</h4>
                                <small class="text-muted">المتأخرة</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">معدل الإنجاز</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="position-relative d-inline-block">
                            <canvas id="performanceChart" width="150" height="150"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <h3 class="mb-0">${data.completion_rate || 0}%</h3>
                                <small class="text-muted">معدل الإنجاز</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// تقرير معدل الإنجاز
function generateCompletionReport(data) {
    let monthlyContent = '';
    if (data.monthly && data.monthly.length > 0) {
        monthlyContent = `
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>الشهر</th>
                            <th>المهام المكتملة</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.monthly.map(item => `
                            <tr>
                                <td>${getMonthName(item.month)}</td>
                                <td><span class="badge bg-success">${item.completed_count}</span></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    return `
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">معدل الإنجاز الشهري</h6>
                    </div>
                    <div class="card-body">
                        ${monthlyContent}
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">معدل الإنجاز حسب المشروع</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>المشروع</th>
                                        <th>معدل الإنجاز</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${(data.by_project || []).map(item => `
                                        <tr>
                                            <td>${item.project ? item.project.name : 'بدون مشروع'}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 100px; height: 8px;">
                                                        <div class="progress-bar bg-success" style="width: ${item.completion_rate}%"></div>
                                                    </div>
                                                    <span class="small">${item.completion_rate}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// تقرير الكفاءة
function generateEfficiencyReport(data) {
    return `
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border text-center">
                    <div class="card-body">
                        <i class="feather-clock text-primary" style="font-size: 48px;"></i>
                        <h4 class="mt-3 text-primary">${data.on_time_rate || 0}%</h4>
                        <p class="text-muted">معدل الإنجاز في الوقت المحدد</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border text-center">
                    <div class="card-body">
                        <i class="feather-activity text-success" style="font-size: 48px;"></i>
                        <h4 class="mt-3 text-success">${data.tasks_per_day || 0}</h4>
                        <p class="text-muted">معدل الإنتاجية (مهام/يوم)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border text-center">
                    <div class="card-body">
                        <i class="feather-alert-triangle text-warning" style="font-size: 48px;"></i>
                        <h4 class="mt-3 text-warning">${data.avg_delay_days || 0}</h4>
                        <p class="text-muted">متوسط أيام التأخير</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card border">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">تفاصيل الكفاءة</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-6">
                                <h5 class="text-success">${data.on_time_completed || 0}</h5>
                                <p class="text-muted">مهام مكتملة في الوقت المحدد</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-info">${data.total_with_deadline || 0}</h5>
                                <p class="text-muted">إجمالي المهام مع موعد نهائي</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// تقرير الاتجاهات
function generateTrendsReport(data) {
    return `
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">متوسط وقت الإنجاز</h6>
                    </div>
                    <div class="card-body text-center">
                        <i class="feather-calendar text-primary" style="font-size: 48px;"></i>
                        <h3 class="mt-3 text-primary">${data.avg_completion_time || 0} يوم</h3>
                        <p class="text-muted">متوسط الوقت المستغرق لإنجاز المهام</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">اتجاه المهام المكتملة</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="trendsChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// تقرير المشاريع
function generateProjectsReport(data) {
    if (!data || data.length === 0) {
        return `
            <div class="text-center py-5">
                <i class="feather-folder text-muted" style="font-size: 48px;"></i>
                <p class="mt-3 text-muted">لا توجد مشاريع متاحة</p>
            </div>
        `;
    }

    return `
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
                    ${data.map(project => `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                                    <span class="fw-medium">${project.name}</span>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary">${project.total_tasks}</span></td>
                            <td><span class="badge bg-success">${project.completed_tasks}</span></td>
                            <td><span class="badge bg-danger">${project.overdue_tasks}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 100px; height: 8px;">
                                        <div class="progress-bar bg-success" style="width: ${project.completion_rate}%"></div>
                                    </div>
                                    <span class="small">${project.completion_rate}%</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge ${getStatusBadgeClass(project.status)}">${getStatusLabel(project.status)}</span>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// تقرير الفريق
function generateTeamReport(data) {
    if (!data || data.length === 0) {
        return `
            <div class="text-center py-5">
                <i class="feather-users text-muted" style="font-size: 48px;"></i>
                <p class="mt-3 text-muted">لا يوجد أعضاء فريق متاحين</p>
            </div>
        `;
    }

    return `
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
                    ${data.map(member => `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px;">
                                        ${member.name.charAt(0).toUpperCase()}
                                    </div>
                                    <span class="fw-medium">${member.name}</span>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary">${member.total_tasks}</span></td>
                            <td><span class="badge bg-success">${member.completed_tasks}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 100px; height: 8px;">
                                        <div class="progress-bar bg-success" style="width: ${member.completion_rate}%"></div>
                                    </div>
                                    <span class="small">${member.completion_rate}%</span>
                                </div>
                            </td>
                            <td>
                                <span class="${getRatingClass(member.completion_rate)}">
                                    <i class="${getRatingIcon(member.completion_rate)} me-1"></i>
                                    ${getRatingText(member.completion_rate)}
                                </span>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// دوال مساعدة
function getMonthName(month) {
    const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    return months[month - 1] || 'غير محدد';
}

function getStatusBadgeClass(status) {
    const classes = {
        'active': 'bg-success',
        'completed': 'bg-primary',
        'on_hold': 'bg-warning',
        'cancelled': 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
}

function getStatusLabel(status) {
    const labels = {
        'active': 'نشط',
        'completed': 'مكتمل',
        'on_hold': 'معلق',
        'cancelled': 'ملغي'
    };
    return labels[status] || 'غير محدد';
}

function getRatingClass(rate) {
    if (rate >= 90) return 'text-success';
    if (rate >= 75) return 'text-primary';
    if (rate >= 60) return 'text-warning';
    return 'text-danger';
}

function getRatingIcon(rate) {
    if (rate >= 90) return 'feather-star';
    if (rate >= 75) return 'feather-thumbs-up';
    if (rate >= 60) return 'feather-check';
    return 'feather-alert-circle';
}

function getRatingText(rate) {
    if (rate >= 90) return 'ممتاز';
    if (rate >= 75) return 'جيد';
    if (rate >= 60) return 'مقبول';
    return 'يحتاج تحسين';
}

// دوال التصدير
function exportToExcel() {
    alert('سيتم إضافة ميزة تصدير Excel قريباً');
}

function exportToPDF() {
    alert('سيتم إضافة ميزة تصدير PDF قريباً');
}
</script>
@endsection 