@extends('layouts.app')

@section('title', 'إدارة الحالات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">إدارة الحالات</h4>
                    <div>
                        <a href="{{ route('cases.test') }}" class="btn btn-secondary">
                            <i class="fas fa-bug"></i> اختبار النظام
                        </a>
                        <a href="{{ route('case-management.dashboard') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> لوحة التحكم
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- رسالة ترحيب -->
                    <div class="alert alert-info">
                        <h5>مرحباً بك في نظام إدارة الحالات</h5>
                        <p>هذه الصفحة تعرض جميع طلبات التطوع كحالات قابلة للإدارة والمراجعة.</p>
                    </div>

                    <!-- فلتر البحث -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6>فلتر البحث</h6>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="{{ route('case-management.index') }}" class="row">
                                        <div class="col-md-2">
                                            <label class="form-label">الحالة</label>
                                            <select name="status" class="form-control">
                                                <option value="">جميع الحالات</option>
                                                @foreach($statuses as $key => $status)
                                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">الأولوية</label>
                                            <select name="priority" class="form-control">
                                                <option value="">جميع الأولويات</option>
                                                @foreach($priorities as $key => $priority)
                                                    <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>
                                                        {{ $priority }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">البحث</label>
                                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-search"></i> بحث
                                                </button>
                                                <a href="{{ route('case-management.index') }}" class="btn btn-secondary">
                                                    <i class="fas fa-refresh"></i> إعادة تعيين
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- جدول الحالات -->
                    <div class="card">
                        <div class="card-header">
                            <h6>قائمة الحالات ({{ $cases->total() }} حالة)</h6>
                        </div>
                        <div class="card-body">
                            @if($cases->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>المتطوع</th>
                                                <th>الحالة</th>
                                                <th>الأولوية</th>
                                                <th>المعني</th>
                                                <th>تاريخ الإنشاء</th>
                                                <th>تاريخ الاستحقاق</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cases as $case)
                                            <tr>
                                                <td>{{ $case->id }}</td>
                                                <td>
                                                    <strong>{{ $case->full_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $case->email }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $case->getStatusColorAttribute() }}">
                                                        {{ $case->getStatusTextAttribute() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $case->getPriorityColorAttribute() }}">
                                                        {{ $case->getPriorityTextAttribute() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $case->assignedTo ? $case->assignedTo->name : 'غير محدد' }}
                                                </td>
                                                <td>{{ $case->created_at->format('Y-m-d') }}</td>
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
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('case-management.show', $case->id) }}" class="btn btn-sm btn-info" title="عرض التفاصيل">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-warning" onclick="updateCaseStatus({{ $case->id }})" title="تحديث الحالة">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-success" onclick="assignCase({{ $case->id }})" title="تعيين الحالة">
                                                            <i class="fas fa-user-plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- ترقيم الصفحات -->
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $cases->links() }}
                                </div>
                            @else
                                <div class="alert alert-warning text-center">
                                    <h5>لا توجد حالات</h5>
                                    <p>لم يتم العثور على أي حالات تطابق معايير البحث.</p>
                                    <a href="{{ route('volunteer-requests.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> إنشاء طلب تطوع جديد
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
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
                            @foreach($statuses as $key => $status)
                                <option value="{{ $key }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="أضف ملاحظات حول التحديث..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تعيين إلى</label>
                        <select name="assigned_to" class="form-control">
                            <option value="">اختر المراجع</option>
                            @foreach($reviewers as $reviewer)
                                <option value="{{ $reviewer->id }}">{{ $reviewer->name }}</option>
                            @endforeach
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
                <button type="button" class="btn btn-primary" onclick="submitCaseStatusUpdate()">حفظ التغييرات</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal تعيين الحالة -->
<div class="modal fade" id="assignCaseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعيين الحالة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignCaseForm">
                    <div class="mb-3">
                        <label class="form-label">تعيين إلى</label>
                        <select name="assigned_to" class="form-control" required>
                            <option value="">اختر المراجع</option>
                            @foreach($reviewers as $reviewer)
                                <option value="{{ $reviewer->id }}">{{ $reviewer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الأولوية</label>
                        <select name="priority" class="form-control">
                            @foreach($priorities as $key => $priority)
                                <option value="{{ $key }}">{{ $priority }}</option>
                            @endforeach
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
                <button type="button" class="btn btn-primary" onclick="submitCaseAssignment()">حفظ التعيين</button>
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

function assignCase(caseId) {
    currentCaseId = caseId;
    $('#assignCaseModal').modal('show');
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

function submitCaseAssignment() {
    const formData = new FormData(document.getElementById('assignCaseForm'));
    
    fetch(`/case-management/${currentCaseId}/assign`, {
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
            alert('حدث خطأ أثناء تعيين الحالة');
        }
    });
}
</script>
@endsection 