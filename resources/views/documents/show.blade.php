@extends('layouts.app')

@section('title', $document->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- تفاصيل المستند -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-{{ getFileIcon($document->file_type) }} text-primary fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $document->title }}</h4>
                            <small class="text-muted">تم الرفع بواسطة {{ $document->user->name }}</small>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
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
                    <!-- معاينة الملف -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-eye"></i>
                            معاينة الملف
                        </h5>
                        <div class="file-preview">
                            @if(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ $document->file_url }}" class="img-fluid rounded" alt="{{ $document->title }}">
                            @elseif($document->file_type === 'pdf')
                                <div class="pdf-preview">
                                    <iframe src="{{ $document->file_url }}" width="100%" height="500" frameborder="0"></iframe>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-file-{{ getFileIcon($document->file_type) }} fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">{{ $document->file_name }}</h5>
                                    <p class="text-muted">لا يمكن معاينة هذا النوع من الملفات</p>
                                    <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                                        <i class="fas fa-download"></i>
                                        تحميل الملف
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- وصف المستند -->
                    @if($document->description)
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle"></i>
                            الوصف
                        </h5>
                        <p class="text-muted">{{ $document->description }}</p>
                    </div>
                    @endif

                    <!-- معلومات الملف -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-cog"></i>
                            معلومات الملف
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted">اسم الملف:</td>
                                        <td>{{ $document->file_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">نوع الملف:</td>
                                        <td>{{ strtoupper($document->file_type) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">حجم الملف:</td>
                                        <td>{{ $document->formatted_size }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">نوع MIME:</td>
                                        <td>{{ $document->mime_type }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted">تاريخ الرفع:</td>
                                        <td>{{ $document->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">آخر تحديث:</td>
                                        <td>{{ $document->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">مستوى الخصوصية:</td>
                                        <td>
                                            <span class="badge bg-{{ getPrivacyColor($document->privacy_level) }}">
                                                {{ $document->privacy_level_text }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">الحالة:</td>
                                        <td>
                                            <span class="badge bg-{{ getStatusColor($document->status) }}">
                                                {{ $document->status_text }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- تاريخ انتهاء الصلاحية -->
                    @if($document->expires_at)
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-clock"></i>
                            تاريخ انتهاء الصلاحية
                        </h5>
                        <div class="alert alert-{{ $document->isExpired() ? 'danger' : 'warning' }}">
                            <i class="fas fa-{{ $document->isExpired() ? 'exclamation-triangle' : 'clock' }}"></i>
                            {{ $document->isExpired() ? 'انتهت صلاحية هذا المستند' : 'ينتهي هذا المستند في' }}
                            <strong>{{ $document->expires_at->format('Y-m-d H:i:s') }}</strong>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- معلومات المالك -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user"></i>
                        معلومات المالك
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="{{ $document->user->avatar ?? asset('files/assets/images/avatar-1.jpg') }}" 
                             class="rounded-circle me-3" width="60" height="60">
                        <div>
                            <h6 class="mb-1">{{ $document->user->name }}</h6>
                            <small class="text-muted">{{ $document->user->email }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الصلاحيات -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-key"></i>
                        الصلاحيات
                    </h5>
                </div>
                <div class="card-body">
                    @if($document->permissions->count() > 0)
                        @foreach($document->permissions as $permission)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <small class="text-muted">{{ $permission->user->name }}</small>
                                <br>
                                <span class="badge bg-info">{{ $permission->permission_type }}</span>
                            </div>
                            <small class="text-muted">{{ $permission->created_at->format('Y-m-d') }}</small>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted small">لا توجد صلاحيات خاصة</p>
                    @endif
                </div>
            </div>

            <!-- النسخ الاحتياطية -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-copy"></i>
                        النسخ الاحتياطية
                    </h5>
                    <div class="btn-group" role="group">
                        <button class="btn btn-success btn-sm" onclick="createBackup('drive')" title="إنشاء نسخة في Google Drive">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Google Drive
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="createBackup('local')" title="إنشاء نسخة محلية">
                            <i class="fas fa-save"></i>
                            محلية
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- إحصائيات النسخ الاحتياطية -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">إجمالي النسخ</h6>
                                <span class="badge bg-primary fs-6">{{ $document->backups->count() }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Google Drive</h6>
                                <span class="badge bg-success fs-6">{{ $document->getDriveBackups()->count() }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">محلية</h6>
                                <span class="badge bg-info fs-6">{{ $document->getLocalBackups()->count() }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- قائمة النسخ الاحتياطية الأخيرة -->
                    @if($document->backups->count() > 0)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">آخر النسخ الاحتياطية:</h6>
                        <div class="list-group list-group-flush">
                            @foreach($document->backups->take(3) as $backup)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">{{ $backup->backup_date->format('Y-m-d H:i') }}</small>
                                    <br>
                                    <span class="badge bg-{{ $backup->storage_type === 'drive' ? 'success' : 'primary' }}">
                                        {{ $backup->storage_type === 'drive' ? 'Google Drive' : 'محلية' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="badge bg-{{ $backup->backup_type === 'automatic' ? 'warning' : 'info' }}">
                                        {{ $backup->backup_type === 'automatic' ? 'تلقائية' : 'يدوية' }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-copy fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">لا توجد نسخ احتياطية</p>
                    </div>
                    @endif
                    
                    <!-- رابط عرض جميع النسخ الاحتياطية -->
                    <div class="d-grid">
                        <a href="{{ route('documents.backups', $document) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye"></i>
                            عرض جميع النسخ الاحتياطية
                        </a>
                    </div>
                </div>
            </div>

            <!-- إجراءات سريعة -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt"></i>
                        إجراءات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                            <i class="fas fa-download"></i>
                            تحميل
                        </a>
                        <button class="btn btn-outline-primary" onclick="shareDocument({{ $document->id }})">
                            <i class="fas fa-share"></i>
                            مشاركة
                        </button>
                        <button class="btn btn-outline-success" onclick="createBackup({{ $document->id }})">
                            <i class="fas fa-copy"></i>
                            نسخة احتياطية
                        </button>
                    </div>
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

@push('styles')
<style>
.file-preview {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
}

.pdf-preview {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
}

.table-borderless td {
    padding: 0.5rem 0;
}
</style>
@endpush

@push('scripts')
<script>
function shareDocument(documentId) {
    const modal = new bootstrap.Modal(document.getElementById('shareModal'));
    const form = document.getElementById('shareForm');
    form.action = `/documents/${documentId}/share`;
    modal.show();
}

function deleteDocument(documentId) {
    if (confirm('هل أنت متأكد من حذف هذا المستند؟')) {
        fetch(`/documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        }).then(response => {
            if (response.ok) {
                window.location.href = '{{ route("documents.index") }}';
            } else {
                alert('حدث خطأ أثناء حذف المستند');
            }
        });
    }
}

function createBackup(type) {
    const storageType = type === 'drive' ? 'Google Drive' : 'التخزين المحلي';
    const button = event.target;
    const originalText = button.innerHTML;
    
    if (confirm(`هل تريد إنشاء نسخة احتياطية في ${storageType}؟`)) {
        // إظهار مؤشر التحميل
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإنشاء...';
        button.disabled = true;
        
        const formData = new FormData();
        formData.append('backup_type', 'manual');
        formData.append('use_drive', type === 'drive' ? '1' : '0');
        formData.append('backup_notes', `نسخة احتياطية يدوية في ${storageType}`);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        fetch(`/documents/{{ $document->id }}/backup`, {
            method: 'POST',
            body: formData,
        }).then(response => {
            if (response.ok) {
                // إظهار رسالة نجاح
                showNotification(`تم إنشاء النسخة الاحتياطية بنجاح في ${storageType}`, 'success');
                // إعادة تحميل الصفحة بعد ثانيتين
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                // إعادة النص الأصلي
                button.innerHTML = originalText;
                button.disabled = false;
                
                response.json().then(data => {
                    showNotification(data.message || `حدث خطأ أثناء إنشاء النسخة الاحتياطية في ${storageType}`, 'error');
                }).catch(() => {
                    showNotification(`حدث خطأ أثناء إنشاء النسخة الاحتياطية في ${storageType}`, 'error');
                });
            }
        }).catch(error => {
            console.error('Error:', error);
            // إعادة النص الأصلي
            button.innerHTML = originalText;
            button.disabled = false;
            showNotification(`حدث خطأ في الاتصال`, 'error');
        });
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