@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('recurring-tasks.index') }}">المهام المتكررة</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('recurring-tasks.show', $task) }}">{{ $task->title }}</a></li>
                        <li class="breadcrumb-item active">إدارة الاستثناءات</li>
                    </ol>
                </div>
                <h4 class="page-title">إدارة استثناءات التكرار</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Exceptions List -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">استثناءات المهمة: {{ $task->title }}</h4>
                    <div class="header-action">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add-exception-modal">
                            <i class="feather-plus mr-1"></i> إضافة استثناء
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($exceptions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>نوع الاستثناء</th>
                                        <th>التاريخ الجديد</th>
                                        <th>السبب</th>
                                        <th>منشئ الاستثناء</th>
                                        <th>تاريخ الإنشاء</th>
                                        <th>العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exceptions as $exception)
                                    <tr>
                                        <td>
                                            <span class="font-weight-semibold">{{ $exception->exception_date->format('Y-m-d') }}</span>
                                        </td>
                                        <td>
                                            @if($exception->exception_type === 'skip')
                                                <span class="badge badge-warning">تخطي</span>
                                            @elseif($exception->exception_type === 'reschedule')
                                                <span class="badge badge-info">إعادة جدولة</span>
                                            @elseif($exception->exception_type === 'modify')
                                                <span class="badge badge-primary">تعديل</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($exception->new_date)
                                                {{ $exception->new_date->format('Y-m-d') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($exception->reason)
                                                <span class="text-truncate" style="max-width: 200px;" title="{{ $exception->reason }}">
                                                    {{ Str::limit($exception->reason, 50) }}
                                                </span>
                                            @else
                                                <span class="text-muted">لا يوجد سبب</span>
                                            @endif
                                        </td>
                                        <td>{{ $exception->creator->name ?? 'غير محدد' }}</td>
                                        <td>{{ $exception->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                @if($exception->modified_data)
                                                    <button type="button" class="btn btn-outline-info btn-sm" 
                                                            onclick="showModifications({{ json_encode($exception->modified_data) }})" 
                                                            title="عرض التعديلات">
                                                        <i class="feather-eye"></i>
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="deleteException({{ $exception->id }})" 
                                                        title="حذف الاستثناء">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $exceptions->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="feather-calendar text-muted" style="font-size: 48px;"></i>
                            <h5 class="mt-3">لا توجد استثناءات</h5>
                            <p class="text-muted">لم يتم إنشاء أي استثناءات لهذه المهمة المتكررة</p>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-exception-modal">
                                <i class="feather-plus mr-1"></i> إضافة استثناء
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Task Info -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">معلومات المهمة</h4>
                </div>
                <div class="card-body">
                    <p><strong>العنوان:</strong> {{ $task->title }}</p>
                    <p><strong>نمط التكرار:</strong> 
                        <span class="badge badge-info">
                            {{ \App\Models\Task::getRecurrencePatterns()[$task->recurrence_pattern] ?? $task->recurrence_pattern }}
                        </span>
                    </p>
                    <p><strong>التكرار التالي:</strong> {{ $task->next_occurrence_date?->format('Y-m-d H:i') ?? 'غير محدد' }}</p>
                    <p><strong>حالة التكرار:</strong> 
                        @if($task->recurring_active)
                            <span class="badge badge-success">نشط</span>
                        @else
                            <span class="badge badge-secondary">متوقف</span>
                        @endif
                    </p>
                    <p><strong>عدد الاستثناءات:</strong> {{ $exceptions->total() }}</p>
                    
                    <div class="mt-3">
                        <a href="{{ route('recurring-tasks.show', $task) }}" class="btn btn-outline-primary btn-block">
                            <i class="feather-arrow-left mr-1"></i> العودة للمهمة
                        </a>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">أنواع الاستثناءات</h4>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center px-0">
                            <span class="badge badge-warning mr-2">تخطي</span>
                            <span class="font-13">تخطي هذا التكرار بالكامل</span>
                        </div>
                        <div class="list-group-item d-flex align-items-center px-0">
                            <span class="badge badge-info mr-2">إعادة جدولة</span>
                            <span class="font-13">تغيير تاريخ هذا التكرار</span>
                        </div>
                        <div class="list-group-item d-flex align-items-center px-0">
                            <span class="badge badge-primary mr-2">تعديل</span>
                            <span class="font-13">تعديل خصائص هذا التكرار</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Exception Modal -->
<div class="modal fade" id="add-exception-modal" tabindex="-1" role="dialog" aria-labelledby="add-exception-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add-exception-modal-label">إضافة استثناء جديد</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exception-form">
                <div class="modal-body">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exception_date">تاريخ الاستثناء</label>
                                <input type="date" class="form-control" id="exception_date" name="exception_date" required>
                                <small class="text-muted">التاريخ المراد إنشاء استثناء له</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exception_type">نوع الاستثناء</label>
                                <select class="form-control" id="exception_type" name="exception_type" required>
                                    <option value="">اختر نوع الاستثناء</option>
                                    <option value="skip">تخطي هذا التكرار</option>
                                    <option value="reschedule">إعادة جدولة</option>
                                    <option value="modify">تعديل المهمة</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Reschedule Options -->
                    <div id="reschedule-options" style="display: none;">
                        <div class="form-group">
                            <label for="new_date">التاريخ الجديد</label>
                            <input type="date" class="form-control" id="new_date" name="new_date">
                        </div>
                    </div>

                    <!-- Modify Options -->
                    <div id="modify-options" style="display: none;">
                        <h6>التعديلات:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modified_title">عنوان جديد (اختياري)</label>
                                    <input type="text" class="form-control" id="modified_title" name="modified_data[title]">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modified_priority">أولوية جديدة (اختياري)</label>
                                    <select class="form-control" id="modified_priority" name="modified_data[priority]">
                                        <option value="">لا تغيير</option>
                                        <option value="urgent">عاجلة</option>
                                        <option value="high">عالية</option>
                                        <option value="medium">متوسطة</option>
                                        <option value="low">منخفضة</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="modified_description">وصف جديد (اختياري)</label>
                            <textarea class="form-control" id="modified_description" name="modified_data[description]" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reason">سبب الاستثناء (اختياري)</label>
                        <textarea class="form-control" id="reason" name="reason" rows="2" 
                                  placeholder="اذكر السبب وراء إنشاء هذا الاستثناء"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save mr-1"></i> حفظ الاستثناء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modifications Modal -->
<div class="modal fade" id="modifications-modal" tabindex="-1" role="dialog" aria-labelledby="modifications-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modifications-modal-label">تفاصيل التعديلات</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modifications-content">
                <!-- Will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Handle exception type change
    $('#exception_type').on('change', function() {
        const type = $(this).val();
        
        // Hide all options
        $('#reschedule-options, #modify-options').hide();
        
        // Show relevant options
        if (type === 'reschedule') {
            $('#reschedule-options').show();
            $('#new_date').attr('required', true);
        } else {
            $('#new_date').attr('required', false);
        }
        
        if (type === 'modify') {
            $('#modify-options').show();
        }
    });

    // Handle form submission
    $('#exception-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {};
        
        // Convert FormData to regular object
        for (let [key, value] of formData.entries()) {
            if (key.includes('[') && key.includes(']')) {
                // Handle nested objects
                const matches = key.match(/([^[]+)\[([^\]]*)\]/);
                if (matches) {
                    const baseKey = matches[1];
                    const subKey = matches[2];
                    
                    if (!data[baseKey]) data[baseKey] = {};
                    if (value !== '') {
                        data[baseKey][subKey] = value;
                    }
                }
            } else {
                if (value !== '') {
                    data[key] = value;
                }
            }
        }

        $.ajax({
            url: '{{ route("recurring-tasks.exceptions.create", $task) }}',
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response.errors) {
                    let errorMsg = 'أخطاء في البيانات:\n';
                    Object.values(response.errors).forEach(function(errors) {
                        if (Array.isArray(errors)) {
                            errors.forEach(function(error) {
                                errorMsg += '- ' + error + '\n';
                            });
                        } else {
                            errorMsg += '- ' + errors + '\n';
                        }
                    });
                    alert(errorMsg);
                } else {
                    alert(response.error || 'حدث خطأ غير متوقع');
                }
            }
        });
    });
});

function showModifications(modifications) {
    let content = '<div class="list-group list-group-flush">';
    
    Object.keys(modifications).forEach(function(key) {
        const value = modifications[key];
        if (value) {
            let displayKey = key;
            switch(key) {
                case 'title': displayKey = 'العنوان'; break;
                case 'description': displayKey = 'الوصف'; break;
                case 'priority': displayKey = 'الأولوية'; break;
                default: displayKey = key;
            }
            
            content += `<div class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="font-weight-semibold">${displayKey}:</span>
                <span>${value}</span>
            </div>`;
        }
    });
    
    content += '</div>';
    
    if (Object.keys(modifications).length === 0 || !Object.values(modifications).some(v => v)) {
        content = '<p class="text-muted">لا توجد تعديلات محددة</p>';
    }
    
    $('#modifications-content').html(content);
    $('#modifications-modal').modal('show');
}

function deleteException(exceptionId) {
    if (!confirm('هل أنت متأكد من حذف هذا الاستثناء؟')) {
        return;
    }

    $.ajax({
        url: `/recurring-tasks/{{ $task->id }}/exceptions/${exceptionId}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            alert(response.message);
            location.reload();
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert(response.error || 'حدث خطأ غير متوقع');
        }
    });
}
</script>
@endpush
@endsection