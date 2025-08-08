@extends('layouts.app')

@section('title', 'إدارة الخبرات السابقة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">إدارة الخبرات السابقة</h4>
                    <a href="{{ route('previous-experiences.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة خبرة جديدة
                    </a>
                </div>
                <div class="card-body">
                    <!-- فلاتر البحث -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="volunteerFilter" class="form-control">
                                <option value="">جميع المتطوعين</option>
                                @foreach($experiences->pluck('volunteerRequest.full_name')->unique() as $name)
                                    @if($name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="statusFilter" class="form-control">
                                <option value="">جميع الحالات</option>
                                <option value="current">خبرات حالية</option>
                                <option value="past">خبرات سابقة</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="البحث في الخبرات...">
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="exportBtn" class="btn btn-success">
                                <i class="fas fa-download"></i> تصدير البيانات
                            </button>
                        </div>
                    </div>

                    <!-- جدول الخبرات -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="experiencesTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>المتطوع</th>
                                    <th>عنوان الخبرة</th>
                                    <th>المنصب</th>
                                    <th>المؤسسة</th>
                                    <th>المدة</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($experiences as $experience)
                                <tr>
                                    <td>{{ $experience->id }}</td>
                                    <td>
                                        <strong>{{ $experience->volunteerRequest->full_name ?? 'غير محدد' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $experience->volunteerRequest->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $experience->title }}</strong>
                                        @if($experience->description)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($experience->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $experience->position }}</td>
                                    <td>{{ $experience->organization }}</td>
                                    <td>
                                        @if($experience->is_current)
                                            <span class="badge badge-success">حالية</span>
                                            <br>
                                            <small>{{ $experience->start_date->format('Y-m-d') }} - حتى الآن</small>
                                        @else
                                            <span class="badge badge-info">سابقة</span>
                                            <br>
                                            <small>{{ $experience->start_date->format('Y-m-d') }} - {{ $experience->end_date->format('Y-m-d') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $experience->is_current ? 'success' : 'secondary' }}">
                                            {{ $experience->is_current ? 'خبرة حالية' : 'خبرة سابقة' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('previous-experiences.show', $experience->id) }}" 
                                               class="btn btn-sm btn-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('previous-experiences.edit', $experience->id) }}" 
                                               class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger delete-experience" 
                                                    data-id="{{ $experience->id }}" 
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            لا توجد خبرات مسجلة حالياً
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
                <p>هل أنت متأكد من حذف هذه الخبرة؟</p>
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
        var volunteer = $('#volunteerFilter').val();
        var status = $('#statusFilter').val();
        var search = $('#searchInput').val().toLowerCase();

        $('#experiencesTable tbody tr').each(function() {
            var row = $(this);
            var volunteerMatch = !volunteer || row.find('td:eq(1)').text().includes(volunteer);
            var statusMatch = !status || (status === 'current' && row.find('td:eq(5)').text().includes('حالية')) || 
                             (status === 'past' && row.find('td:eq(5)').text().includes('سابقة'));
            var searchMatch = !search || row.text().toLowerCase().includes(search);

            if (volunteerMatch && statusMatch && searchMatch) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    $('#volunteerFilter, #statusFilter, #searchInput').on('change keyup', filterTable);

    // حذف الخبرة
    $('.delete-experience').click(function() {
        var experienceId = $(this).data('id');
        $('#deleteForm').attr('action', '/previous-experiences/' + experienceId);
        $('#deleteModal').modal('show');
    });

    // تصدير البيانات
    $('#exportBtn').click(function() {
        // يمكن إضافة منطق التصدير هنا
        toastr.info('سيتم إضافة ميزة التصدير قريباً');
    });

    // DataTables
    $('#experiencesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
        },
        "pageLength": 25,
        "order": [[0, "desc"]]
    });
});
</script>
@endpush 