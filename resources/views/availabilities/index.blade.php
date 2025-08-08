@extends('layouts.app')

@section('title', 'إدارة جدول التوفر')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">إدارة جدول التوفر</h4>
                    <a href="{{ route('availabilities.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة توفر جديد
                    </a>
                </div>
                <div class="card-body">
                    <!-- فلاتر البحث -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="volunteerFilter" class="form-control">
                                <option value="">جميع المتطوعين</option>
                                @foreach($availabilities->pluck('volunteerRequest.full_name')->unique() as $name)
                                    @if($name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="dayFilter" class="form-control">
                                <option value="">جميع الأيام</option>
                                @foreach($days as $key => $day)
                                    <option value="{{ $key }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="statusFilter" class="form-control">
                                <option value="">جميع الحالات</option>
                                <option value="1">متاح</option>
                                <option value="0">غير متاح</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="البحث في التوفر...">
                        </div>
                    </div>

                    <!-- جدول التوفر -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="availabilitiesTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>المتطوع</th>
                                    <th>اليوم</th>
                                    <th>فترة الوقت</th>
                                    <th>وقت البداية</th>
                                    <th>وقت النهاية</th>
                                    <th>الحالة</th>
                                    <th>الساعات المفضلة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($availabilities as $availability)
                                <tr data-id="{{ $availability->id }}">
                                    <td>{{ $availability->id }}</td>
                                    <td>
                                        <strong>{{ $availability->volunteerRequest->full_name ?? 'غير محدد' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $availability->volunteerRequest->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $days[$availability->day] ?? $availability->day }}</span>
                                    </td>
                                    <td>
                                        @if($availability->time_slot)
                                            <span class="badge badge-info">{{ $timeSlots[$availability->time_slot] ?? $availability->time_slot }}</span>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($availability->start_time)
                                            {{ \Carbon\Carbon::parse($availability->start_time)->format('H:i') }}
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($availability->end_time)
                                            {{ \Carbon\Carbon::parse($availability->end_time)->format('H:i') }}
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $availability->is_available ? 'success' : 'danger' }}">
                                            {{ $availability->is_available ? 'متاح' : 'غير متاح' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($availability->preferred_hours_per_week)
                                            <span class="badge badge-secondary">{{ $availability->preferred_hours_per_week }} ساعة/أسبوع</span>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('availabilities.show', $availability->id) }}" 
                                               class="btn btn-sm btn-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('availabilities.edit', $availability->id) }}" 
                                               class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-{{ $availability->is_available ? 'secondary' : 'success' }} toggle-availability"
                                                    data-id="{{ $availability->id }}" 
                                                    title="{{ $availability->is_available ? 'إلغاء التوفر' : 'تفعيل التوفر' }}">
                                                <i class="fas fa-{{ $availability->is_available ? 'times' : 'check' }}"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger delete-availability" 
                                                    data-id="{{ $availability->id }}" 
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
                                            لا يوجد توفر مسجل حالياً
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

    <!-- عرض إحصائيات التوفر -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>{{ $availabilities->where('is_available', true)->count() }}</h3>
                    <p>متاح حالياً</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ $availabilities->count() }}</h3>
                    <p>إجمالي التوفر</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3>{{ $availabilities->pluck('volunteerRequest.id')->unique()->count() }}</h3>
                    <p>عدد المتطوعين</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3>{{ $availabilities->pluck('day')->unique()->count() }}</h3>
                    <p>أيام التوفر</p>
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
                <p>هل أنت متأكد من حذف هذا التوفر؟</p>
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
        var day = $('#dayFilter').val();
        var status = $('#statusFilter').val();
        var search = $('#searchInput').val().toLowerCase();

        $('#availabilitiesTable tbody tr').each(function() {
            var row = $(this);
            var volunteerMatch = !volunteer || row.find('td:eq(1)').text().includes(volunteer);
            var dayMatch = !day || row.find('td:eq(2)').text().includes(day);
            var statusMatch = !status || (status === '1' && row.find('td:eq(6)').text().includes('متاح')) || 
                             (status === '0' && row.find('td:eq(6)').text().includes('غير متاح'));
            var searchMatch = !search || row.text().toLowerCase().includes(search);

            if (volunteerMatch && dayMatch && statusMatch && searchMatch) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    $('#volunteerFilter, #dayFilter, #statusFilter, #searchInput').on('change keyup', filterTable);

    // تبديل حالة التوفر
    $('.toggle-availability').click(function() {
        var availabilityId = $(this).data('id');
        var button = $(this);

        $.ajax({
            url: '/availabilities/' + availabilityId + '/toggle',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // تحديث الواجهة
                    var statusCell = button.closest('tr').find('td:eq(6)');
                    var badge = statusCell.find('.badge');
                    
                    if (response.is_available) {
                        badge.removeClass('badge-danger').addClass('badge-success').text('متاح');
                        button.removeClass('btn-success').addClass('btn-secondary')
                              .find('i').removeClass('fa-check').addClass('fa-times');
                    } else {
                        badge.removeClass('badge-success').addClass('badge-danger').text('غير متاح');
                        button.removeClass('btn-secondary').addClass('btn-success')
                              .find('i').removeClass('fa-times').addClass('fa-check');
                    }

                    // إظهار رسالة نجاح
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
                alert('حدث خطأ أثناء تحديث الحالة');
            }
        });
    });

    // حذف التوفر
    $('.delete-availability').click(function() {
        var availabilityId = $(this).data('id');
        if (confirm('هل أنت متأكد من حذف هذا التوفر؟')) {
            $.ajax({
                url: '/availabilities/' + availabilityId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // إزالة الصف من الجدول
                    $('tr[data-id="' + availabilityId + '"]').remove();
                    alert('تم الحذف بنجاح');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    alert('حدث خطأ أثناء الحذف');
                }
            });
        }
    });

    // DataTables
    $('#availabilitiesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
        },
        "pageLength": 25,
        "order": [[0, "desc"]]
    });
});
</script>
@endpush 