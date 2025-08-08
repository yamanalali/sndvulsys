@extends('layouts.app')

@section('title', 'تفاصيل المهارة - ' . $skill->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="fas fa-star"></i> تفاصيل المهارة: {{ $skill->name }}
                    </h4>
                    <div>
                        <a href="{{ route('skills.edit', $skill->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('skills.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- معلومات المهارة الأساسية -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="text-primary">المعلومات الأساسية</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>اسم المهارة:</strong></td>
                                            <td>{{ $skill->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الفئة:</strong></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $skill->getCategories()[$skill->category] ?? $skill->category }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>المستوى:</strong></td>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    {{ $skill->getLevels()[$skill->level] ?? $skill->level }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>الحالة:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $skill->is_active ? 'success' : 'danger' }}">
                                                    {{ $skill->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>تاريخ الإنشاء:</strong></td>
                                            <td>{{ $skill->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>آخر تحديث:</strong></td>
                                            <td>{{ $skill->updated_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="text-primary">الوصف</h5>
                                    @if($skill->description)
                                        <div class="alert alert-light">
                                            {{ $skill->description }}
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            لا يوجد وصف لهذه المهارة
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- إحصائيات المهارة -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary">إحصائيات المهارة</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center">
                                                    <h3>{{ $skill->volunteerRequests->count() }}</h3>
                                                    <p>عدد المتطوعين</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <h3>{{ $skill->users->count() }}</h3>
                                                    <p>عدد المستخدمين</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-info text-white">
                                                <div class="card-body text-center">
                                                    <h3>{{ $skill->is_active ? 'نشط' : 'غير نشط' }}</h3>
                                                    <p>الحالة</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- الإجراءات السريعة -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">الإجراءات السريعة</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="button" 
                                                class="btn btn-{{ $skill->is_active ? 'secondary' : 'success' }} toggle-status"
                                                data-id="{{ $skill->id }}">
                                            <i class="fas fa-{{ $skill->is_active ? 'times' : 'check' }}"></i>
                                            {{ $skill->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}
                                        </button>
                                        
                                        <a href="{{ route('skills.edit', $skill->id) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> تعديل المهارة
                                        </a>
                                        
                                        <button type="button" 
                                                class="btn btn-danger delete-skill" 
                                                data-id="{{ $skill->id }}">
                                            <i class="fas fa-trash"></i> حذف المهارة
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- معلومات إضافية -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="card-title">معلومات إضافية</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-calendar"></i> تم إنشاؤها: {{ $skill->created_at->diffForHumans() }}</li>
                                        <li><i class="fas fa-clock"></i> آخر تحديث: {{ $skill->updated_at->diffForHumans() }}</li>
                                        <li><i class="fas fa-users"></i> المتطوعون: {{ $skill->volunteerRequests->count() }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- قائمة المتطوعين الذين لديهم هذه المهارة -->
                    @if($skill->volunteerRequests->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary">المتطوعون الذين لديهم هذه المهارة</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>اسم المتطوع</th>
                                            <th>البريد الإلكتروني</th>
                                            <th>المستوى</th>
                                            <th>سنوات الخبرة</th>
                                            <th>تاريخ الطلب</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($skill->volunteerRequests as $request)
                                        <tr>
                                            <td>{{ $request->id }}</td>
                                            <td>{{ $request->full_name ?? 'غير محدد' }}</td>
                                            <td>{{ $request->email }}</td>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    {{ $request->pivot->level ?? 'غير محدد' }}
                                                </span>
                                            </td>
                                            <td>{{ $request->pivot->years_experience ?? 0 }} سنوات</td>
                                            <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
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
                <p>هل أنت متأكد من حذف المهارة "{{ $skill->name }}"؟</p>
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
                    if (response.is_active) {
                        button.removeClass('btn-success').addClass('btn-secondary')
                              .find('i').removeClass('fa-check').addClass('fa-times');
                        button.text(' إلغاء التفعيل');
                    } else {
                        button.removeClass('btn-secondary').addClass('btn-success')
                              .find('i').removeClass('fa-times').addClass('fa-check');
                        button.text(' تفعيل');
                    }

                    // تحديث البادج
                    var statusBadge = $('.badge-success, .badge-danger');
                    if (response.is_active) {
                        statusBadge.removeClass('badge-danger').addClass('badge-success').text('نشط');
                    } else {
                        statusBadge.removeClass('badge-success').addClass('badge-danger').text('غير نشط');
                    }

                    toastr.success(response.message);
                }
            },
            error: function() {
                toastr.error('حدث خطأ أثناء تحديث الحالة');
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