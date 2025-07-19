@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>طلبات التطوع</h4>
                    <div>
                        <a href="{{ route('applicationtovolunteer.export') }}" class="btn btn-success me-2">
                            <i class="fas fa-download"></i> تصدير CSV
                        </a>
                        <a href="{{ route('applicationtovolunteer.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> طلب جديد
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total'] }}</h5>
                                    <small>إجمالي الطلبات</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['pending'] }}</h5>
                                    <small>معلق</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['approved'] }}</h5>
                                    <small>موافق عليه</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['rejected'] }}</h5>
                                    <small>مرفوض</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['withdrawn'] }}</h5>
                                    <small>مسحوب</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['recent'] }}</h5>
                                    <small>الأسبوع الماضي</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6>فلاتر البحث</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('applicationtovolunteer.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>البحث</label>
                                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="الاسم، البريد، الهاتف...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>الحالة</label>
                                            <select class="form-control" name="status">
                                                <option value="">جميع الحالات</option>
                                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                                <option value="withdrawn" {{ request('status') == 'withdrawn' ? 'selected' : '' }}>مسحوب</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>المدينة</label>
                                            <select class="form-control" name="city">
                                                <option value="">جميع المدن</option>
                                                @foreach($cities as $city)
                                                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-primary">بحث</button>
                                                <a href="{{ route('applicationtovolunteer.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Applications Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الهاتف</th>
                                    <th>المدينة</th>
                                    <th>الحالة</th>
                                    <th>تاريخ التقديم</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applications as $application)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $application->full_name_with_last_name }}</td>
                                        <td>{{ $application->email }}</td>
                                        <td>{{ $application->phone }}</td>
                                        <td>{{ $application->city }}</td>
                                        <td>
                                            @switch($application->status)
                                                @case('pending')
                                                    <span class="badge badge-warning">معلق</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge badge-success">موافق عليه</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge badge-danger">مرفوض</span>
                                                    @break
                                                @case('withdrawn')
                                                    <span class="badge badge-secondary">مسحوب</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ $application->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('applicationtovolunteer.show', $application->uuid) }}" 
                                                   class="btn btn-sm btn-info" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                    <span class="d-none d-md-inline">عرض</span>
                                                </a>
                                                <a href="{{ route('applicationtovolunteer.edit', $application->uuid) }}" 
                                                   class="btn btn-sm btn-warning" title="تعديل الطلب">
                                                    <i class="fas fa-edit"></i>
                                                    <span class="d-none d-md-inline">تعديل</span>
                                                </a>
                                                <!-- Quick Status Update -->
                                                <div class="dropdown d-inline">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown" title="تحديث الحالة">
                                                        <i class="fas fa-edit"></i>
                                                        <span class="d-none d-md-inline">الحالة</span>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <form method="POST" action="{{ route('applicationtovolunteer.update-status', $application->uuid) }}" style="display: inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="approved">
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="fas fa-check"></i> موافق عليه
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('applicationtovolunteer.update-status', $application->uuid) }}" style="display: inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="rejected">
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="fas fa-times"></i> مرفوض
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('applicationtovolunteer.update-status', $application->uuid) }}" style="display: inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="withdrawn">
                                                            <button type="submit" class="dropdown-item text-secondary">
                                                                <i class="fas fa-undo"></i> مسحوب
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <form method="POST" action="{{ route('applicationtovolunteer.destroy', $application->uuid) }}" 
                                                      style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟ هذا الإجراء لا يمكن التراجع عنه.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="حذف الطلب">
                                                        <i class="fas fa-trash"></i>
                                                        <span class="d-none d-md-inline">حذف</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد طلبات تطوع</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($applications->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $applications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
/* تحسين مظهر الأزرار */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease-in-out;
}

.btn-sm:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* تحسين مظهر الجدول */
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
    cursor: pointer;
}

/* تحسين مظهر البطاقات الإحصائية */
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

/* تحسين مظهر الأزرار في الجدول */
.d-flex.gap-1 .btn {
    margin: 0 2px;
    min-width: 60px;
}

/* تحسين مظهر رسائل التأكيد */
.alert {
    border-radius: 0.5rem;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* تحسين مظهر الفلاتر */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

/* تحسين مظهر الـ dropdown */
.dropdown-menu {
    border-radius: 0.5rem;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}
</style>

<script>
// تفعيل الـ dropdown
$(document).ready(function() {
    $('.dropdown-toggle').dropdown();
});
</script> 