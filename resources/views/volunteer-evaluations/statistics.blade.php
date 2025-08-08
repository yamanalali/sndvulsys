<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إحصائيات تقييمات المتطوعين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .badge { font-size: 0.75em; }
        .bg-primary { background-color: #007bff !important; }
        .bg-success { background-color: #28a745 !important; }
        .bg-warning { background-color: #ffc107 !important; color: #212529 !important; }
        .bg-info { background-color: #17a2b8 !important; }
        .bg-danger { background-color: #dc3545 !important; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">إحصائيات تقييمات المتطوعين</h4>
                            <p class="card-subtitle mb-0">نظرة عامة على تقييمات المتطوعين</p>
                        </div>
                        <div>
                            <a href="{{ route('volunteer-evaluations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> العودة للتقييمات
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- إحصائيات عامة -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $stats['total_evaluations'] ?? 0 }}</h3>
                                        <p class="mb-0">إجمالي التقييمات</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $stats['completed_evaluations'] ?? 0 }}</h3>
                                        <p class="mb-0">التقييمات المكتملة</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $stats['pending_evaluations'] ?? 0 }}</h3>
                                        <p class="mb-0">التقييمات المعلقة</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ number_format($stats['average_score'] ?? 0, 1) }}</h3>
                                        <p class="mb-0">متوسط التقييم</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- إحصائيات الموافقة والرفض -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $stats['approved_evaluations'] ?? 0 }}</h3>
                                        <p class="mb-0">التقييمات الموافق عليها</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $stats['rejected_evaluations'] ?? 0 }}</h3>
                                        <p class="mb-0">التقييمات المرفوضة</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- رسوم بيانية -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>توزيع التقييمات حسب الحالة</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="statusChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>توزيع التقييمات حسب النتيجة</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="resultChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- جدول آخر التقييمات -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>آخر التقييمات</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>اسم المتطوع</th>
                                                        <th>المقيّم</th>
                                                        <th>التقييم العام</th>
                                                        <th>التوصية</th>
                                                        <th>الحالة</th>
                                                        <th>تاريخ التقييم</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $evaluations = \App\Models\VolunteerEvaluation::with(['volunteerRequest.user', 'evaluator'])->latest()->take(10)->get();
                                                    @endphp
                                                    @forelse($evaluations as $evaluation)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $evaluation->volunteerRequest->full_name ?? 'غير محدد' }}</td>
                                                        <td>{{ $evaluation->evaluator->name ?? 'غير محدد' }}</td>
                                                        <td>
                                                            <span class="badge bg-success">
                                                                {{ number_format($evaluation->overall_score, 1) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $evaluation->getRecommendationText() }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $evaluation->status === 'completed' ? 'success' : 'warning' }}">
                                                                {{ $evaluation->getStatusText() }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $evaluation->evaluation_date ? \Carbon\Carbon::parse($evaluation->evaluation_date)->format('Y-m-d') : 'غير محدد' }}</td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">لا توجد تقييمات متاحة</td>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // رسم بياني لتوزيع الحالات
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['مكتملة', 'معلقة'],
                datasets: [{
                    data: [{{ $stats['completed_evaluations'] ?? 0 }}, {{ $stats['pending_evaluations'] ?? 0 }}],
                    backgroundColor: ['#28a745', '#ffc107'],
                    borderWidth: 2
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

        // رسم بياني لتوزيع النتائج
        const resultCtx = document.getElementById('resultChart').getContext('2d');
        new Chart(resultCtx, {
            type: 'doughnut',
            data: {
                labels: ['موافق عليها', 'مرفوضة'],
                datasets: [{
                    data: [{{ $stats['approved_evaluations'] ?? 0 }}, {{ $stats['rejected_evaluations'] ?? 0 }}],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderWidth: 2
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
</body>
</html> 