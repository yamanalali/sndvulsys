<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إحصائيات قرارات الموافقة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .chart-container { position: relative; height: 300px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">
                            <i class="fas fa-chart-bar"></i> إحصائيات قرارات الموافقة
                        </h4>
                        <p class="card-subtitle mb-0">نظرة عامة على قرارات طلبات التطوع</p>
                    </div>
                    <div>
                        <a href="{{ route('volunteer-requests.list') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> قائمة الطلبات
                        </a>
                        <a href="{{ route('approval-decisions.statistics') }}" class="btn btn-info">
                            <i class="fas fa-gavel"></i> إحصائيات القرارات
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- إحصائيات سريعة -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stat-card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-gavel fa-2x mb-2"></i>
                                    <h3>{{ $statistics->total_decisions }}</h3>
                                    <p class="mb-0">إجمالي القرارات</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <h3>{{ $statistics->approved_decisions }}</h3>
                                    <p class="mb-0">قرارات مقبولة</p>
                                    <small>{{ $statistics->approval_rate }}%</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card bg-danger text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                                    <h3>{{ $statistics->rejected_decisions }}</h3>
                                    <p class="mb-0">قرارات مرفوضة</p>
                                    <small>{{ $statistics->rejection_rate }}%</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h3>{{ $statistics->pending_requests }}</h3>
                                    <p class="mb-0">طلبات معلقة</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- رسوم بيانية -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-pie"></i> توزيع القرارات</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="decisionsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-line"></i> القرارات الشهرية</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="monthlyChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- إحصائيات إضافية -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-info-circle"></i> معلومات إضافية</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center p-3">
                                                <h4 class="text-primary">{{ $statistics->this_month_decisions }}</h4>
                                                <p class="mb-0">قرارات هذا الشهر</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-3">
                                                <h4 class="text-info">{{ $statistics->avg_decision_time_hours }}</h4>
                                                <p class="mb-0">متوسط وقت القرار (ساعات)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-trophy"></i> أفضل المقررين</h5>
                                </div>
                                <div class="card-body">
                                    <div id="topDeciders">
                                        <!-- سيتم ملؤها بالجافاسكريبت -->
                                        <p class="text-muted text-center">جاري تحميل البيانات...</p>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // رسم بياني دائري لتوزيع القرارات
    const decisionsCtx = document.getElementById('decisionsChart').getContext('2d');
    new Chart(decisionsCtx, {
        type: 'doughnut',
        data: {
            labels: ['مقبول', 'مرفوض'],
            datasets: [{
                data: [{{ $statistics->approved_decisions }}, {{ $statistics->rejected_decisions }}],
                backgroundColor: ['#28a745', '#dc3545'],
                borderWidth: 2,
                borderColor: '#fff'
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

    // رسم بياني خطي للقرارات الشهرية
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyData = @json($statistics->monthly_stats);
    
    // تجهيز البيانات الشهرية
    const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 
                   'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    const approvedData = new Array(12).fill(0);
    const rejectedData = new Array(12).fill(0);
    
    monthlyData.forEach(stat => {
        const monthIndex = stat.month - 1;
        if (stat.decision_status === 'approved') {
            approvedData[monthIndex] = stat.count;
        } else {
            rejectedData[monthIndex] = stat.count;
        }
    });

    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'مقبول',
                data: approvedData,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            }, {
                label: 'مرفوض',
                data: rejectedData,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // تحميل أفضل المقررين
    fetch('/approval-decisions/api/top-deciders')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('topDeciders');
            if (data.length > 0) {
                let html = '';
                data.forEach((decider, index) => {
                    html += `
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge bg-primary me-2">${index + 1}</span>
                                <strong>${decider.name}</strong>
                            </div>
                            <div>
                                <span class="badge bg-success me-1">${decider.approved_count} مقبول</span>
                                <span class="badge bg-danger">${decider.rejected_count} مرفوض</span>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-muted text-center">لا توجد بيانات متاحة</p>';
            }
        })
        .catch(error => {
            console.error('Error loading top deciders:', error);
            document.getElementById('topDeciders').innerHTML = 
                '<p class="text-muted text-center">حدث خطأ في تحميل البيانات</p>';
        });
</script>
</body>
</html> 