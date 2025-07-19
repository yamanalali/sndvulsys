@extends('layouts.master')

@section('content')
<div class="upload-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-text">
                <h1 class="page-title">
                    <i class="fas fa-cloud-upload-alt"></i>
                    رفع مستند جديد
                </h1>
                <p class="page-subtitle">أضف مستنداتك وملفاتك إلى النظام بأمان</p>
            </div>
            <div class="header-action">
                <a href="{{ route('documents.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-right"></i>
                    العودة للمستندات
                </a>
            </div>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="upload-form-container">
        <div class="upload-card">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <div class="alert-content">
                        <i class="fas fa-check-circle"></i>
                        <div class="alert-text">
                            <h4>تم الرفع بنجاح!</h4>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <div class="alert-content">
                        <i class="fas fa-exclamation-circle"></i>
                        <div class="alert-text">
                            <h4>حدث خطأ!</h4>
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <div class="alert-content">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div class="alert-text">
                            <h4>يرجى تصحيح الأخطاء التالية:</h4>
                            <ul class="error-list">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Upload Form -->
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                
                <!-- File Upload Area -->
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h3>اسحب وأفلت الملف هنا</h3>
                    <p>أو انقر لاختيار ملف</p>
                    <div class="supported-files">
                        <span class="file-type">PDF</span>
                        <span class="file-type">DOCX</span>
                        <span class="file-type">ZIP</span>
                    </div>
                    <input type="file" id="fileInput" name="file" accept=".pdf,.docx,.zip" required hidden>
                </div>

                <!-- File Preview -->
                <div class="file-preview" id="filePreview" style="display: none;">
                    <div class="preview-header">
                        <h4>معاينة الملف</h4>
                        <button type="button" class="remove-file" onclick="removeFile()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="preview-content">
                        <div class="file-info">
                            <div class="file-icon">
                                <i id="fileIcon" class="fas fa-file"></i>
                            </div>
                            <div class="file-details">
                                <h5 id="fileName">اسم الملف</h5>
                                <p id="fileSize">حجم الملف</p>
                                <p id="fileType">نوع الملف</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Details -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        تفاصيل المستند
                    </h3>
                    
                    <div class="form-group">
                        <label for="title" class="form-label">
                            <i class="fas fa-tag"></i>
                            اسم المستند
                        </label>
                        <input type="text" 
                               class="form-input @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               placeholder="أدخل اسم المستند أو اتركه فارغاً ليتم استخدام اسم الملف"
                               value="{{ old('title') }}">
                        @error('title')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-shield-alt"></i>
                            معلومات الأمان
                        </label>
                        <div class="security-info">
                            <div class="security-item">
                                <i class="fas fa-lock"></i>
                                <span>ملفاتك محمية ومشفرة</span>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-eye-slash"></i>
                                <span>يمكنك فقط الوصول لملفاتك</span>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-clock"></i>
                                <span>متاح 24/7</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Progress -->
                <div class="upload-progress" id="uploadProgress" style="display: none;">
                    <div class="progress-header">
                        <h4>جاري رفع الملف...</h4>
                        <span class="progress-percentage" id="progressPercentage">0%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-large" id="submitBtn">
                        <i class="fas fa-upload"></i>
                        رفع المستند
                    </button>
                    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary btn-large">
                        <i class="fas fa-times"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>

        <!-- Upload Guidelines -->
        <div class="guidelines-card">
            <h3 class="guidelines-title">
                <i class="fas fa-lightbulb"></i>
                إرشادات الرفع
            </h3>
            <div class="guidelines-content">
                <div class="guideline-item">
                    <div class="guideline-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="guideline-text">
                        <h4>الملفات المدعومة</h4>
                        <p>PDF, DOCX, ZIP - الحد الأقصى 2 ميجابايت</p>
                    </div>
                </div>
                <div class="guideline-item">
                    <div class="guideline-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="guideline-text">
                        <h4>تنظيم الملفات</h4>
                        <p>استخدم أسماء واضحة ووصفية للملفات</p>
                    </div>
                </div>
                <div class="guideline-item">
                    <div class="guideline-icon">
                        <i class="fas fa-sync"></i>
                    </div>
                    <div class="guideline-text">
                        <h4>النسخ الاحتياطية</h4>
                        <p>احتفظ بنسخة احتياطية من ملفاتك المهمة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Upload Container */
.upload-container {
    padding: 70px;
    background: #f8f9fa;
    min-height: calc(100vh - 80px);
}

/* Page Header */
.page-header {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-text {
    flex: 1;
}

.page-title {
    font-size: 1.0rem;
    font-weight: 400;
    color: #2d3748;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-title i {
    color: #4e73df;
}

.page-subtitle {
    color: #718096;
    margin: 0;
    font-size: 0.95rem;
}

.header-action {
    flex-shrink: 0;
}

/* Upload Form Container */
.upload-form-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
}

.upload-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Alerts */
.alert {
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
    border: 1px solid transparent;
}

.alert-success {
    background: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-danger {
    background: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.alert-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.alert-content i {
    font-size: 1.2rem;
    margin-top: 2px;
}

.alert-text h4 {
    margin: 0 0 5px 0;
    font-weight: 600;
}

.alert-text p {
    margin: 0;
    font-size: 0.9rem;
}

.error-list {
    margin: 10px 0 0 0;
    padding-right: 20px;
    font-size: 0.9rem;
}

/* Upload Area */
.upload-area {
    border: 2px dashed #cbd5e0;
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 25px;
}

.upload-area:hover {
    border-color: #4e73df;
    background: #f7fafc;
}

.upload-area.dragover {
    border-color: #4e73df;
    background: #ebf8ff;
}

.upload-icon {
    font-size: 3rem;
    color: #4e73df;
    margin-bottom: 15px;
}

.upload-area h3 {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 8px 0;
}

.upload-area p {
    color: #718096;
    margin: 0 0 20px 0;
    font-size: 0.95rem;
}

.supported-files {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.file-type {
    background: #e2e8f0;
    color: #4a5568;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* File Preview */
.file-preview {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
}

.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.preview-header h4 {
    margin: 0;
    color: #2d3748;
    font-weight: 600;
}

.remove-file {
    background: none;
    border: none;
    color: #e53e3e;
    font-size: 1.1rem;
    cursor: pointer;
    padding: 5px;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.remove-file:hover {
    background: #fed7d7;
}

.file-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.file-icon {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #4e73df;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.file-details h5 {
    margin: 0 0 5px 0;
    color: #2d3748;
    font-weight: 600;
}

.file-details p {
    margin: 0 0 3px 0;
    color: #718096;
    font-size: 0.85rem;
}

/* Form Section */
.form-section {
    margin-bottom: 25px;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title i {
    color: #4e73df;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-label i {
    color: #4e73df;
}

.form-input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #4e73df;
    box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
}

.form-input.is-invalid {
    border-color: #e53e3e;
}

.error-message {
    color: #e53e3e;
    font-size: 0.85rem;
    margin-top: 5px;
}

/* Security Info */
.security-info {
    background: #f0fff4;
    border: 1px solid #c6f6d5;
    border-radius: 8px;
    padding: 15px;
}

.security-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
    color: #2f855a;
    font-size: 0.9rem;
}

.security-item:last-child {
    margin-bottom: 0;
}

.security-item i {
    width: 16px;
}

/* Upload Progress */
.upload-progress {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.progress-header h4 {
    margin: 0;
    color: #2d3748;
    font-weight: 600;
}

.progress-percentage {
    color: #4e73df;
    font-weight: 600;
    font-size: 0.9rem;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #4e73df;
    width: 0%;
    transition: width 0.3s ease;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-start;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 500;
    text-decoration: none;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-large {
    padding: 15px 30px;
    font-size: 1rem;
}

.btn-primary {
    background: #4e73df;
    border-color: #4e73df;
    color: white;
}

.btn-primary:hover {
    background: #2e59d9;
    border-color: #2e59d9;
    color: white;
    text-decoration: none;
}

.btn-outline-primary {
    color: #4e73df;
    border-color: #4e73df;
    background: transparent;
}

.btn-outline-primary:hover {
    background: #4e73df;
    border-color: #4e73df;
    color: white;
    text-decoration: none;
}

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
    background: transparent;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    border-color: #6c757d;
    color: white;
    text-decoration: none;
}

/* Guidelines Card */
.guidelines-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    height: fit-content;
}

.guidelines-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.guidelines-title i {
    color: #f6ad55;
}

.guidelines-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.guideline-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.guideline-icon {
    width: 40px;
    height: 40px;
    background: #fef5e7;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f6ad55;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.guideline-text h4 {
    margin: 0 0 5px 0;
    color: #2d3748;
    font-weight: 600;
    font-size: 0.95rem;
}

.guideline-text p {
    margin: 0;
    color: #718096;
    font-size: 0.85rem;
    line-height: 1.5;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .upload-form-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .upload-container {
        padding: 20px;
    }
    
    .header-content {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .upload-area {
        padding: 30px 15px;
    }
    
    .upload-icon {
        font-size: 2.5rem;
    }
    
    .upload-area h3 {
        font-size: 1.1rem;
    }
}

@media (max-width: 576px) {
    .upload-container {
        padding: 15px;
    }
    
    .page-header {
        padding: 20px;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .upload-card {
        padding: 20px;
    }
    
    .guidelines-card {
        padding: 20px;
    }
}

/* Message Toast */
.message-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    min-width: 300px;
    max-width: 400px;
    animation: slideInRight 0.3s ease;
}

.message-content {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    color: white;
    font-weight: 500;
}

.message-success {
    background: #10b981;
}

.message-error {
    background: #ef4444;
}

.message-info {
    background: #3b82f6;
}

.message-close {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 2px;
    border-radius: 4px;
    margin-left: auto;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.message-close:hover {
    opacity: 1;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const uploadForm = document.getElementById('uploadForm');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressPercentage = document.getElementById('progressPercentage');
    const submitBtn = document.getElementById('submitBtn');

    // Click to upload
    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFile(e.target.files[0]);
        }
    });

    // Handle file selection
    function handleFile(file) {
        // Validate file type
        const allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'];
        const allowedExtensions = ['.pdf', '.docx', '.zip'];
        
        // Check both MIME type and file extension
        const isValidType = allowedTypes.includes(file.type) || 
                           allowedExtensions.some(ext => file.name.toLowerCase().endsWith(ext));
        
        if (!isValidType) {
            showMessage('نوع الملف غير مدعوم. يرجى اختيار ملف PDF أو DOCX أو ZIP', 'error');
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            showMessage('حجم الملف كبير جداً. الحد الأقصى 2 ميجابايت', 'error');
            return;
        }

        // Update file input
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;

        // Show preview
        showFilePreview(file);
    }

    // Show file preview
    function showFilePreview(file) {
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const fileType = document.getElementById('fileType');
        const fileIcon = document.getElementById('fileIcon');

        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileType.textContent = getFileTypeName(file.type);

        // Set icon based on file type
        if (file.type === 'application/pdf') {
            fileIcon.className = 'fas fa-file-pdf';
            fileIcon.style.color = '#dc3545';
        } else if (file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            fileIcon.className = 'fas fa-file-word';
            fileIcon.style.color = '#0d6efd';
        } else if (file.type === 'application/zip') {
            fileIcon.className = 'fas fa-file-archive';
            fileIcon.style.color = '#fd7e14';
        }

        uploadArea.style.display = 'none';
        filePreview.style.display = 'block';
    }

    // Remove file
    window.removeFile = function() {
        fileInput.value = '';
        filePreview.style.display = 'none';
        uploadArea.style.display = 'block';
    }

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Get file type name
    function getFileTypeName(mimeType) {
        const types = {
            'application/pdf': 'PDF Document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'Word Document',
            'application/zip': 'ZIP Archive'
        };
        return types[mimeType] || 'Unknown Type';
    }

    // Show message function
    function showMessage(message, type = 'info') {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.message-toast');
        existingMessages.forEach(msg => msg.remove());
        
        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-toast message-${type}`;
        messageDiv.innerHTML = `
            <div class="message-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="message-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        // Add to page
        document.body.appendChild(messageDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentElement) {
                messageDiv.remove();
            }
        }, 5000);
    }

    // Form submission with progress
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check if file is selected
        if (!fileInput.files.length) {
            showMessage('يرجى اختيار ملف أولاً', 'error');
            return;
        }
        
        // Show progress
        uploadProgress.style.display = 'block';
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الرفع...';

        // Simulate progress
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            
            progressFill.style.width = progress + '%';
            progressPercentage.textContent = Math.round(progress) + '%';
        }, 200);

        // Submit form using traditional form submission for better file handling
        const formData = new FormData(uploadForm);
        
        // Create XMLHttpRequest for better progress tracking
        const xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressFill.style.width = percentComplete + '%';
                progressPercentage.textContent = Math.round(percentComplete) + '%';
            }
        });
        
        xhr.addEventListener('load', function() {
            clearInterval(progressInterval);
            progressFill.style.width = '100%';
            progressPercentage.textContent = '100%';
            
            console.log('Response status:', xhr.status);
            console.log('Response text:', xhr.responseText);
            
            setTimeout(() => {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        console.log('Parsed response:', response);
                        if (response.success) {
                            // Show success message
                            showMessage('تم رفع الملف بنجاح', 'success');
                            setTimeout(() => {
                                window.location.href = '{{ route("documents.index") }}';
                            }, 1500);
                        } else {
                            showMessage(response.message || 'حدث خطأ أثناء رفع الملف', 'error');
                            uploadProgress.style.display = 'none';
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-upload"></i> رفع المستند';
                        }
                    } catch (e) {
                        console.log('JSON parse error:', e);
                        // If response is not JSON, it might be a redirect
                        showMessage('تم رفع الملف بنجاح', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                } else if (xhr.status === 422) {
                    // Validation errors
                    try {
                        const response = JSON.parse(xhr.responseText);
                        let errorMessage = 'بيانات غير صحيحة:\n';
                        if (response.errors) {
                            Object.values(response.errors).forEach(errors => {
                                errors.forEach(error => {
                                    errorMessage += '- ' + error + '\n';
                                });
                            });
                        }
                        showMessage(errorMessage, 'error');
                    } catch (e) {
                        showMessage('بيانات غير صحيحة', 'error');
                    }
                    uploadProgress.style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-upload"></i> رفع المستند';
                } else {
                    showMessage('حدث خطأ أثناء رفع الملف (Status: ' + xhr.status + ')', 'error');
                    uploadProgress.style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-upload"></i> رفع المستند';
                }
            }, 500);
        });
        
        xhr.addEventListener('error', function() {
            clearInterval(progressInterval);
            console.error('Upload error:', xhr.status, xhr.statusText);
            showMessage('حدث خطأ في الاتصال بالخادم', 'error');
            uploadProgress.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-upload"></i> رفع المستند';
        });
        
        xhr.addEventListener('timeout', function() {
            clearInterval(progressInterval);
            console.error('Upload timeout');
            showMessage('انتهت مهلة الاتصال، يرجى المحاولة مرة أخرى', 'error');
            uploadProgress.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-upload"></i> رفع المستند';
        });
        
        xhr.open('POST', uploadForm.action);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.timeout = 30000; // 30 seconds timeout
        xhr.send(formData);
    });
});
</script>
@endsection 