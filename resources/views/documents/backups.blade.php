@extends('layouts.app')

@section('title', 'النسخ الاحتياطية - ' . $document->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Debug Info -->
            @if(config('app.debug'))
            <div class="alert alert-info">
                <strong>معلومات التصحيح:</strong><br>
                إجمالي النسخ: {{ $allBackups->count() }}<br>
                النسخ في Google Drive: {{ $driveBackups->count() }}<br>
                النسخ المحلية: {{ $localBackups->count() }}<br>
                المستند ID: {{ $document->id }}
            </div>
            @endif
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-copy text-primary me-2"></i>
                        <h4 class="mb-0">النسخ الاحتياطية - {{ $document->title }}</h4>
                    </div>
                    <div>
                        <button class="btn btn-success me-2" onclick="createBackup('drive')">
                            <i class="fas fa-cloud-upload-alt"></i>
                            نسخة في Google Drive
                        </button>
                        <button class="btn btn-primary" onclick="createBackup('local')">
                            <i class="fas fa-save"></i>
                            نسخة محلية
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- إحصائيات النسخ الاحتياطية -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $allBackups->count() }}</h5>
                                    <small>إجمالي النسخ</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $driveBackups->count() }}</h5>
                                    <small>في Google Drive</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $localBackups->count() }}</h5>
                                    <small>محلية</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $allBackups->where('backup_type', 'automatic')->count() }}</h5>
                                    <small>تلقائية</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- رسالة إذا لم توجد نسخ احتياطية -->
                    @if($allBackups->count() == 0)
                    <div class="text-center py-5">
                        <i class="fas fa-copy fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد نسخ احتياطية</h5>
                        <p class="text-muted">قم بإنشاء نسخة احتياطية أولاً</p>
                        <div class="mt-3">
                            <button class="btn btn-success me-2" onclick="createBackup('drive')">
                                <i class="fas fa-cloud-upload-alt"></i>
                                نسخة في Google Drive
                            </button>
                            <button class="btn btn-primary" onclick="createBackup('local')">
                                <i class="fas fa-save"></i>
                                نسخة محلية
                            </button>
                        </div>
                    </div>
                    @else
                    <!-- معلومات التصحيح -->
                    @if(config('app.debug'))
                    <div class="alert alert-info">
                        <strong>معلومات التصحيح:</strong><br>
                        إجمالي النسخ: {{ $allBackups->count() }}<br>
                        النسخ في Google Drive: {{ $driveBackups->count() }}<br>
                        النسخ المحلية: {{ $localBackups->count() }}<br>
                        المستند ID: {{ $document->id }}
                    </div>
                    @endif
                    <!-- قائمة النسخ الاحتياطية -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>نوع النسخة</th>
                                    <th>التخزين</th>
                                    <th>الحجم</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backups as $backup)
                                <!-- Debug: Backup ID {{ $backup->id }} -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-{{ $backup->backup_type === 'automatic' ? 'robot' : 'hand' }} text-primary me-2"></i>
                                            <div>
                                                <strong>{{ ucfirst($backup->backup_type) }}</strong>
                                                @if($backup->backup_notes)
                                                <br>
                                                <small class="text-muted">{{ $backup->backup_notes }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($backup->drive_file_id)
                                            <span class="badge bg-success">
                                                <i class="fas fa-cloud"></i>
                                                Google Drive
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="fas fa-hdd"></i>
                                                محلي
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $backup->formatted_size }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $backup->backup_date->format('Y-m-d') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $backup->backup_date->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($backup->isValid())
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i>
                                                صالح
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times"></i>
                                                تالف
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($backup->isValid())
                                            <button class="btn btn-sm btn-outline-primary" onclick="restoreBackup({{ $backup->id }})" title="استعادة">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            @endif
                                            <a href="{{ $backup->backup_url }}" class="btn btn-sm btn-outline-success" target="_blank" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($backup->drive_file_id)
                                            <a href="{{ $backup->drive_web_link }}" class="btn btn-sm btn-outline-info" target="_blank" title="فتح في Google Drive">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            @endif
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteBackup({{ $backup->id }})" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-copy fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">لا توجد نسخ احتياطية</h5>
                                        <p class="text-muted">قم بإنشاء نسخة احتياطية أولاً</p>
                                        @if(config('app.debug'))
                                        <small class="text-muted">Debug: allBackups count = {{ $allBackups->count() }}, backups count = {{ $backups->count() }}</small>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- ترقيم الصفحات -->
                    @if($backups->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $backups->links() }}
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إنشاء نسخة احتياطية -->
<div class="modal fade" id="createBackupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إنشاء نسخة احتياطية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createBackupForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نوع النسخة</label>
                        <select name="backup_type" class="form-control" required>
                            <option value="manual">يدوية</option>
                            <option value="automatic">تلقائية</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع التخزين</label>
                        <select name="use_drive" class="form-control" required>
                            <option value="1">Google Drive</option>
                            <option value="0">التخزين المحلي</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات (اختياري)</label>
                        <textarea name="backup_notes" class="form-control" rows="3" placeholder="أضف ملاحظات للنسخة الاحتياطية..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إنشاء النسخة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal تأكيد الاستعادة -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الاستعادة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من استعادة هذا المستند من النسخة الاحتياطية؟</p>
                <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> سيتم استبدال المستند الحالي بالنسخة الاحتياطية</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-warning" id="confirmRestore">تأكيد الاستعادة</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.btn-group .btn {
    margin-right: 2px;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

.backup-stats {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
}
</style>
@endpush

@push('scripts')
<script>
let currentBackupId = null;

function createBackup(storageType) {
    const modal = new bootstrap.Modal(document.getElementById('createBackupModal'));
    const form = document.getElementById('createBackupForm');
    const useDriveSelect = form.querySelector('select[name="use_drive"]');
    
    // تعيين نوع التخزين
    if (storageType === 'drive') {
        useDriveSelect.value = '1';
    } else if (storageType === 'local') {
        useDriveSelect.value = '0';
    }
    
    form.action = '{{ route("documents.backup", $document) }}';
    modal.show();
}

function restoreBackup(backupId) {
    currentBackupId = backupId;
    const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
    modal.show();
}

function deleteBackup(backupId) {
    if (confirm('هل أنت متأكد من حذف هذه النسخة الاحتياطية؟')) {
        fetch(`/document-backups/${backupId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('حدث خطأ أثناء حذف النسخة الاحتياطية');
            }
        });
    }
}

// تأكيد الاستعادة
document.getElementById('confirmRestore').addEventListener('click', function() {
    if (currentBackupId) {
        fetch(`/document-backups/${currentBackupId}/restore`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        }).then(response => {
            if (response.ok) {
                alert('تم استعادة المستند بنجاح');
                window.location.href = '{{ route("documents.show", $document) }}';
            } else {
                alert('حدث خطأ أثناء استعادة المستند');
            }
        });
    }
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('restoreModal'));
    modal.hide();
});

// تحديث الإحصائيات
function updateStats() {
    // يمكن إضافة تحديث ديناميكي للإحصائيات هنا
}
</script>
@endpush 