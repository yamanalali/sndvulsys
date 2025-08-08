<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>قائمة طلبات التطوع</title>
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
                        <h4 class="card-title">قائمة طلبات التطوع</h4>
                        <p class="card-subtitle mb-0">إدارة وتتبع طلبات التطوع</p>
                    </div>
                    <div>
                        <a href="{{ route('volunteer-requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> إضافة طلب جديد
                        </a>
                        <a href="{{ route('volunteer-evaluations.index') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> التقييمات
                        </a>
                        <a href="{{ route('approval-decisions.statistics') }}" class="btn btn-success">
                            <i class="fas fa-chart-pie"></i> إحصائيات القرارات
                        </a>
                        <a href="{{ route('volunteer-requests.reset-data') }}" class="btn btn-warning" 
                           onclick="return confirm('هل أنت متأكد من إعادة تعيين البيانات التجريبية؟')">
                            <i class="fas fa-refresh"></i> إعادة تعيين البيانات
                        </a>
                        <a href="{{ route('volunteer-requests.clear-old-data') }}" class="btn btn-danger" 
                           onclick="return confirm('هل أنت متأكد من مسح البيانات القديمة؟')">
                            <i class="fas fa-trash"></i> مسح البيانات القديمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم المتطوع</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>رقم الهاتف</th>
                                    <th>المستوى التعليمي</th>
                                    <th>المجال المفضل</th>
                                    <th>حالة الطلب</th>
                                    <th>التقييم</th>
                                    <th>تاريخ التقديم</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $request)
                                <tr data-request-id="{{ $request->id }}" 
                                    data-request-name="{{ $request->full_name }}"
                                    data-request-email="{{ $request->email }}"
                                    data-request-phone="{{ $request->phone }}"
                                    data-request-education="{{ $request->education_level }} {{ $request->field_of_study }}"
                                    data-request-area="{{ $request->preferred_area }} {{ $request->availability }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $request->full_name }}</strong>
                                        @if($request->gender)
                                            <br><small class="text-muted">{{ $request->gender === 'male' ? 'ذكر' : 'أنثى' }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $request->email }}</td>
                                    <td>{{ $request->phone }}</td>
                                    <td>
                                        {{ $request->education_level }}
                                        @if($request->field_of_study)
                                            <br><small class="text-muted">{{ $request->field_of_study }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $request->preferred_area }}
                                        @if($request->availability)
                                            <br><small class="text-muted">التوفر: {{ $request->availability }}</small>
                                        @endif
                                    </td>
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
                                    <td>
                                        @if($request->hasEvaluation())
                                            @php
                                                $evaluation = $request->latestEvaluation;
                                                $scoreColor = $evaluation->overall_score >= 80 ? 'success' : ($evaluation->overall_score >= 60 ? 'warning' : 'danger');
                                            @endphp
                                            <span class="badge badge-{{ $scoreColor }}">
                                                {{ $evaluation->overall_score }}/100
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $evaluation->getScoreLevel() }}</small>
                                        @else
                                            <span class="badge badge-light">لم يتم التقييم</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $request->created_at->format('Y-m-d') }}
                                        <br>
                                        <small class="text-muted">{{ $request->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('volunteer-requests.show', $request->id) }}" 
                                               class="btn btn-sm btn-info" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if(!$request->hasEvaluation())
                                                <a href="{{ route('volunteer-evaluations.create', $request->id) }}" 
                                                   class="btn btn-sm btn-success" title="تقييم الطلب">
                                                    <i class="fas fa-star"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('volunteer-evaluations.show', $request->latestEvaluation->id) }}" 
                                                   class="btn btn-sm btn-warning" title="عرض التقييم">
                                                    <i class="fas fa-chart-line"></i>
                                                </a>
                                            @endif
                                            
                                            @if($request->status === 'pending' && !$request->approvalDecision)
                                                <button type="button" class="btn btn-sm btn-success btn-approve" 
                                                        onclick="openDecisionModal({{ $request->id }}, 'approve')" 
                                                        title="قبول الطلب">
                                                    <i class="fas fa-check"></i> قبول
                                                </button>
                                                
                                                <button type="button" class="btn btn-sm btn-danger btn-reject" 
                                                        onclick="openDecisionModal({{ $request->id }}, 'reject')" 
                                                        title="رفض الطلب">
                                                    <i class="fas fa-times"></i> رفض
                                                </button>
                                            @elseif($request->approvalDecision)
                                                <div class="d-flex flex-column align-items-start">
                                                    <span class="badge bg-{{ $request->approvalDecision->isApproved() ? 'success' : 'danger' }} mb-1">
                                                        {{ $request->approvalDecision->decision_status_text }}
                                                    </span>
                                                    <small class="text-muted">
                                                        {{ $request->approvalDecision->decision_at->format('Y-m-d H:i') }}
                                                    </small>
                                                    <small class="text-muted">
                                                        بواسطة: {{ $request->approvalDecision->decisionBy->name ?? 'غير محدد' }}
                                                    </small>
                                                </div>
                                            @endif
                                            
                                            <a href="{{ route('volunteer-requests.edit', $request->id) }}" 
                                               class="btn btn-sm btn-primary" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('volunteer-requests.destroy', $request->id) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟')" 
                                                        title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">
                                        <p class="text-muted">لا توجد طلبات تطوع متاحة</p>
                                        <a href="{{ route('volunteer-requests.create') }}" class="btn btn-primary">
                                            إضافة أول طلب تطوع
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- إحصائيات سريعة -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $requests->where('status', 'pending')->count() }}</h5>
                                    <p class="mb-0">في الانتظار</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $requests->where('status', 'approved')->count() }}</h5>
                                    <p class="mb-0">موافق عليه</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $requests->where('status', 'rejected')->count() }}</h5>
                                    <p class="mb-0">مرفوض</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $requests->filter(function($r){ return $r->hasEvaluation(); })->count() }}</h5>
                                    <p class="mb-0">تم تقييمه</p>
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
.badge {
    font-size: 0.75em;
}
.btn-group .btn {
    margin-right: 2px;
}
.table th {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}
.card {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- مودال اتخاذ القرار -->
    <div class="modal fade" id="decisionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="decisionModalTitle">
                        <i class="fas fa-gavel"></i> اتخاذ قرار
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>ملاحظة:</strong> سيتم تسجيل قرارك مع اسمك وتاريخ القرار تلقائياً.
                    </div>
                    
                    <form id="decisionForm">
                        <input type="hidden" id="requestId" name="request_id">
                        <input type="hidden" id="decisionType" name="decision_type">
                        
                        <div class="form-group mb-3">
                            <label for="decisionReason" class="form-label fw-bold">
                                سبب القرار <span class="text-danger">*</span>
                            </label>
                            <textarea id="decisionReason" name="decision_reason" class="form-control" rows="5" 
                                      placeholder="اكتب سبب القرار هنا..." required></textarea>
                            <div class="form-text">
                                <i class="fas fa-lightbulb"></i>
                                <strong>نصائح:</strong>
                                <ul class="mb-0 mt-1">
                                    <li>اكتب سبباً واضحاً ومفصلاً للقرار</li>
                                    <li>اذكر النقاط الإيجابية في حالة القبول</li>
                                    <li>اذكر النقاط التي تحتاج تحسين في حالة الرفض</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">معلومات الطلب:</label>
                            <div id="requestInfo" class="p-3 bg-light rounded">
                                <!-- سيتم ملؤها بالجافاسكريبت -->
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> إلغاء
                    </button>
                    <button type="button" class="btn btn-primary" onclick="submitDecision()" id="submitDecisionBtn">
                        <i class="fas fa-check"></i> تأكيد القرار
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentRequestId = null;
        let currentDecisionType = null;
        let currentRequestData = null;

        function openDecisionModal(requestId, decisionType) {
            console.log('Opening modal for request ID:', requestId, 'decision type:', decisionType);
            
            currentRequestId = requestId;
            currentDecisionType = decisionType;
            
            const modal = document.getElementById('decisionModal');
            const title = document.getElementById('decisionModalTitle');
            const reasonField = document.getElementById('decisionReason');
            const submitBtn = document.getElementById('submitDecisionBtn');
            
            // تحديث عنوان المودال
            if (decisionType === 'approve') {
                title.innerHTML = '<i class="fas fa-check-circle text-success"></i> قبول الطلب';
                reasonField.placeholder = 'اكتب سبب القبول... مثال: المتطوع يمتلك المهارات المطلوبة والخبرة المناسبة...';
                submitBtn.className = 'btn btn-success';
                submitBtn.innerHTML = '<i class="fas fa-check"></i> قبول الطلب';
            } else {
                title.innerHTML = '<i class="fas fa-times-circle text-danger"></i> رفض الطلب';
                reasonField.placeholder = 'اكتب سبب الرفض... مثال: لا يمتلك المتطوع المهارات المطلوبة أو الخبرة الكافية...';
                submitBtn.className = 'btn btn-danger';
                submitBtn.innerHTML = '<i class="fas fa-times"></i> رفض الطلب';
            }
            
            // مسح الحقول
            reasonField.value = '';
            
            // عرض معلومات الطلب
            showRequestInfo(requestId);
            
            // فتح المودال
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
            
            // التركيز على حقل السبب
            setTimeout(() => {
                reasonField.focus();
            }, 500);
        }

        function showRequestInfo(requestId) {
            console.log('Searching for request ID:', requestId);
            
            // البحث عن صف الطلب في الجدول باستخدام المعرف المباشر
            const targetRow = document.querySelector(`tr[data-request-id="${requestId}"]`);
            
            if (targetRow) {
                console.log('Found target row:', targetRow);
                
                // استخراج البيانات من data-attributes
                const name = targetRow.getAttribute('data-request-name') || 'غير محدد';
                const email = targetRow.getAttribute('data-request-email') || 'غير محدد';
                const phone = targetRow.getAttribute('data-request-phone') || 'غير محدد';
                const education = targetRow.getAttribute('data-request-education') || 'غير محدد';
                const area = targetRow.getAttribute('data-request-area') || 'غير محدد';
                
                // التحقق من صحة البيانات
                console.log('Raw data from attributes:', {
                    name: targetRow.getAttribute('data-request-name'),
                    email: targetRow.getAttribute('data-request-email'),
                    phone: targetRow.getAttribute('data-request-phone'),
                    education: targetRow.getAttribute('data-request-education'),
                    area: targetRow.getAttribute('data-request-area')
                });
                
                console.log('Extracted data from data-attributes:', { name, email, phone, education, area });
                
                const requestInfo = document.getElementById('requestInfo');
                requestInfo.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <strong>اسم المتطوع:</strong> ${name}<br>
                            <strong>البريد الإلكتروني:</strong> ${email}<br>
                            <strong>رقم الهاتف:</strong> ${phone}
                        </div>
                        <div class="col-md-6">
                            <strong>المستوى التعليمي:</strong> ${education}<br>
                            <strong>المجال المفضل:</strong> ${area}
                        </div>
                    </div>
                `;
            } else {
                console.log('Target row not found for ID:', requestId);
                // إذا لم يتم العثور على الصف، عرض رسالة
                const requestInfo = document.getElementById('requestInfo');
                requestInfo.innerHTML = '<div class="text-muted">جاري تحميل معلومات الطلب...</div>';
            }
        }

        function submitDecision() {
            const reason = document.getElementById('decisionReason').value.trim();
            const submitBtn = document.getElementById('submitDecisionBtn');
            
            if (!reason) {
                showAlert('يرجى كتابة سبب القرار', 'warning');
                return;
            }
            
            // تم إزالة التحقق من الحد الأدنى للأحرف
            
            // تعطيل الزر لمنع الإرسال المتكرر
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
            
            const url = currentDecisionType === 'approve' 
                ? `/approval-decisions/approve/${currentRequestId}`
                : `/approval-decisions/reject/${currentRequestId}`;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    decision_reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // إغلاق المودال
                    const modal = bootstrap.Modal.getInstance(document.getElementById('decisionModal'));
                    modal.hide();
                    
                    // إظهار رسالة نجاح
                    showAlert(data.message, 'success');
                    
                    // إعادة تحميل الصفحة بعد ثانيتين
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showAlert(data.message || 'حدث خطأ أثناء اتخاذ القرار', 'danger');
                    // إعادة تفعيل الزر
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = currentDecisionType === 'approve' 
                        ? '<i class="fas fa-check"></i> قبول الطلب'
                        : '<i class="fas fa-times"></i> رفض الطلب';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('حدث خطأ أثناء اتخاذ القرار', 'danger');
                // إعادة تفعيل الزر
                submitBtn.disabled = false;
                submitBtn.innerHTML = currentDecisionType === 'approve' 
                    ? '<i class="fas fa-check"></i> قبول الطلب'
                    : '<i class="fas fa-times"></i> رفض الطلب';
            });
        }

        function showAlert(message, type) {
            // إنشاء عنصر التنبيه
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'times-circle'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // إضافة التنبيه للصفحة
            document.body.appendChild(alertDiv);
            
            // إزالة التنبيه تلقائياً بعد 5 ثوان
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // إضافة data-request-id للصفوف
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                const requestId = row.querySelector('td:first-child')?.textContent?.trim();
                if (requestId && !isNaN(requestId)) {
                    row.setAttribute('data-request-id', requestId);
                }
            });
        });
    </script>
</body>
</html> 