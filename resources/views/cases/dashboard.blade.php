@extends('layouts.app')

@section('title', 'لوحة تحكم إدارة الحالات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- إحصائيات سريعة -->
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $statistics['total'] }}</h4>
                            <p class="mb-0">إجمالي الحالات</p>
                        </div>
                        <div>
                            <i class="fas fa-folder fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $statistics['pending'] }}</h4>
                            <p class="mb-0">معلق</p>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $statistics['approved'] }}</h4>
                            <p class="mb-0">موافق عليه</p>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $statistics['overdue'] }}</h4>
                            <p class="mb-0">متأخر</p>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- الحالات الحديثة -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>الحالات الحديثة</h5>
                </div>
                <div class="card-body">
                    @if($recentCases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>المتطوع</th>
                                        <th>الحالة</th>
                                        <th>التاريخ</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentCases as $case)
                                    <tr>
                                        <td>{{ $case->full_name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $case->getStatusColorAttribute() }}">
                                                {{ $case->getStatusTextAttribute() }}
                                            </span>
                                        </td>
                                        <td>{{ $case->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('case-management.show', $case->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">لا توجد حالات حديثة</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- الحالات العاجلة -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>الحالات العاجلة</h5>
                </div>
                <div class="card-body">
                    @if($urgentCases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>المتطوع</th>
                                        <th>الأولوية</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($urgentCases as $case)
                                    <tr>
                                        <td>{{ $case->full_name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $case->getPriorityColorAttribute() }}">
                                                {{ $case->getPriorityTextAttribute() }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($case->due_date)
                                                <span class="{{ $case->isOverdue() ? 'text-danger' : '' }}">
                                                    {{ $case->due_date->format('Y-m-d') }}
                                                </span>
                                            @else
                                                <span class="text-muted">غير محدد</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('case-management.show', $case->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-exclamation"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">لا توجد حالات عاجلة</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- الحالات المتأخرة -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>الحالات المتأخرة</h5>
                </div>
                <div class="card-body">
                    @if($overdueCases->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>المتطوع</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>المعني</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overdueCases as $case)
                                    <tr class="table-danger">
                                        <td>{{ $case->full_name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $case->getStatusColorAttribute() }}">
                                                {{ $case->getStatusTextAttribute() }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-danger">
                                                {{ $case->due_date ? $case->due_date->format('Y-m-d') : 'غير محدد' }}
                                            </span>
                                        </td>
                                        <td>{{ $case->assignedTo ? $case->assignedTo->name : 'غير محدد' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('case-management.show', $case->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="updateCaseStatus({{ $case->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-success">لا توجد حالات متأخرة</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات إضافية -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>معدل الإكمال</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $statistics['completion_rate'] }}%"
                             aria-valuenow="{{ $statistics['completion_rate'] }}" 
                             aria-valuemin="0" aria-valuemax="100">
                            {{ $statistics['completion_rate'] }}%
                        </div>
                    </div>
                    <p class="text-muted">معدل إكمال الحالات</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>متوسط وقت الإكمال</h5>
                </div>
                <div class="card-body">
                    <h3>{{ $statistics['avg_completion_time'] }} يوم</h3>
                    <p class="text-muted">متوسط الوقت المطلوب لإكمال الحالة</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تحديث حالة الحالة -->
<div class="modal fade" id="updateCaseStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحديث حالة الحالة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateCaseStatusForm">
                    <div class="mb-3">
                        <label class="form-label">الحالة الجديدة</label>
                        <select name="status" class="form-control" required>
                            <option value="pending">معلق</option>
                            <option value="in_progress">قيد التقدم</option>
                            <option value="under_review">قيد المراجعة</option>
                            <option value="approved">موافق عليه</option>
                            <option value="rejected">مرفوض</option>
                            <option value="needs_revision">يحتاج مراجعة</option>
                            <option value="completed">مكتمل</option>
                            <option value="cancelled">ملغي</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تعيين إلى</label>
                        <select name="assigned_to" class="form-control">
                            <option value="">اختر المراجع</option>
                            <!-- سيتم إضافة المراجعين هنا -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاريخ الاستحقاق</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="submitCaseStatusUpdate()">حفظ</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentCaseId = null;

function updateCaseStatus(caseId) {
    currentCaseId = caseId;
    $('#updateCaseStatusModal').modal('show');
}

function submitCaseStatusUpdate() {
    const formData = new FormData(document.getElementById('updateCaseStatusForm'));
    
    fetch(`/case-management/${currentCaseId}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ أثناء تحديث الحالة');
        }
    });
}
</script>
@endsection 