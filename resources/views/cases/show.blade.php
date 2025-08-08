@extends('layouts.app')

@section('title', 'تفاصيل الحالة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">تفاصيل الحالة #{{ $case->id }}</h4>
                    <div>
                        <a href="{{ route('case-management.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                        <button type="button" class="btn btn-primary" onclick="updateCaseStatus({{ $case->id }})">
                            <i class="fas fa-edit"></i> تحديث الحالة
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- معلومات المتطوع -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>معلومات المتطوع</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>الاسم:</strong></td>
                                            <td>{{ $case->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>البريد الإلكتروني:</strong></td>
                                            <td>{{ $case->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>رقم الجوال:</strong></td>
                                            <td>{{ $case->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>رقم الهوية:</strong></td>
                                            <td>{{ $case->national_id ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>تاريخ الميلاد:</strong></td>
                                            <td>{{ $case->birth_date ? $case->birth_date->format('Y-m-d') : 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الجنس:</strong></td>
                                            <td>{{ $case->gender == 'male' ? 'ذكر' : ($case->gender == 'female' ? 'أنثى' : 'غير محدد') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>العنوان:</strong></td>
                                            <td>{{ $case->address ?? 'غير محدد' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- حالة الطلب -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>حالة الطلب</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>الحالة:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $case->getStatusColorAttribute() }}">
                                                    {{ $case->getStatusTextAttribute() }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>الأولوية:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $case->getPriorityColorAttribute() }}">
                                                    {{ $case->getPriorityTextAttribute() }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>المعني:</strong></td>
                                            <td>{{ $case->assignedTo ? $case->assignedTo->name : 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>تاريخ الإنشاء:</strong></td>
                                            <td>{{ $case->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>تاريخ الاستحقاق:</strong></td>
                                            <td>
                                                @if($case->due_date)
                                                    <span class="{{ $case->isOverdue() ? 'text-danger' : '' }}">
                                                        {{ $case->due_date->format('Y-m-d') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>تاريخ المراجعة:</strong></td>
                                            <td>{{ $case->reviewed_at ? $case->reviewed_at->format('Y-m-d H:i') : 'لم تتم المراجعة' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تقدم الحالة -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>تقدم الحالة</h5>
                                </div>
                                <div class="card-body">
                                    <div class="progress mb-3">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $caseProgress['percentage'] }}%"
                                             aria-valuenow="{{ $caseProgress['percentage'] }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ $caseProgress['percentage'] }}%
                                        </div>
                                    </div>
                                    <p class="text-muted">
                                        {{ $caseProgress['completed_steps'] }} من {{ $caseProgress['total_steps'] }} خطوة مكتملة
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- سير المراجعة -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>سير المراجعة</h5>
                                </div>
                                <div class="card-body">
                                    @if($relatedWorkflows->count() > 0)
                                        <div class="timeline">
                                            @foreach($relatedWorkflows as $workflow)
                                                <div class="timeline-item">
                                                    <div class="timeline-marker"></div>
                                                    <div class="timeline-content">
                                                        <h6>{{ $workflow->step_name }}</h6>
                                                        <p class="text-muted">
                                                            الحالة: 
                                                            <span class="badge badge-{{ $workflow->getStatusColorAttribute() }}">
                                                                {{ $workflow->getStatusTextAttribute() }}
                                                            </span>
                                                        </p>
                                                        @if($workflow->notes)
                                                            <p>{{ $workflow->notes }}</p>
                                                        @endif
                                                        <small class="text-muted">
                                                            {{ $workflow->created_at->format('Y-m-d H:i') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">لا يوجد سير مراجعة لهذه الحالة</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الملاحظات -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>الملاحظات</h5>
                                </div>
                                <div class="card-body">
                                    <form id="addNoteForm">
                                        <div class="mb-3">
                                            <textarea name="note" class="form-control" rows="3" placeholder="أضف ملاحظة جديدة..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_internal" id="isInternal">
                                                <label class="form-check-label" for="isInternal">
                                                    ملاحظة داخلية
                                                </label>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="addNote()">
                                            إضافة ملاحظة
                                        </button>
                                    </form>

                                    <hr>

                                    <div id="notesList">
                                        @foreach($case->notes as $note)
                                            <div class="note-item mb-3 p-3 border rounded">
                                                <div class="d-flex justify-content-between">
                                                    <strong>{{ $note->user->name }}</strong>
                                                    <small class="text-muted">{{ $note->created_at->format('Y-m-d H:i') }}</small>
                                                </div>
                                                <p class="mb-0 mt-2">{{ $note->note }}</p>
                                                @if($note->is_internal)
                                                    <span class="badge badge-warning">داخلية</span>
                                                @endif
                                            </div>
                                        @endforeach
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
                            <option value="pending" {{ $case->status == 'pending' ? 'selected' : '' }}>معلق</option>
                            <option value="in_progress" {{ $case->status == 'in_progress' ? 'selected' : '' }}>قيد التقدم</option>
                            <option value="under_review" {{ $case->status == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="approved" {{ $case->status == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                            <option value="rejected" {{ $case->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            <option value="needs_revision" {{ $case->status == 'needs_revision' ? 'selected' : '' }}>يحتاج مراجعة</option>
                            <option value="completed" {{ $case->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ $case->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
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
                        <input type="date" name="due_date" class="form-control" value="{{ $case->due_date ? $case->due_date->format('Y-m-d') : '' }}">
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

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #007bff;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #007bff;
}

.timeline-content {
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 5px;
}
</style>

<script>
function updateCaseStatus(caseId) {
    $('#updateCaseStatusModal').modal('show');
}

function addNote() {
    const formData = new FormData(document.getElementById('addNoteForm'));
    
    fetch(`/case-management/{{ $case->id }}/note`, {
        method: 'POST',
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
            alert('حدث خطأ أثناء إضافة الملاحظة');
        }
    });
}

function submitCaseStatusUpdate() {
    const formData = new FormData(document.getElementById('updateCaseStatusForm'));
    
    fetch(`/case-management/{{ $case->id }}/status`, {
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