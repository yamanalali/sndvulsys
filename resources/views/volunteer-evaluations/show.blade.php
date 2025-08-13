<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض التقييم - {{ $evaluation->volunteerRequest->full_name ?? 'متطوع' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .score-badge { font-size: 1.2rem; padding: 0.5rem 1rem; }
        .score-excellent { background-color: #28a745; color: white; }
        .score-good { background-color: #ffc107; color: black; }
        .score-poor { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                            <h4 class="card-title">
                                <i class="fas fa-chart-line text-info"></i> 
                                عرض التقييم - {{ $evaluation->volunteerRequest->full_name ?? 'متطوع' }}
                            </h4>
                            <p class="card-subtitle mb-0">تفاصيل التقييم الكاملة</p>
                    </div>
                    <div>
                            <a href="{{ route('volunteer-evaluations.edit', $evaluation->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <a href="{{ route('volunteer-requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> العودة
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- معلومات المتطوع -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="fas fa-user"></i> معلومات المتطوع</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>الاسم:</strong> {{ $evaluation->volunteerRequest->full_name ?? 'غير محدد' }}<br>
                                                <strong>البريد الإلكتروني:</strong> {{ $evaluation->volunteerRequest->email ?? 'غير محدد' }}<br>
                                                <strong>رقم الهاتف:</strong> {{ $evaluation->volunteerRequest->phone ?? 'غير محدد' }}<br>
                                                <strong>التخصص:</strong> {{ $evaluation->volunteerRequest->field_of_study ?? 'غير محدد' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>المهارات:</strong> {{ $evaluation->volunteerRequest->skills ?? 'غير محدد' }}<br>
                                                <strong>الدافع:</strong> {{ Str::limit($evaluation->volunteerRequest->motivation ?? 'غير محدد', 100) }}<br>
                                                <strong>التوفر:</strong> {{ $evaluation->volunteerRequest->availability ?? 'غير محدد' }}
                                            </div>
                                        </div>
                                    </div>
                    </div>
                </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                <div class="card-body">
                                        @php
                                            $scoreClass = match(true) {
                                                $evaluation->overall_score > 37 => 'score-excellent',
                                                $evaluation->overall_score >= 25 => 'score-good',
                                                default => 'score-poor'
                                            };
                                        @endphp
                                        <div class="score-badge {{ $scoreClass }} rounded">
                                            {{ $evaluation->overall_score ?? 0 }}/50
                                        </div>
                                        <h6 class="mt-2">{{ $evaluation->getScoreLevel() }}</h6>
                                        <p class="text-muted mb-0">النتيجة الإجمالية</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تفاصيل التقييم -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> معلومات التقييم</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>تاريخ التقييم:</strong></td>
                                    <td>{{ $evaluation->evaluation_date ? $evaluation->evaluation_date->format('Y-m-d') : 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                                <td><strong>التوصية:</strong></td>
                                                <td>
                                                    @php
                                                        $badgeClass = match($evaluation->recommendation) {
                                                            'accepted', 'strong_approve', 'approve' => 'success',
                                                            'training_required', 'conditional' => 'warning',
                                                            'rejected', 'reject', 'strong_reject' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $badgeClass }}">
                                                        {{ $evaluation->getRecommendationText() }}
                                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                                <td><strong>الحالة:</strong></td>
                                    <td>
                                                    <span class="badge bg-{{ $evaluation->status === 'completed' ? 'success' : 'warning' }}">
                                                        {{ $evaluation->getStatusText() }}
                                        </span>
                                    </td>
                                </tr>
                                            <tr>
                                                <td><strong>المقيم:</strong></td>
                                                <td>{{ $evaluation->evaluator->name ?? 'غير محدد' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-chart-bar"></i> تفاصيل النقاط</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small>التعريف الشخصي</small>
                                                <div class="progress mb-2">
                                                    <div class="progress-bar" style="width: {{ ($evaluation->interview_score ?? 0) * 10 }}%">{{ $evaluation->interview_score ?? 0 }}/10</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <small>الدافع للتطوع</small>
                                                <div class="progress mb-2">
                                                    <div class="progress-bar" style="width: {{ ($evaluation->skills_assessment_score ?? 0) * 10 }}%">{{ $evaluation->skills_assessment_score ?? 0 }}/10</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small>المهارات والخبرات</small>
                                                <div class="progress mb-2">
                                                    <div class="progress-bar" style="width: {{ ($evaluation->motivation_score ?? 0) * 10 }}%">{{ $evaluation->motivation_score ?? 0 }}/10</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <small>التوفر الزمني</small>
                                                <div class="progress mb-2">
                                                    <div class="progress-bar" style="width: {{ ($evaluation->availability_score ?? 0) * 10 }}%">{{ $evaluation->availability_score ?? 0 }}/10</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <small>التعامل مع التحديات</small>
                                                <div class="progress mb-2">
                                                    <div class="progress-bar" style="width: {{ ($evaluation->teamwork_score ?? 0) * 10 }}%">{{ $evaluation->teamwork_score ?? 0 }}/10</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>

                        <!-- الملاحظات -->
                    @if($evaluation->notes)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-sticky-note"></i> الملاحظات</h6>
                                    </div>
                                <div class="card-body">
                                        <p class="mb-0">{{ $evaluation->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                        <!-- الإجراءات -->
                    <div class="row">
                            <div class="col-12 text-center">
                                <a href="{{ route('volunteer-evaluations.edit', $evaluation->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> تعديل التقييم
                                </a>
                                <a href="{{ route('volunteer-requests.show', $evaluation->volunteerRequest->id) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> عرض طلب التطوع
                                </a>
                                <a href="{{ route('volunteer-requests.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-list"></i> قائمة الطلبات
                                </a>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 