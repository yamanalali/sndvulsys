<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل طلب التطوع - {{ $request->full_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">تفاصيل طلب التطوع</h4>
                        <p class="card-subtitle mb-0">معلومات المتطوع والطلب</p>
                    </div>
                    <div>
                        <a href="{{ route('volunteer-requests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> العودة للقائمة
                        </a>
                        <a href="{{ route('volunteer-requests.edit', $request->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- جدول التوفر الأسبوعي -->
                    @if($request->availabilities && $request->availabilities->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>جدول التوفر الأسبوعي</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>اليوم</th>
                                                    <th>الفترة</th>
                                                    <th>متاح</th>
                                                    <th>من</th>
                                                    <th>إلى</th>
                                                    <th>ملاحظات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($request->availabilities as $av)
                                                    <tr>
                                                        <td>{{ \App\Models\Availability::getDays()[$av->day] ?? $av->day }}</td>
                                                        <td>{{ \App\Models\Availability::getTimeSlots()[$av->time_slot] ?? ($av->time_slot ?? '—') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $av->is_available ? 'success' : 'secondary' }}">
                                                                {{ $av->is_available ? 'متاح' : 'غير متاح' }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $av->start_time ? \Carbon\Carbon::parse($av->start_time)->format('H:i') : '—' }}</td>
                                                        <td>{{ $av->end_time ? \Carbon\Carbon::parse($av->end_time)->format('H:i') : '—' }}</td>
                                                        <td>{{ $av->notes ?? '—' }}</td>
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

                    <!-- مستندات الطلب -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">مستندات الطلب</h5>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#uploadDocumentForm">
                                        <i class="fas fa-upload"></i> رفع مستند
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="uploadDocumentForm" class="collapse mb-3">
                                        <form method="POST" action="{{ route('volunteer-requests.documents.store', $request->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">العنوان</label>
                                                    <input type="text" name="title" class="form-control" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">مستوى الخصوصية</label>
                                                    <select name="privacy_level" class="form-control" required>
                                                        <option value="private">خاص</option>
                                                        <option value="restricted">مقيد</option>
                                                        <option value="public">عام</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">وصف (اختياري)</label>
                                                    <textarea name="description" rows="2" class="form-control"></textarea>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                                                    <small class="text-muted">الحد الأقصى 10MB</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="submit" class="btn btn-success w-100">
                                                        حفظ
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    @if($request->documents && $request->documents->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>العنوان</th>
                                                        <th>النوع</th>
                                                        <th>الحجم</th>
                                                        <th>الخصوصية</th>
                                                        <th>تاريخ الرفع</th>
                                                        <th>إجراءات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($request->documents as $doc)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $doc->title }}</td>
                                                            <td>{{ strtoupper($doc->file_type) }}</td>
                                                            <td>{{ $doc->formatted_size ?? number_format(($doc->file_size/1024), 1) . ' KB' }}</td>
                                                            <td><span class="badge bg-secondary">{{ $doc->privacy_level_text ?? $doc->privacy_level }}</span></td>
                                                            <td>{{ $doc->created_at->format('Y-m-d') }}</td>
                                                            <td>
                                                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <form method="POST" action="{{ route('volunteer-requests.documents.destroy', [$request->id, $doc->id]) }}" style="display:inline-block" onsubmit="return confirm('تأكيد الحذف؟');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">لا توجد مستندات مرتبطة بهذا الطلب.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- المعلومات الشخصية -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>المعلومات الشخصية</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>الاسم الكامل:</strong></td>
                                            <td>{{ $request->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>البريد الإلكتروني:</strong></td>
                                            <td>{{ $request->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>رقم الهاتف:</strong></td>
                                            <td>{{ $request->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>رقم الهوية الوطنية:</strong></td>
                                            <td>{{ $request->national_id ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>تاريخ الميلاد:</strong></td>
                                            <td>{{ $request->birth_date ? \Carbon\Carbon::parse($request->birth_date)->format('Y-m-d') : 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الجنس:</strong></td>
                                            <td>{{ $request->gender === 'male' ? 'ذكر' : ($request->gender === 'female' ? 'أنثى' : 'غير محدد') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الحالة الاجتماعية:</strong></td>
                                            <td>{{ $request->social_status === 'single' ? 'عازب/عازبة' : ($request->social_status === 'married' ? 'متزوج/متزوجة' : 'غير محدد') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- المعلومات التعليمية والمهنية -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>المعلومات التعليمية والمهنية</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>المستوى التعليمي:</strong></td>
                                            <td>{{ $request->education_level ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>التخصص الدراسي:</strong></td>
                                            <td>{{ $request->field_of_study ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>المهنة:</strong></td>
                                            <td>{{ $request->occupation ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>المهارات:</strong></td>
                                            <td>{{ $request->skills ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>اللغات:</strong></td>
                                            <td>{{ $request->languages ?? 'غير محدد' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- معلومات التطوع -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>معلومات التطوع</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>المجال المفضل:</strong></td>
                                            <td>{{ $request->preferred_area ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>التوفر:</strong></td>
                                            <td>{{ $request->availability ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الخبرة السابقة:</strong></td>
                                            <td>{{ $request->previous_experience ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>نوع المنظمة المفضلة:</strong></td>
                                            <td>{{ $request->preferred_organization_type ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>هل سبق له التطوع:</strong></td>
                                            <td>{{ $request->has_previous_volunteering ? 'نعم' : 'لا' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- حالة الطلب والتقييم -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>حالة الطلب والتقييم</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>حالة الطلب:</strong></td>
                                            <td>
                                                @php
                                                    $statusColor = match($request->status) {
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                        'withdrawn' => 'secondary',
                                                        default => 'warning'
                                                    };
                                                    $statusText = match($request->status) {
                                                        'approved' => 'موافق عليه',
                                                        'rejected' => 'مرفوض',
                                                        'withdrawn' => 'منسحب',
                                                        default => 'في الانتظار'
                                                    };
                                                @endphp
                                                <span class="badge badge-{{ $statusColor }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>تاريخ التقديم:</strong></td>
                                            <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>آخر تحديث:</strong></td>
                                            <td>{{ $request->updated_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        @if($request->reviewed_at)
                                        <tr>
                                            <td><strong>تاريخ المراجعة:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($request->reviewed_at)->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($request->hasEvaluation())
                                        <tr>
                                            <td><strong>التقييم العام:</strong></td>
                                            <td>
                                                @php
                                                    $evaluation = $request->latestEvaluation;
                                                    $scoreColor = $evaluation->overall_score >= 80 ? 'success' : ($evaluation->overall_score >= 60 ? 'warning' : 'danger');
                                                @endphp
                                                <span class="badge badge-{{ $scoreColor }}">
                                                    {{ number_format($evaluation->overall_score, 1) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($request->motivation)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>سبب التقديم</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $request->motivation }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($request->admin_notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>ملاحظات الإدارة</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $request->admin_notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($request->cv)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>السيرة الذاتية</h5>
                                </div>
                                <div class="card-body">
                                    <a href="{{ asset('storage/' . $request->cv) }}" target="_blank" class="btn btn-info">
                                        <i class="fas fa-download"></i> تحميل السيرة الذاتية
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 