@extends('layouts.app')

@section('title', 'إدارة المهارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">إدارة المهارات</h4>
                        <p class="card-subtitle mb-0">إدارة مهارات المتطوعين وتصنيفها</p>
                    </div>
                    <div>
                        <a href="{{ route('skills.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            إضافة مهارة جديدة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- فلاتر البحث -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('skills.index') }}" id="filterForm">
                                <select name="category" id="categoryFilter" class="form-control" onchange="this.form.submit()">
                                    <option value="">جميع الفئات</option>
                                    @foreach($categories as $key => $category)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <select name="level" id="levelFilter" class="form-control" onchange="document.getElementById('filterForm').submit()">
                                <option value="">جميع المستويات</option>
                                @foreach($levels as $key => $level)
                                    <option value="{{ $key }}" {{ request('level') == $key ? 'selected' : '' }}>
                                        {{ $level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" id="statusFilter" class="form-control" onchange="document.getElementById('filterForm').submit()">
                                <option value="">جميع الحالات</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('skills.index') }}" class="d-flex">
                                <input type="text" name="search" id="searchInput" class="form-control" 
                                       placeholder="البحث في المهارات..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary ms-2">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search') || request('category') || request('level') || request('status'))
                                    <a href="{{ route('skills.index') }}" class="btn btn-secondary ms-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    <!-- جدول المهارات -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="skillsTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>اسم المهارة</th>
                                    <th>الوصف</th>
                                    <th>الفئة</th>
                                    <th>المستوى</th>
                                    <th>الحالة</th>
                                    <th>عدد المتطوعين</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($skills as $skill)
                                <tr>
                                    <td>{{ $skill->id }}</td>
                                    <td>
                                        <strong>{{ $skill->name }}</strong>
                                    </td>
                                    <td>
                                        @if($skill->description)
                                            {{ Str::limit($skill->description, 50) }}
                                        @else
                                            <span class="text-muted">لا يوجد وصف</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $categories[$skill->category] ?? $skill->category }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $levels[$skill->level] ?? $skill->level }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $skill->is_active ? 'success' : 'danger' }}">
                                            {{ $skill->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $skill->volunteer_requests_count }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('skills.show', $skill->id) }}" 
                                               class="btn btn-sm btn-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('skills.edit', $skill->id) }}" 
                                               class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-{{ $skill->is_active ? 'secondary' : 'success' }} toggle-status"
                                                    data-id="{{ $skill->id }}" 
                                                    title="{{ $skill->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                                <i class="fas fa-{{ $skill->is_active ? 'times' : 'check' }}"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger delete-skill" 
                                                    data-id="{{ $skill->id }}" 
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
                                            لا توجد مهارات مسجلة حالياً
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
                <p>هل أنت متأكد من حذف هذه المهارة؟</p>
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
    // تهيئة DataTable بدون فلترة (لأننا نستخدم فلترة من جانب الخادم)
    var table = $('#skillsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
        },
        "pageLength": 25,
        "order": [[0, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [7] } // تعطيل الترتيب في عمود الإجراءات
        ],
        "searching": false, // تعطيل البحث في DataTable
        "info": true,
        "paging": true
    });

    // تبديل حالة المهارة
    $('.toggle-status').click(function() {
        var skillId = $(this).data('id');
        var button = $(this);

        $.ajax({
            url: '/skills/' + skillId + '/toggle-status',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // تحديث الواجهة
                    var statusCell = button.closest('tr').find('td:eq(5)');
                    var badge = statusCell.find('.badge');
                    
                    if (response.is_active) {
                        badge.removeClass('badge-danger').addClass('badge-success').text('نشط');
                        button.removeClass('btn-success').addClass('btn-secondary')
                              .find('i').removeClass('fa-check').addClass('fa-times');
                    } else {
                        badge.removeClass('badge-success').addClass('badge-danger').text('غير نشط');
                        button.removeClass('btn-secondary').addClass('btn-success')
                              .find('i').removeClass('fa-times').addClass('fa-check');
                    }

                    // رسالة نجاح
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                }
            },
            error: function() {
                if (typeof toastr !== 'undefined') {
                    toastr.error('حدث خطأ أثناء تحديث الحالة');
                } else {
                    alert('حدث خطأ أثناء تحديث الحالة');
                }
            }
        });
    });

    // حذف المهارة
    $('.delete-skill').click(function() {
        var skillId = $(this).data('id');
        $('#deleteForm').attr('action', '/skills/' + skillId);
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush 