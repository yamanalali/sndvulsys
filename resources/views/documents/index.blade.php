@extends('layouts.app')

@section('title', 'المستندات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-alt text-primary"></i>
                        المستندات
                    </h4>
                    <a href="{{ route('documents.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        رفع مستند جديد
                    </a>
                </div>
                <div class="card-body">
                    <!-- فلتر البحث -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="البحث في المستندات..." id="searchInput">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="filterType">
                                <option value="">جميع الأنواع</option>
                                <option value="pdf">PDF</option>
                                <option value="doc">Word</option>
                                <option value="xls">Excel</option>
                                <option value="ppt">PowerPoint</option>
                                <option value="jpg">صور</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>
                    </div>

                    <!-- إحصائيات سريعة -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $documents->total() }}</h5>
                                    <small>إجمالي المستندات</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $documents->where('status', 'active')->count() }}</h5>
                                    <small>مستندات نشطة</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $documents->where('privacy_level', 'private')->count() }}</h5>
                                    <small>مستندات خاصة</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $documents->where('expires_at', '!=', null)->count() }}</h5>
                                    <small>مستندات محددة المدة</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- قائمة المستندات -->
                    <div class="row" id="documentsContainer">
                        @forelse($documents as $document)
                        <div class="col-md-6 col-lg-4 mb-4 document-item" data-type="{{ $document->file_type }}">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-{{ getFileIcon($document->file_type) }} text-primary me-2"></i>
                                        <span class="badge bg-{{ getStatusColor($document->status) }}">{{ $document->status_text }}</span>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('documents.show', $document) }}">
                                                <i class="fas fa-eye"></i> عرض
                                            </a></li>
                                            <li><a class="dropdown-item" href="{{ route('documents.download', $document) }}">
                                                <i class="fas fa-download"></i> تحميل
                                            </a></li>
                                            @if($document->user_id === auth()->id() || auth()->user()->hasDocumentPermission($document->id, 'edit'))
                                            <li><a class="dropdown-item" href="{{ route('documents.edit', $document) }}">
                                                <i class="fas fa-edit"></i> تعديل
                                            </a></li>
                                            @endif
                                            <li><a class="dropdown-item" href="{{ route('documents.backups', $document) }}">
                                                <i class="fas fa-copy"></i> النسخ الاحتياطية
                                            </a></li>
                                            @if($document->user_id === auth()->id() || auth()->user()->hasDocumentPermission($document->id, 'share'))
                                            <li><a class="dropdown-item" href="#" onclick="shareDocument({{ $document->id }})">
                                                <i class="fas fa-share"></i> مشاركة
                                            </a></li>
                                            @endif
                                            @if($document->user_id === auth()->id() || auth()->user()->hasDocumentPermission($document->id, 'delete'))
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteDocument({{ $document->id }})">
                                                <i class="fas fa-trash"></i> حذف
                                            </a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title text-truncate" title="{{ $document->title }}">
                                        {{ $document->title }}
                                    </h6>
                                    <p class="card-text text-muted small">
                                        {{ Str::limit($document->description, 100) }}
                                    </p>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <small class="text-muted">الحجم</small>
                                            <div class="fw-bold">{{ $document->formatted_size }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">النوع</small>
                                            <div class="fw-bold">{{ strtoupper($document->file_type) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $document->user->avatar ?? asset('files/assets/images/avatar-1.jpg') }}" 
                                                 class="rounded-circle me-2" width="24" height="24">
                                            <small class="text-muted">{{ $document->user->name }}</small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block">{{ $document->created_at->format('Y-m-d') }}</small>
                                            @if($document->expires_at)
                                            <small class="text-{{ $document->isExpired() ? 'danger' : 'warning' }}">
                                                ينتهي في {{ $document->expires_at->format('Y-m-d') }}
                                            </small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                        <span class="badge bg-{{ getPrivacyColor($document->privacy_level) }}">
                                            {{ $document->privacy_level_text }}
                                        </span>
                                        @if($document->user_id === auth()->id() || auth()->user()->hasDocumentPermission($document->id, 'delete'))
                                        <form action="{{ route('documents.destroy', $document) }}" method="POST" class="d-inline delete-form" data-document-id="{{ $document->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirmDelete(event, {{ $document->id }})" 
                                                    title="حذف المستند">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد مستندات</h5>
                                <p class="text-muted">ابدأ برفع مستند جديد</p>
                                <a href="{{ route('documents.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    رفع مستند جديد
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <!-- ترقيم الصفحات -->
                    @if($documents->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $documents->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal مشاركة المستند -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">مشاركة المستند</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="shareForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اختر المستخدم</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">اختر مستخدم...</option>
                            @foreach(\App\Models\User::where('id', '!=', auth()->id())->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع الصلاحية</label>
                        <select name="permission_type" class="form-control" required>
                            <option value="view">عرض فقط</option>
                            <option value="download">تحميل</option>
                            <option value="edit">تعديل</option>
                            <option value="share">مشاركة</option>
                            <option value="admin">إدارة كاملة</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاريخ انتهاء الصلاحية (اختياري)</label>
                        <input type="date" name="expires_at" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">مشاركة</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// البحث والفلترة
document.getElementById('searchInput').addEventListener('input', filterDocuments);
document.getElementById('filterType').addEventListener('change', filterDocuments);

// معالجة حذف المستندات عبر الـ forms
document.addEventListener('DOMContentLoaded', function() {
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const documentId = this.dataset.documentId;
            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            
            // إظهار مؤشر التحميل
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;
            
            // إرسال الـ form
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
            }).then(response => {
                if (response.ok) {
                    showNotification('تم حذف المستند بنجاح', 'success');
                    const documentCard = this.closest('.document-item');
                    if (documentCard) {
                        documentCard.style.opacity = '0.5';
                        setTimeout(() => {
                            documentCard.remove();
                            updateDocumentStats();
                        }, 500);
                    } else {
                        location.reload();
                    }
                } else {
                    button.innerHTML = originalText;
                    button.disabled = false;
                    response.json().then(data => {
                        showNotification(data.message || 'حدث خطأ أثناء حذف المستند', 'error');
                    }).catch(() => {
                        showNotification('حدث خطأ أثناء حذف المستند', 'error');
                    });
                }
            }).catch(error => {
                console.error('Delete error:', error);
                button.innerHTML = originalText;
                button.disabled = false;
                showNotification('حدث خطأ في الاتصال', 'error');
            });
        });
    });
});

function filterDocuments() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const filterType = document.getElementById('filterType').value;
    const documents = document.querySelectorAll('.document-item');
    
    documents.forEach(doc => {
        const title = doc.querySelector('.card-title').textContent.toLowerCase();
        const type = doc.dataset.type;
        const matchesSearch = title.includes(searchTerm);
        const matchesType = !filterType || type === filterType;
        
        doc.style.display = matchesSearch && matchesType ? 'block' : 'none';
    });
}

// مشاركة المستند
function shareDocument(documentId) {
    const modal = new bootstrap.Modal(document.getElementById('shareModal'));
    const form = document.getElementById('shareForm');
    form.action = `/documents/${documentId}/share`;
    modal.show();
}

// حذف المستند (للأزرار في القائمة المنسدلة)
function deleteDocument(documentId) {
    console.log('حذف المستند:', documentId);
    
    if (confirm('هل أنت متأكد من حذف هذا المستند؟\n\n⚠️ تحذير: لا يمكن التراجع عن هذا الإجراء!')) {
        // إظهار مؤشر التحميل
        const deleteButton = event.target.closest('.dropdown-item') || event.target.closest('.btn');
        const originalText = deleteButton.innerHTML;
        deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحذف...';
        deleteButton.style.pointerEvents = 'none';
        
        // الحصول على CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        console.log('CSRF Token:', csrfToken);
        
        fetch(`/documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        }).then(response => {
            console.log('Response status:', response.status);
            
            if (response.ok) {
                showNotification('تم حذف المستند بنجاح', 'success');
                const documentCard = event.target.closest('.document-item');
                if (documentCard) {
                    documentCard.style.opacity = '0.5';
                    setTimeout(() => {
                        documentCard.remove();
                        updateDocumentStats();
                    }, 500);
                } else {
                    location.reload();
                }
            } else {
                deleteButton.innerHTML = originalText;
                deleteButton.style.pointerEvents = 'auto';
                
                response.json().then(data => {
                    console.log('Error response:', data);
                    showNotification(data.message || 'حدث خطأ أثناء حذف المستند', 'error');
                }).catch(() => {
                    showNotification('حدث خطأ أثناء حذف المستند', 'error');
                });
            }
        }).catch(error => {
            console.error('Fetch error:', error);
            deleteButton.innerHTML = originalText;
            deleteButton.style.pointerEvents = 'auto';
            showNotification('حدث خطأ في الاتصال', 'error');
        });
    }
}

// تأكيد الحذف (للأزرار في البطاقات)
function confirmDelete(event, documentId) {
    console.log('تأكيد حذف المستند:', documentId);
    
    if (confirm('هل أنت متأكد من حذف هذا المستند؟\n\n⚠️ تحذير: لا يمكن التراجع عن هذا الإجراء!')) {
        // إظهار مؤشر التحميل
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        // السماح بإرسال الـ form
        return true;
    } else {
        // منع إرسال الـ form
        event.preventDefault();
        return false;
    }
}

// دالة إظهار الإشعارات
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    
    // إزالة الإشعار تلقائياً بعد 5 ثواني
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// دالة تحديث الإحصائيات
function updateDocumentStats() {
    const totalDocs = document.querySelectorAll('.document-item').length;
    const activeDocs = document.querySelectorAll('.document-item .badge.bg-success').length;
    const privateDocs = document.querySelectorAll('.document-item .badge.bg-warning').length;
    const timeLimitedDocs = document.querySelectorAll('.document-item .text-warning').length;
    
    // تحديث الإحصائيات في البطاقات
    const statsCards = document.querySelectorAll('.card-body .row .col-md-3 .card-body h5');
    if (statsCards.length >= 4) {
        statsCards[0].textContent = totalDocs;
        statsCards[1].textContent = activeDocs;
        statsCards[2].textContent = privateDocs;
        statsCards[3].textContent = timeLimitedDocs;
    }
}
</script>
@endpush

@php
function getFileIcon($type) {
    $icons = [
        'pdf' => 'pdf',
        'doc' => 'word',
        'docx' => 'word',
        'xls' => 'excel',
        'xlsx' => 'excel',
        'ppt' => 'powerpoint',
        'pptx' => 'powerpoint',
        'jpg' => 'image',
        'jpeg' => 'image',
        'png' => 'image',
        'gif' => 'image',
        'txt' => 'alt',
    ];
    return $icons[$type] ?? 'alt';
}

function getStatusColor($status) {
    $colors = [
        'active' => 'success',
        'archived' => 'warning',
        'deleted' => 'danger',
    ];
    return $colors[$status] ?? 'secondary';
}

function getPrivacyColor($level) {
    $colors = [
        'public' => 'success',
        'private' => 'warning',
        'restricted' => 'danger',
    ];
    return $colors[$level] ?? 'secondary';
}
@endphp 