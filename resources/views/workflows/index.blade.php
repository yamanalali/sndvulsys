
@extends('layouts.app')

@section('title', 'إدارة سير العمل')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">إدارة سير العمل</h4>
                    <a href="{{ route('workflows.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إنشاء سير عمل جديد
                    </a>
                </div>
                <div class="card-body">
                    <!-- فلاتر البحث -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="statusFilter" class="form-control">
                                <option value="">جميع الحالات</option>
                                @foreach($statuses as $key => $status)
                                    <option value="{{ $key }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="stepFilter" class="form-control">
                                <option value="">جميع الخطوات</option>
                                @foreach($steps as $key => $step)
                                    <option value="{{ $key }}">{{ $step }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="priorityFilter" class="form-control">
                                <option value="">جميع الأولويات</option>
                                @foreach($priorities as $key => $priority)
                                    <option value="{{ $key }}">{{ $priority }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="البحث في سير العمل...">
                        </div>
                    </div>

                    <!-- جدول سير العمل -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="workflowsTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>المتطوع</th>
                                    <th>الخطوة</th>
                                    <th>الحالة</th>
                                    <th>الأولوية</th>
                                    <th>المسؤول</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>آخر تحديث</th>
                                    <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
                                @forelse($workflows as $workflow)
                                <tr>
                                    <td>{{ $workflow->id }}</td>
                                    <td>
                                        <strong>{{ $workflow->volunteerRequest->full_name ?? 'غير محدد' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $workflow->volunteerRequest->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $steps[$workflow->step] ?? $workflow->step }}</span>
                                        @if($workflow->is_completed)
                                            <i class="fas fa-check-circle text-success ml-1"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'in_review' => 'info',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'needs_revision' => 'secondary',
                                                'completed' => 'success',
                                                'cancelled' => 'dark'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $statusColors[$workflow->status] ?? 'secondary' }}">
                                            {{ $statuses[$workflow->status] ?? $workflow->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $priorityColors = [
                                                'low' => 'success',
                                                'medium' => 'info',
                                                'high' => 'warning',
                                                'urgent' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $priorityColors[$workflow->priority] ?? 'secondary' }}">
                                            {{ $priorities[$workflow->priority] ?? $workflow->priority }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($workflow->assignedTo)
                                            <strong>{{ $workflow->assignedTo->name }}</strong>
                                        @elseif($workflow->reviewer)
                                            <strong>{{ $workflow->reviewer->name }}</strong>
                                            <br>
                                            <small class="text-muted">مراجع</small>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $workflow->created_at->format('Y-m-d H:i') }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $workflow->updated_at->format('Y-m-d H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('workflows.show', $workflow->id) }}" 
                                               class="btn btn-sm btn-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('workflows.edit', $workflow->id) }}" 
                                               class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($workflow->canProceedToNext())
                                                <button type="button" 
                                                        class="btn btn-sm btn-success proceed-next"
                                                        data-id="{{ $workflow->id }}" 
                                                        title="الخطوة التالية">
                                                    <i class="fas fa-arrow-right"></i>
                                                </button>
                                            @endif
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger delete-workflow" 
                                                    data-id="{{ $workflow->id }}" 
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            لا يوجد سير عمل مسجل حالياً
                                        </div>
                </td>
            </tr>
                                @endforelse
        </tbody>
    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- عرض إحصائيات سير العمل -->
    <div class="row mt-4">
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3>{{ $workflows->where('status', 'pending')->count() }}</h3>
                    <p>معلق</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3>{{ $workflows->where('status', 'in_review')->count() }}</h3>
                    <p>قيد المراجعة</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ $workflows->where('status', 'approved')->count() }}</h3>
                    <p>موافق عليه</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3>{{ $workflows->where('status', 'rejected')->count() }}</h3>
                    <p>مرفوض</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3>{{ $workflows->where('status', 'needs_revision')->count() }}</h3>
                    <p>يحتاج مراجعة</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>{{ $workflows->where('status', 'completed')->count() }}</h3>
                    <p>مكتمل</p>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تأكيد الحذف -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف هذا السير العمل؟</p>
                <p class="text-danger"><small>لا يمكن التراجع عن هذا الإجراء.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // فلترة الجدول
    function filterTable() {
        var status = $('#statusFilter').val();
        var step = $('#stepFilter').val();
        var priority = $('#priorityFilter').val();
        var search = $('#searchInput').val().toLowerCase();

        $('#workflowsTable tbody tr').each(function() {
            var row = $(this);
            var statusMatch = !status || row.find('td:eq(3)').text().includes(status);
            var stepMatch = !step || row.find('td:eq(2)').text().includes(step);
            var priorityMatch = !priority || row.find('td:eq(4)').text().includes(priority);
            var searchMatch = !search || row.text().toLowerCase().includes(search);

            if (statusMatch && stepMatch && priorityMatch && searchMatch) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    $('#statusFilter, #stepFilter, #priorityFilter, #searchInput').on('change keyup', filterTable);

    // الانتقال للخطوة التالية
    $('.proceed-next').click(function() {
        var workflowId = $(this).data('id');
        var button = $(this);

        $.ajax({
            url: '/workflows/' + workflowId + '/next-step',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // إعادة تحميل الصفحة لتحديث البيانات
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('حدث خطأ أثناء الانتقال للخطوة التالية');
                }
            }
        });
    });

    // حذف سير العمل
    $('.delete-workflow').click(function() {
        var workflowId = $(this).data('id');
        $('#deleteForm').attr('action', '/workflows/' + workflowId);
        $('#deleteModal').modal('show');
    });

    // DataTables
    $('#workflowsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
        },
        "pageLength": 25,
        "order": [[0, "desc"]]
    });
});
</script>
@endpush