@extends('layouts.app')

@section('title', 'تفاصيل الإرسال')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">تفاصيل الإرسال #{{ $submission->id }}</h4>
                    <div>
                        <a href="{{ route('submissions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> رجوع
                        </a>
                        @if($submission->canBeReviewed())
                            <button type="button" class="btn btn-warning" onclick="updateStatus()">
                                <i class="fas fa-edit"></i> تحديث الحالة
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- معلومات الإرسال -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">معلومات الإرسال</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>طلب التطوع:</strong> 
                                                <a href="{{ route('volunteer-requests.show', $submission->volunteerRequest->id) }}">
                                                    {{ $submission->volunteerRequest->full_name }}
                                                </a>
                                            </p>
                                            <p><strong>البريد الإلكتروني:</strong> {{ $submission->volunteerRequest->email }}</p>
                                            <p><strong>رقم الهاتف:</strong> {{ $submission->volunteerRequest->phone }}</p>
                                            <p><strong>تاريخ الإنشاء:</strong> {{ $submission->created_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>الحالة:</strong> 
                                                <span class="badge bg-{{ $submission->status_color }}">
                                                    {{ $submission->status_text }}
                                                </span>
                                            </p>
                                            <p><strong>الأولوية:</strong> 
                                                <span class="badge bg-{{ $submission->priority_color }}">
                                                    {{ $submission->priority_text }}
                                                </span>
                                            </p>
                                            <p><strong>المعني:</strong> 
                                                @if($submission->assignedTo)
                                                    {{ $submission->assignedTo->name }}
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </p>
                                            <p><strong>المراجع:</strong> 
                                                @if($submission->reviewer)
                                                    {{ $submission->reviewer->name }}
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    
                                    @if($submission->due_date)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p><strong>تاريخ الاستحقاق:</strong> 
                                                    @if($submission->isOverdue())
                                                        <span class="text-danger">{{ $submission->due_date->format('Y-m-d H:i') }}</span>
                                                        <span class="badge bg-danger">متأخر</span>
                                                    @else
                                                        {{ $submission->due_date->format('Y-m-d H:i') }}
                                                        <span class="text-muted">({{ $submission->getRemainingTime() }} متبقي)</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($submission->notes)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p><strong>ملاحظات:</strong></p>
                                                <div class="alert alert-info">
                                                    {{ $submission->notes }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- المرفقات -->
                            @if($submission->attachments->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">المرفقات ({{ $submission->attachments->count() }})</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($submission->attachments as $attachment)
                                                <div class="col-md-6 mb-2">
                                                    <div class="d-flex align-items-center p-2 border rounded">
                                                        <div class="me-3">
                                                            @if($attachment->isImage())
                                                                <i class="fas fa-image text-primary"></i>
                                                            @elseif($attachment->isPdf())
                                                                <i class="fas fa-file-pdf text-danger"></i>
                                                            @elseif($attachment->isDocument())
                                                                <i class="fas fa-file-word text-primary"></i>
                                                            @else
                                                                <i class="fas fa-file text-secondary"></i>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-bold">{{ $attachment->file_name }}</div>
                                                            <small class="text-muted">{{ $attachment->file_size_text }}</small>
                                                        </div>
                                                        <div>
                                                            <a href="{{ $attachment->download_url }}" 
                                                               class="btn btn-sm btn-outline-primary" 
                                                               target="_blank" title="تحميل">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- التعليقات -->
                            <div class="card mt-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title">التعليقات ({{ $submission->comments->count() }})</h5>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addComment()">
                                        <i class="fas fa-plus"></i> إضافة تعليق
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="commentsList">
                                        @forelse($submission->comments as $comment)
                                            <div class="comment-item border-bottom pb-3 mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>{{ $comment->user->name }}</strong>
                                                        <small class="text-muted">{{ $comment->created_at->format('Y-m-d H:i') }}</small>
                                                        @if($comment->is_internal)
                                                            <span class="badge bg-warning">داخلي</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    {{ $comment->comment }}
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted text-center">لا توجد تعليقات</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الشريط الجانبي -->
                        <div class="col-md-4">
                            <!-- معلومات سير المراجعة -->
                            @if($submission->workflow)
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">سير المراجعة</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>الخطوة الحالية:</strong> {{ $submission->workflow->step_name }}</p>
                                        <p><strong>الحالة:</strong> 
                                            <span class="badge bg-{{ $submission->workflow->status_color }}">
                                                {{ $submission->workflow->status_text }}
                                            </span>
                                        </p>
                                        @if($submission->workflow->reviewed_at)
                                            <p><strong>تاريخ المراجعة:</strong> {{ $submission->workflow->reviewed_at->format('Y-m-d H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- إحصائيات سريعة -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">إحصائيات</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>عدد المرفقات:</strong> {{ $submission->attachments->count() }}</p>
                                    <p><strong>عدد التعليقات:</strong> {{ $submission->comments->count() }}</p>
                                    @if($submission->getReviewDuration())
                                        <p><strong>مدة المراجعة:</strong> {{ $submission->getReviewDuration() }} ساعة</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تحديث الحالة -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحديث حالة الإرسال</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    <div class="mb-3">
                        <label for="status" class="form-label">الحالة الجديدة</label>
                        <select name="status" id="status" class="form-control" required>
                            @foreach($statuses as $key => $status)
                                <option value="{{ $key }}" {{ $submission->status == $key ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control">{{ $submission->notes }}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="submitUpdateStatus()">تحديث</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة تعليق -->
<div class="modal fade" id="addCommentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة تعليق</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addCommentForm">
                    <div class="mb-3">
                        <label for="comment" class="form-label">التعليق</label>
                        <textarea name="comment" id="comment" rows="4" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_internal" id="is_internal" class="form-check-input">
                            <label for="is_internal" class="form-check-label">تعليق داخلي</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="submitAddComment()">إضافة</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateStatus() {
    $('#updateStatusModal').modal('show');
}

function submitUpdateStatus() {
    const formData = new FormData(document.getElementById('updateStatusForm'));
    
    fetch(`/submissions/{{ $submission->id }}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            status: formData.get('status'),
            notes: formData.get('notes')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#updateStatusModal').modal('hide');
            location.reload();
        } else {
            alert('حدث خطأ أثناء تحديث الحالة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تحديث الحالة');
    });
}

function addComment() {
    $('#addCommentModal').modal('show');
}

function submitAddComment() {
    const formData = new FormData(document.getElementById('addCommentForm'));
    
    fetch(`/submissions/{{ $submission->id }}/comment`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            comment: formData.get('comment'),
            is_internal: formData.get('is_internal') ? true : false
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#addCommentModal').modal('hide');
            location.reload();
        } else {
            alert('حدث خطأ أثناء إضافة التعليق');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء إضافة التعليق');
    });
}
</script>
@endpush 