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
                        <li class="breadcrumb-item active">تعديل الإعدادات</li>
                    </ol>
                </div>
                <h4 class="page-title">تعديل إعدادات التكرار</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ $task->title }}</h4>
                </div>
                <div class="card-body">
                    <form id="recurring-form">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recurrence_pattern">نمط التكرار</label>
                                    <select class="form-control" id="recurrence_pattern" name="recurrence_pattern" required>
                                        @foreach($recurrencePatterns as $key => $label)
                                            <option value="{{ $key }}" {{ $task->recurrence_pattern === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="interval">فترة التكرار</label>
                                    <input type="number" class="form-control" id="interval" name="recurrence_config[interval]" 
                                           value="{{ $task->getRecurrenceConfig()['interval'] ?? 1 }}" min="1" required>
                                    <small class="text-muted">كل كم من الوحدة المحددة</small>
                                </div>
                            </div>
                        </div>

                        <!-- Daily Options -->
                        <div id="daily-options" class="recurrence-options" style="display: none;">
                            <p class="text-muted">سيتم تكرار المهمة كل {{ $task->getRecurrenceConfig()['interval'] ?? 1 }} يوم</p>
                        </div>

                        <!-- Weekly Options -->
                        <div id="weekly-options" class="recurrence-options" style="display: none;">
                            <div class="form-group">
                                <label>أيام الأسبوع</label>
                                <div class="row">
                                    @php
                                        $daysOfWeek = [
                                            1 => 'الاثنين',
                                            2 => 'الثلاثاء', 
                                            3 => 'الأربعاء',
                                            4 => 'الخميس',
                                            5 => 'الجمعة',
                                            6 => 'السبت',
                                            0 => 'الأحد'
                                        ];
                                        $selectedDays = $task->getRecurrenceConfig()['days_of_week'] ?? [1];
                                    @endphp
                                    @foreach($daysOfWeek as $day => $label)
                                        <div class="col-md-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="day_{{ $day }}" name="recurrence_config[days_of_week][]" 
                                                       value="{{ $day }}" {{ in_array($day, $selectedDays) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="day_{{ $day }}">{{ $label }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Options -->
                        <div id="monthly-options" class="recurrence-options" style="display: none;">
                            <div class="form-group">
                                <label for="day_of_month">يوم من الشهر</label>
                                <select class="form-control" id="day_of_month" name="recurrence_config[day_of_month]">
                                    @for($i = 1; $i <= 31; $i++)
                                        <option value="{{ $i }}" {{ ($task->getRecurrenceConfig()['day_of_month'] ?? 1) == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Yearly Options -->
                        <div id="yearly-options" class="recurrence-options" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="month_of_year">الشهر</label>
                                        <select class="form-control" id="month_of_year" name="recurrence_config[month_of_year]">
                                            @php
                                                $months = [
                                                    1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                                                    5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                                                    9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                                                ];
                                            @endphp
                                            @foreach($months as $month => $label)
                                                <option value="{{ $month }}" {{ ($task->getRecurrenceConfig()['month_of_year'] ?? 1) == $month ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="yearly_day_of_month">اليوم</label>
                                        <select class="form-control" id="yearly_day_of_month" name="recurrence_config[day_of_month]">
                                            @for($i = 1; $i <= 31; $i++)
                                                <option value="{{ $i }}" {{ ($task->getRecurrenceConfig()['day_of_month'] ?? 1) == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recurrence_start_date">تاريخ البدء</label>
                                    <input type="date" class="form-control" id="recurrence_start_date" 
                                           name="recurrence_start_date" value="{{ $task->recurrence_start_date?->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recurrence_end_date">تاريخ الانتهاء (اختياري)</label>
                                    <input type="date" class="form-control" id="recurrence_end_date" 
                                           name="recurrence_end_date" value="{{ $task->recurrence_end_date?->format('Y-m-d') }}">
                                    <small class="text-muted">اتركه فارغاً للتكرار إلى ما لا نهاية</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recurrence_max_occurrences">العدد الأقصى للتكرارات (اختياري)</label>
                                    <input type="number" class="form-control" id="recurrence_max_occurrences" 
                                           name="recurrence_max_occurrences" value="{{ $task->recurrence_max_occurrences }}" min="1">
                                    <small class="text-muted">اتركه فارغاً للتكرار إلى ما لا نهاية</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch mt-4">
                                        <input type="checkbox" class="custom-control-input" id="recurring_active" 
                                               name="recurring_active" value="1" {{ $task->recurring_active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="recurring_active">تفعيل التكرار</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-info btn-sm" id="preview-btn">
                                <i class="feather-eye mr-1"></i> معاينة التكرارات القادمة
                            </button>
                        </div>

                        <!-- Preview Container -->
                        <div id="preview-container" style="display: none;">
                            <div class="alert alert-info">
                                <h6>التكرارات القادمة:</h6>
                                <div id="preview-content"></div>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <a href="{{ route('recurring-tasks.show', $task) }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save mr-1"></i> حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">معلومات المهمة</h4>
                </div>
                <div class="card-body">
                    <p><strong>العنوان:</strong> {{ $task->title }}</p>
                    <p><strong>الوصف:</strong> {{ $task->description ?? 'لا يوجد وصف' }}</p>
                    <p><strong>الفئة:</strong> {{ $task->category->name ?? 'بدون فئة' }}</p>
                    <p><strong>الأولوية:</strong> 
                        <span class="badge badge-{{ $task->priority_color }}">{{ $task->priority_label }}</span>
                    </p>
                    <p><strong>تاريخ الإنشاء:</strong> {{ $task->created_at->format('Y-m-d') }}</p>
                    <p><strong>عدد المهام المنشأة:</strong> {{ $task->recurrence_current_count }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">تعليمات</h4>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="feather-info text-info mr-2"></i> اختر نمط التكرار المناسب</li>
                        <li><i class="feather-info text-info mr-2"></i> حدد فترة التكرار بالأرقام</li>
                        <li><i class="feather-info text-info mr-2"></i> استخدم المعاينة لرؤية النتائج</li>
                        <li><i class="feather-info text-info mr-2"></i> يمكن تحديد تاريخ انتهاء أو عدد أقصى</li>
                        <li><i class="feather-info text-info mr-2"></i> يمكن إيقاف التكرار مؤقتاً</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Show appropriate options based on recurrence pattern
    function updateRecurrenceOptions() {
        const pattern = $('#recurrence_pattern').val();
        $('.recurrence-options').hide();
        $('#' + pattern + '-options').show();
    }

    $('#recurrence_pattern').on('change', updateRecurrenceOptions);
    updateRecurrenceOptions(); // Initial call

    // Preview functionality
    $('#preview-btn').on('click', function() {
        const formData = new FormData($('#recurring-form')[0]);
        const data = {};
        
        // Convert FormData to regular object
        for (let [key, value] of formData.entries()) {
            if (key.includes('[') && key.includes(']')) {
                // Handle nested arrays/objects
                const matches = key.match(/([^[]+)\[([^\]]*)\](?:\[([^\]]*)\])?/);
                if (matches) {
                    const baseKey = matches[1];
                    const subKey = matches[2];
                    const subSubKey = matches[3];
                    
                    if (!data[baseKey]) data[baseKey] = {};
                    
                    if (subSubKey !== undefined) {
                        if (!data[baseKey][subKey]) data[baseKey][subKey] = [];
                        data[baseKey][subKey].push(value);
                    } else if (subKey === '') {
                        if (!Array.isArray(data[baseKey])) data[baseKey] = [];
                        data[baseKey].push(value);
                    } else {
                        data[baseKey][subKey] = value;
                    }
                }
            } else {
                data[key] = value;
            }
        }

        $.ajax({
            url: '{{ route("recurring-tasks.preview") }}',
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let content = '<ul class="list-unstyled mb-0">';
                response.occurrences.forEach(function(date) {
                    content += '<li><i class="feather-calendar mr-2"></i>' + date + '</li>';
                });
                content += '</ul>';
                
                $('#preview-content').html(content);
                $('#preview-container').show();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response.errors) {
                    let errorMsg = 'أخطاء في التكوين:\n';
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
                    alert('حدث خطأ أثناء المعاينة');
                }
            }
        });
    });

    // Form submission
    $('#recurring-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {};
        
        // Convert FormData to regular object
        for (let [key, value] of formData.entries()) {
            if (key.includes('[') && key.includes(']')) {
                // Handle nested arrays/objects
                const matches = key.match(/([^[]+)\[([^\]]*)\](?:\[([^\]]*)\])?/);
                if (matches) {
                    const baseKey = matches[1];
                    const subKey = matches[2];
                    const subSubKey = matches[3];
                    
                    if (!data[baseKey]) data[baseKey] = {};
                    
                    if (subSubKey !== undefined) {
                        if (!data[baseKey][subKey]) data[baseKey][subKey] = [];
                        data[baseKey][subKey].push(value);
                    } else if (subKey === '') {
                        if (!Array.isArray(data[baseKey])) data[baseKey] = [];
                        data[baseKey].push(value);
                    } else {
                        data[baseKey][subKey] = value;
                    }
                }
            } else {
                data[key] = value;
            }
        }

        $.ajax({
            url: '{{ route("recurring-tasks.update", $task) }}',
            method: 'PUT',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert(response.message);
                window.location.href = '{{ route("recurring-tasks.show", $task) }}';
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
</script>
@endpush
@endsection