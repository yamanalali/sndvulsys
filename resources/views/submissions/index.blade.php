@extends('layouts.app')

@section('title', 'قائمة الإرسالات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">قائمة الإرسالات</h4>
                    <a href="{{ route('submissions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إرسال جديد
                    </a>
                </div>
                <div class="card-body">
                    <!-- إحصائيات سريعة -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>{{ $statistics['total'] }}</h5>
                                    <p class="mb-0">إجمالي الإرسالات</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>{{ $statistics['pending'] }}</h5>
                                    <p class="mb-0">معلق</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>{{ $statistics['in_review'] }}</h5>
                                    <p class="mb-0">قيد المراجعة</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>{{ $statistics['completed'] }}</h5>
                                    <p class="mb-0">مكتمل</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- فلتر البحث -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="GET" action="{{ route('submissions.index') }}" class="row">
                                        <div class="col-md-3">
                                            <select name="status" class="form-control">
                                                <option value="">جميع الحالات</option>
                                                @foreach($statuses as $key => $status)
                                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="priority" class="form-control">
                                                <option value="">جميع الأولويات</option>
                                                @foreach($priorities as $key => $priority)
                                                    <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>
                                                        {{ $priority }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary">بحث</button>
                                            <a href="{{ route('submissions.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- جدول الإرسالات -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>طلب التطوع</th>
                                    <th>المعني</th>
                                    <th>المراجع</th>
                                    <th>الحالة</th>
                                    <th>الأولوية</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>تاريخ الاستحقاق</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($submissions as $submission)
                                <tr>
                                    <td>{{ $submission->id }}</td>
                                    <td>
                                        <a href="{{ route('volunteer-requests.show', $submission->volunteerRequest->id) }}">
                                            {{ $submission->volunteerRequest->full_name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($submission->assignedTo)
                                            {{ $submission->assignedTo->name }}
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($submission->reviewer)
                                            {{ $submission->reviewer->name }}
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $submission->status_color }}">
                                            {{ $submission->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $submission->priority_color }}">
                                            {{ $submission->priority_text }}
                                        </span>
                                    </td>
                                    <td>{{ $submission->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($submission->due_date)
                                            @if($submission->isOverdue())
                                                <span class="text-danger">{{ $submission->due_date->format('Y-m-d') }}</span>
                                            @else
                                                {{ $submission->due_date->format('Y-m-d') }}
                                            @endif
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('submissions.show', $submission->id) }}" 
                                               class="btn btn-sm btn-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($submission->canBeReviewed())
                                                <button type="button" class="btn btn-sm btn-warning" 
                                                        onclick="assignReviewer({{ $submission->id }})" title="تعيين مراجع">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">لا توجد إرسالات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- الترقيم -->
                    <div class="d-flex justify-content-center">
                        {{ $submissions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal لتعيين المراجع -->
<div class="modal fade" id="assignReviewerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعيين مراجع</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignReviewerForm">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">المراجع</label>
                        <select name="assigned_to" id="assigned_to" class="form-control" required>
                            <option value="">اختر المراجع</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">تاريخ الاستحقاق</label>
                        <input type="datetime-local" name="due_date" id="due_date" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="submitAssignReviewer()">تعيين</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentSubmissionId = null;

function assignReviewer(submissionId) {
    currentSubmissionId = submissionId;
    $('#assignReviewerModal').modal('show');
}

function submitAssignReviewer() {
    const formData = new FormData(document.getElementById('assignReviewerForm'));
    
    fetch(`/submissions/${currentSubmissionId}/assign`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            assigned_to: formData.get('assigned_to'),
            due_date: formData.get('due_date')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#assignReviewerModal').modal('hide');
            location.reload();
        } else {
            alert('حدث خطأ أثناء تعيين المراجع');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تعيين المراجع');
    });
}
</script>
@endpush 