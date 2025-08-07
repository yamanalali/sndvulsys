@extends('layouts.app')

@section('title', 'البحث المتقدم')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-search"></i>
                        البحث المتقدم
                    </h4>
                </div>
                <div class="card-body">
                    <!-- نموذج البحث -->
                    <form id="advancedSearchForm" class="mb-4">
                        @csrf
                        
                        <!-- نوع البحث -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="search_type" class="form-label">نوع البحث</label>
                                <select class="form-select" id="search_type" name="search_type" required>
                                    <option value="">اختر نوع البحث</option>
                                    @foreach($searchTypes as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="search_term" class="form-label">مصطلح البحث</label>
                                <input type="text" class="form-control" id="search_term" name="search_term" 
                                       placeholder="أدخل مصطلح البحث...">
                            </div>
                        </div>

                        <!-- المرشحات -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="mb-3">المرشحات</h6>
                                
                                <!-- مرشح الحالة -->
                                <div class="row mb-2">
                                    <div class="col-md-3">
                                        <label class="form-label">الحالة</label>
                                        <select class="form-select" name="filters[status]">
                                            <option value="">جميع الحالات</option>
                                            @foreach($filterOptions['status'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- مرشح الأولوية -->
                                    <div class="col-md-3">
                                        <label class="form-label">الأولوية</label>
                                        <select class="form-select" name="filters[priority]">
                                            <option value="">جميع الأولويات</option>
                                            @foreach($filterOptions['priority'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- مرشح النطاق الزمني -->
                                    <div class="col-md-3">
                                        <label class="form-label">النطاق الزمني</label>
                                        <select class="form-select" name="filters[date_range]">
                                            <option value="">جميع التواريخ</option>
                                            @foreach($filterOptions['date_range'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- مرشح عدد النتائج في الصفحة -->
                                    <div class="col-md-3">
                                        <label class="form-label">نتائج في الصفحة</label>
                                        <select class="form-select" name="per_page">
                                            <option value="15">15</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- خيارات الترتيب -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="mb-3">خيارات الترتيب</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">الترتيب الأساسي</label>
                                        <select class="form-select" name="sort_options[primary]">
                                            <option value="created_at_desc">الأحدث أولاً</option>
                                            <option value="created_at_asc">الأقدم أولاً</option>
                                            <option value="updated_at_desc">آخر تحديث</option>
                                            <option value="name_asc">الاسم (أ-ي)</option>
                                            <option value="name_desc">الاسم (ي-أ)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">الترتيب الثانوي</label>
                                        <select class="form-select" name="sort_options[secondary]">
                                            <option value="">لا يوجد</option>
                                            <option value="status_asc">الحالة (أ-ي)</option>
                                            <option value="priority_desc">الأولوية (عاجلة أولاً)</option>
                                            <option value="priority_asc">الأولوية (منخفضة أولاً)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">الترتيب الثالث</label>
                                        <select class="form-select" name="sort_options[tertiary]">
                                            <option value="">لا يوجد</option>
                                            <option value="id_asc">الرقم (تصاعدي)</option>
                                            <option value="id_desc">الرقم (تنازلي)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار البحث -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                    بحث
                                </button>
                                <button type="button" class="btn btn-secondary" id="clearForm">
                                    <i class="fas fa-eraser"></i>
                                    مسح
                                </button>
                                <button type="button" class="btn btn-info" id="saveSearchBtn" style="display: none;">
                                    <i class="fas fa-save"></i>
                                    حفظ البحث
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- نتائج البحث -->
                    <div id="searchResults" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>نتائج البحث</h6>
                                <div id="resultsInfo"></div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" class="btn btn-success btn-sm" id="exportResults">
                                    <i class="fas fa-download"></i>
                                    تصدير النتائج
                                </button>
                            </div>
                        </div>
                        
                        <div id="resultsTable"></div>
                        <div id="pagination"></div>
                    </div>

                    <!-- البحوث المحفوظة -->
                    @if($savedSearches->count() > 0)
                    <div class="mt-4">
                        <h6>البحوث المحفوظة</h6>
                        <div class="row">
                            @foreach($savedSearches as $search)
                            <div class="col-md-4 mb-2">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $search->saved_name }}</h6>
                                        <p class="card-text small">
                                            {{ $search->search_term ?: 'بحث بدون مصطلح' }}
                                        </p>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-primary btn-sm load-saved-search" 
                                                    data-search-id="{{ $search->id }}">
                                                تحميل
                                            </button>
                                            <button class="btn btn-danger btn-sm delete-saved-search" 
                                                    data-search-id="{{ $search->id }}">
                                                حذف
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- البحوث الشائعة -->
                    @if($popularSearches->count() > 0)
                    <div class="mt-4">
                        <h6>البحوث الشائعة</h6>
                        <div class="row">
                            @foreach($popularSearches as $search)
                            <div class="col-md-3 mb-2">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $search->search_term ?: 'بحث بدون مصطلح' }}</h6>
                                        <p class="card-text small">
                                            {{ $searchTypes[$search->search_type] ?? 'غير محدد' }}
                                        </p>
                                        <small class="text-muted">
                                            {{ $search->usage_count }} استخدام
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- إحصائيات البحث -->
                    <div class="mt-4">
                        <h6>إحصائيات البحث</h6>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $statistics['total_searches'] }}</h5>
                                        <p class="card-text small">إجمالي البحوث</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $statistics['saved_searches'] }}</h5>
                                        <p class="card-text small">البحوث المحفوظة</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $statistics['recent_searches'] }}</h5>
                                        <p class="card-text small">البحوث الحديثة</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ number_format($statistics['total_results_found']) }}</h5>
                                        <p class="card-text small">النتائج المجموعة</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ round($statistics['avg_execution_time'] ?? 0, 2) }}</h5>
                                        <p class="card-text small">متوسط وقت التنفيذ (مللي ثانية)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal لحفظ البحث -->
<div class="modal fade" id="saveSearchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">حفظ البحث</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="saveSearchForm">
                    <div class="mb-3">
                        <label for="search_name" class="form-label">اسم البحث</label>
                        <input type="text" class="form-control" id="search_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="search_notes" class="form-label">ملاحظات (اختياري)</label>
                        <textarea class="form-control" id="search_notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="confirmSaveSearch">حفظ</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentSearchId = null;
    
    // تنفيذ البحث
    $('#advancedSearchForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("advanced-search.search") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    currentSearchId = response.search_id;
                    displayResults(response.results);
                    $('#resultsInfo').html(`
                        <div class="alert alert-info">
                            <strong>تم العثور على ${response.total_results} نتيجة</strong><br>
                            وقت التنفيذ: ${response.execution_time} مللي ثانية
                        </div>
                    `);
                    $('#searchResults').show();
                    $('#saveSearchBtn').show();
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorMessage = 'خطأ في البيانات:\n';
                    Object.values(xhr.responseJSON.errors).forEach(errors => {
                        errors.forEach(error => {
                            errorMessage += '- ' + error + '\n';
                        });
                    });
                    alert(errorMessage);
                } else {
                    alert('حدث خطأ أثناء البحث');
                }
            }
        });
    });
    
    // عرض النتائج
    function displayResults(results) {
        let tableHtml = '<table class="table table-striped">';
        tableHtml += '<thead><tr>';
        
        // إنشاء رؤوس الجدول حسب نوع البحث
        const searchType = $('#search_type').val();
        switch(searchType) {
            case 'volunteer_requests':
                tableHtml += '<th>الاسم</th><th>البريد الإلكتروني</th><th>الحالة</th><th>التاريخ</th><th>الإجراءات</th>';
                break;
            case 'submissions':
                tableHtml += '<th>طلب التطوع</th><th>الحالة</th><th>الأولوية</th><th>التاريخ</th><th>الإجراءات</th>';
                break;
            case 'workflows':
                tableHtml += '<th>طلب التطوع</th><th>الخطوة</th><th>الحالة</th><th>التاريخ</th><th>الإجراءات</th>';
                break;
            default:
                tableHtml += '<th>الاسم</th><th>الحالة</th><th>التاريخ</th><th>الإجراءات</th>';
        }
        
        tableHtml += '</tr></thead><tbody>';
        
        results.data.forEach(item => {
            tableHtml += '<tr>';
            switch(searchType) {
                case 'volunteer_requests':
                    tableHtml += `
                        <td>${item.full_name}</td>
                        <td>${item.email}</td>
                        <td><span class="badge bg-${getStatusColor(item.status)}">${getStatusText(item.status)}</span></td>
                        <td>${formatDate(item.created_at)}</td>
                        <td><a href="/volunteer-requests/${item.id}" class="btn btn-sm btn-primary">عرض</a></td>
                    `;
                    break;
                case 'submissions':
                    tableHtml += `
                        <td>${item.volunteer_request ? item.volunteer_request.full_name : 'غير محدد'}</td>
                        <td><span class="badge bg-${getStatusColor(item.status)}">${getStatusText(item.status)}</span></td>
                        <td><span class="badge bg-${getPriorityColor(item.priority)}">${getPriorityText(item.priority)}</span></td>
                        <td>${formatDate(item.created_at)}</td>
                        <td><a href="/submissions/${item.id}" class="btn btn-sm btn-primary">عرض</a></td>
                    `;
                    break;
                default:
                    tableHtml += `
                        <td>${item.name || item.title || 'غير محدد'}</td>
                        <td><span class="badge bg-${getStatusColor(item.status)}">${getStatusText(item.status)}</span></td>
                        <td>${formatDate(item.created_at)}</td>
                        <td><a href="/${searchType}/${item.id}" class="btn btn-sm btn-primary">عرض</a></td>
                    `;
            }
            tableHtml += '</tr>';
        });
        
        tableHtml += '</tbody></table>';
        $('#resultsTable').html(tableHtml);
        
        // إضافة الترقيم
        if (results.last_page > 1) {
            let paginationHtml = '<nav><ul class="pagination justify-content-center">';
            
            if (results.current_page > 1) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${results.current_page - 1}">السابق</a></li>`;
            }
            
            for (let i = 1; i <= results.last_page; i++) {
                if (i === results.current_page) {
                    paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                } else {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
            }
            
            if (results.current_page < results.last_page) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${results.current_page + 1}">التالي</a></li>`;
            }
            
            paginationHtml += '</ul></nav>';
            $('#pagination').html(paginationHtml);
        }
    }
    
    // مسح النموذج
    $('#clearForm').click(function() {
        $('#advancedSearchForm')[0].reset();
        $('#searchResults').hide();
        $('#saveSearchBtn').hide();
        currentSearchId = null;
    });
    
    // حفظ البحث
    $('#saveSearchBtn').click(function() {
        if (!currentSearchId) {
            alert('قم بتنفيذ البحث أولاً');
            return;
        }
        $('#saveSearchModal').modal('show');
    });
    
    $('#confirmSaveSearch').click(function() {
        const name = $('#search_name').val();
        const notes = $('#search_notes').val();
        
        if (!name) {
            alert('يرجى إدخال اسم للبحث');
            return;
        }
        
        $.ajax({
            url: '{{ route("advanced-search.save") }}',
            method: 'POST',
            data: {
                search_id: currentSearchId,
                name: name,
                notes: notes,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('تم حفظ البحث بنجاح');
                    $('#saveSearchModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert(xhr.responseJSON.error);
                } else {
                    alert('حدث خطأ أثناء حفظ البحث');
                }
            }
        });
    });
    
    // تحميل البحث المحفوظ
    $('.load-saved-search').click(function() {
        const searchId = $(this).data('search-id');
        window.location.href = `/advanced-search/saved/${searchId}`;
    });
    
    // حذف البحث المحفوظ
    $('.delete-saved-search').click(function() {
        if (!confirm('هل أنت متأكد من حذف هذا البحث؟')) {
            return;
        }
        
        const searchId = $(this).data('search-id');
        const button = $(this);
        
        $.ajax({
            url: `/advanced-search/${searchId}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    button.closest('.col-md-4').remove();
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert(xhr.responseJSON.error);
                } else {
                    alert('حدث خطأ أثناء حذف البحث');
                }
            }
        });
    });
    
    // تصدير النتائج
    $('#exportResults').click(function() {
        if (!currentSearchId) {
            alert('قم بتنفيذ البحث أولاً');
            return;
        }
        
        const format = prompt('اختر صيغة التصدير (csv, excel, pdf):', 'csv');
        if (format && ['csv', 'excel', 'pdf'].includes(format)) {
            window.open(`/advanced-search/export?search_id=${currentSearchId}&format=${format}`, '_blank');
        }
    });
    
    // دوال مساعدة
    function getStatusColor(status) {
        const colors = {
            'pending': 'warning',
            'in_review': 'info',
            'approved': 'success',
            'rejected': 'danger',
            'completed': 'success'
        };
        return colors[status] || 'secondary';
    }
    
    function getStatusText(status) {
        const texts = {
            'pending': 'معلق',
            'in_review': 'قيد المراجعة',
            'approved': 'موافق عليه',
            'rejected': 'مرفوض',
            'completed': 'مكتمل'
        };
        return texts[status] || status;
    }
    
    function getPriorityColor(priority) {
        const colors = {
            'low': 'success',
            'medium': 'info',
            'high': 'warning',
            'urgent': 'danger'
        };
        return colors[priority] || 'secondary';
    }
    
    function getPriorityText(priority) {
        const texts = {
            'low': 'منخفضة',
            'medium': 'متوسطة',
            'high': 'عالية',
            'urgent': 'عاجلة'
        };
        return texts[priority] || priority;
    }
    
    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('ar-SA');
    }
});
</script>
@endpush 