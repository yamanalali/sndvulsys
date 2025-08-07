@extends('layouts.app')

@section('title', 'عرض البحث المحفوظ')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">
                            <i class="fas fa-search"></i>
                            البحث المحفوظ: {{ $search->saved_name }}
                        </h4>
                        <div>
                            <button type="button" class="btn btn-info btn-sm" id="shareSearch">
                                <i class="fas fa-share"></i>
                                مشاركة
                            </button>
                            <a href="{{ route('advanced-search.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i>
                                عودة
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- تفاصيل البحث -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>تفاصيل البحث</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>اسم البحث:</strong></td>
                                    <td>{{ $search->saved_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>نوع البحث:</strong></td>
                                    <td>{{ $searchTypes[$search->search_type] ?? 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>مصطلح البحث:</strong></td>
                                    <td>{{ $search->search_term ?: 'لا يوجد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>تاريخ التنفيذ:</strong></td>
                                    <td>{{ $search->executed_at ? $search->executed_at->format('Y-m-d H:i:s') : 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>وقت التنفيذ:</strong></td>
                                    <td>{{ $search->execution_time_text }}</td>
                                </tr>
                                <tr>
                                    <td><strong>عدد النتائج:</strong></td>
                                    <td>{{ $search->results_count_text }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>المرشحات المطبقة</h6>
                            @if($search->filters)
                                <ul class="list-group">
                                    @foreach($search->filters as $key => $value)
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>{{ ucfirst($key) }}:</span>
                                            <span class="badge bg-primary">{{ $value }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">لا توجد مرشحات مطبقة</p>
                            @endif
                        </div>
                    </div>

                    @if($search->notes)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6>ملاحظات البحث</h6>
                            <div class="alert alert-info">
                                {{ $search->notes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- إعادة تنفيذ البحث -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6>إعادة تنفيذ البحث</h6>
                            <form id="reExecuteForm">
                                @csrf
                                <input type="hidden" name="search_term" value="{{ $search->search_term }}">
                                <input type="hidden" name="search_type" value="{{ $search->search_type }}">
                                @if($search->filters)
                                    @foreach($search->filters as $key => $value)
                                        <input type="hidden" name="filters[{{ $key }}]" value="{{ $value }}">
                                    @endforeach
                                @endif
                                @if($search->sort_options)
                                    @foreach($search->sort_options as $key => $value)
                                        <input type="hidden" name="sort_options[{{ $key }}]" value="{{ $value }}">
                                    @endforeach
                                @endif
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-play"></i>
                                    إعادة تنفيذ البحث
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- نتائج البحث الحالية -->
                    <div id="currentResults" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>النتائج الحالية</h6>
                                <div id="currentResultsInfo"></div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" class="btn btn-success btn-sm" id="exportCurrentResults">
                                    <i class="fas fa-download"></i>
                                    تصدير النتائج
                                </button>
                            </div>
                        </div>
                        
                        <div id="currentResultsTable"></div>
                        <div id="currentPagination"></div>
                    </div>

                    <!-- تاريخ البحث -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>تاريخ البحث</h6>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">تم إنشاء البحث</h6>
                                        <p class="timeline-text">{{ $search->created_at->format('Y-m-d H:i:s') }}</p>
                                    </div>
                                </div>
                                
                                @if($search->executed_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">تم تنفيذ البحث</h6>
                                        <p class="timeline-text">
                                            {{ $search->executed_at->format('Y-m-d H:i:s') }}<br>
                                            وقت التنفيذ: {{ $search->execution_time_text }}<br>
                                            النتائج: {{ $search->results_count_text }}
                                        </p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($search->is_saved)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">تم حفظ البحث</h6>
                                        <p class="timeline-text">{{ $search->updated_at->format('Y-m-d H:i:s') }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal للمشاركة -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">مشاركة البحث</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">رابط المشاركة</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareUrl" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="copyShareUrl">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    يمكن للمستخدمين الآخرين الوصول إلى هذا البحث عبر الرابط أعلاه
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 50px;
}

.timeline-marker {
    position: absolute;
    left: 11px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin: 0 0 10px 0;
    font-size: 16px;
    font-weight: bold;
}

.timeline-text {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let currentSearchId = null;
    
    // إعادة تنفيذ البحث
    $('#reExecuteForm').on('submit', function(e) {
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
                    displayCurrentResults(response.results);
                    $('#currentResultsInfo').html(`
                        <div class="alert alert-info">
                            <strong>تم العثور على ${response.total_results} نتيجة</strong><br>
                            وقت التنفيذ: ${response.execution_time} مللي ثانية
                        </div>
                    `);
                    $('#currentResults').show();
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
    
    // عرض النتائج الحالية
    function displayCurrentResults(results) {
        let tableHtml = '<table class="table table-striped">';
        tableHtml += '<thead><tr>';
        
        // إنشاء رؤوس الجدول حسب نوع البحث
        const searchType = '{{ $search->search_type }}';
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
        $('#currentResultsTable').html(tableHtml);
        
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
            $('#currentPagination').html(paginationHtml);
        }
    }
    
    // مشاركة البحث
    $('#shareSearch').click(function() {
        $.ajax({
            url: '{{ route("advanced-search.share", $search->id) }}',
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#shareUrl').val(response.share_url);
                    $('#shareModal').modal('show');
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert(xhr.responseJSON.error);
                } else {
                    alert('حدث خطأ أثناء مشاركة البحث');
                }
            }
        });
    });
    
    // نسخ رابط المشاركة
    $('#copyShareUrl').click(function() {
        const shareUrl = $('#shareUrl');
        shareUrl.select();
        document.execCommand('copy');
        
        // إظهار رسالة نجاح
        const button = $(this);
        const originalText = button.html();
        button.html('<i class="fas fa-check"></i>');
        button.removeClass('btn-outline-secondary').addClass('btn-success');
        
        setTimeout(function() {
            button.html(originalText);
            button.removeClass('btn-success').addClass('btn-outline-secondary');
        }, 2000);
    });
    
    // تصدير النتائج الحالية
    $('#exportCurrentResults').click(function() {
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