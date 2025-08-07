@extends('layouts.app')

@section('title', 'رفع مستند جديد')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-upload text-primary"></i>
                        رفع مستند جديد
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- معلومات المستند -->
                                <div class="mb-4">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-info-circle"></i>
                                        معلومات المستند
                                    </h5>
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">عنوان المستند <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title') }}" required>
                                        @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">وصف المستند</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                        @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- رفع الملف -->
                                <div class="mb-4">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-file-upload"></i>
                                        رفع الملف
                                    </h5>
                                    
                                    <div class="upload-area" id="uploadArea">
                                        <div class="upload-content text-center">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <h5>اسحب الملف هنا أو اضغط للاختيار</h5>
                                            <p class="text-muted">الحد الأقصى: 10 ميجابايت</p>
                                            <input type="file" id="document" name="document" class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.txt" required>
                                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('document').click()">
                                                <i class="fas fa-folder-open"></i>
                                                اختيار ملف
                                            </button>
                                        </div>
                                        <div class="upload-preview d-none" id="uploadPreview">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-alt fa-2x text-primary me-3"></i>
                                                <div class="flex-grow-1">
                                                    <h6 id="fileName">اسم الملف</h6>
                                                    <small class="text-muted" id="fileSize">الحجم</small>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @error('document')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- إعدادات الخصوصية -->
                                <div class="mb-4">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-shield-alt"></i>
                                        إعدادات الخصوصية
                                    </h5>
                                    
                                    <div class="mb-3">
                                        <label for="privacy_level" class="form-label">مستوى الخصوصية <span class="text-danger">*</span></label>
                                        <select class="form-control @error('privacy_level') is-invalid @enderror" 
                                                id="privacy_level" name="privacy_level" required>
                                            <option value="">اختر مستوى الخصوصية...</option>
                                            <option value="public" {{ old('privacy_level') == 'public' ? 'selected' : '' }}>
                                                <i class="fas fa-globe"></i> عام - يمكن للجميع الوصول
                                            </option>
                                            <option value="private" {{ old('privacy_level') == 'private' ? 'selected' : '' }}>
                                                <i class="fas fa-lock"></i> خاص - أنت فقط
                                            </option>
                                            <option value="restricted" {{ old('privacy_level') == 'restricted' ? 'selected' : '' }}>
                                                <i class="fas fa-users"></i> مقيد - للمستخدمين المصرح لهم
                                            </option>
                                        </select>
                                        @error('privacy_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="expires_at" class="form-label">تاريخ انتهاء الصلاحية</label>
                                        <input type="date" class="form-control @error('expires_at') is-invalid @enderror" 
                                               id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                        <small class="text-muted">اتركه فارغاً إذا كان المستند دائم</small>
                                        @error('expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- معلومات إضافية -->
                                <div class="mb-4">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-info-circle"></i>
                                        معلومات إضافية
                                    </h5>
                                    
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6>الملفات المدعومة:</h6>
                                            <ul class="list-unstyled small">
                                                <li><i class="fas fa-file-pdf text-danger"></i> PDF</li>
                                                <li><i class="fas fa-file-word text-primary"></i> Word</li>
                                                <li><i class="fas fa-file-excel text-success"></i> Excel</li>
                                                <li><i class="fas fa-file-powerpoint text-warning"></i> PowerPoint</li>
                                                <li><i class="fas fa-file-image text-info"></i> صور</li>
                                                <li><i class="fas fa-file-alt text-secondary"></i> نص</li>
                                            </ul>
                                            
                                            <hr>
                                            
                                            <h6>الحد الأقصى:</h6>
                                            <p class="mb-0"><strong>10 ميجابايت</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار التحكم -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('documents.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                رجوع
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-upload"></i>
                                رفع المستند
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal معاينة الملف -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">معاينة الملف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.upload-area:hover {
    border-color: #007bff;
    background: #f0f8ff;
}

.upload-area.dragover {
    border-color: #007bff;
    background: #e3f2fd;
}

.upload-preview {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
}

.privacy-option {
    padding: 0.5rem;
    border-radius: 4px;
    margin-bottom: 0.5rem;
}

.privacy-option:hover {
    background: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script>
// رفع الملف
document.getElementById('document').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        showFilePreview(file);
    }
});

// سحب وإفلات الملفات
const uploadArea = document.getElementById('uploadArea');

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('document').files = files;
        showFilePreview(files[0]);
    }
});

function showFilePreview(file) {
    const uploadContent = document.querySelector('.upload-content');
    const uploadPreview = document.getElementById('uploadPreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    
    // التحقق من حجم الملف
    const maxSize = 10 * 1024 * 1024; // 10MB
    if (file.size > maxSize) {
        alert('حجم الملف أكبر من 10 ميجابايت');
        return;
    }
    
    // التحقق من نوع الملف
    const allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'txt'];
    const fileExtension = file.name.split('.').pop().toLowerCase();
    
    if (!allowedTypes.includes(fileExtension)) {
        alert('نوع الملف غير مدعوم');
        return;
    }
    
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    
    uploadContent.classList.add('d-none');
    uploadPreview.classList.remove('d-none');
}

function removeFile() {
    document.getElementById('document').value = '';
    document.querySelector('.upload-content').classList.remove('d-none');
    document.getElementById('uploadPreview').classList.add('d-none');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// التحقق من النموذج قبل الإرسال
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const file = document.getElementById('document').files[0];
    if (!file) {
        e.preventDefault();
        alert('يرجى اختيار ملف');
        return;
    }
    
    // إظهار مؤشر التحميل
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الرفع...';
    submitBtn.disabled = true;
});
</script>
@endpush 