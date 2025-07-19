@extends('layouts.master')

@section('title', 'تعديل المهارة')

@section('content')
<div class="skills-create-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-text">
                <h1 class="page-title">
                    <i class="fas fa-edit"></i>
                    تعديل المهارة
                </h1>
                <p class="page-subtitle">تحديث معلومات المهارة وتحسينها</p>
            </div>
            <div class="header-action">
                <a href="{{ route('skills.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>
                    رجوع للمهارات
                </a>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="form-section">
            <!-- Error Messages -->
            @if($errors->any())
            <div class="alert alert-danger">
                <div class="alert-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>يرجى تصحيح الأخطاء التالية:</strong>
                </div>
                <ul class="error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Skill Form -->
            <div class="form-card">
                <div class="form-header">
                    <h3><i class="fas fa-edit"></i> تعديل تفاصيل المهارة</h3>
                    <p>قم بتحديث المعلومات التالية للمهارة</p>
                </div>

                <form method="POST" action="{{ route('skills.update', $skill->id) }}" id="skillForm" class="skill-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-grid">
                        <!-- Skill Name -->
                        <div class="form-group full-width">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag"></i>
                                اسم المهارة
                                <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-input" 
                                   value="{{ old('name', $skill->name) }}" 
                                   placeholder="مثال: البرمجة، التصميم، التدريس، الترجمة..."
                                   required>
                            <div class="input-help">
                                <i class="fas fa-info-circle"></i>
                                أدخل اسم المهارة باللغة العربية أو الإنجليزية
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="form-group">
                            <label for="category" class="form-label">
                                <i class="fas fa-folder"></i>
                                الفئة
                                <span class="required">*</span>
                            </label>
                            <select name="category" id="category" class="form-select" required>
                                <option value="">اختر الفئة</option>
                                <option value="تقنية" {{ old('category', $skill->category) == 'تقنية' ? 'selected' : '' }}>تقنية</option>
                                <option value="تعليمية" {{ old('category', $skill->category) == 'تعليمية' ? 'selected' : '' }}>تعليمية</option>
                                <option value="طبية" {{ old('category', $skill->category) == 'طبية' ? 'selected' : '' }}>طبية</option>
                                <option value="اجتماعية" {{ old('category', $skill->category) == 'اجتماعية' ? 'selected' : '' }}>اجتماعية</option>
                                <option value="إبداعية" {{ old('category', $skill->category) == 'إبداعية' ? 'selected' : '' }}>إبداعية</option>
                                <option value="أخرى" {{ old('category', $skill->category) == 'أخرى' ? 'selected' : '' }}>أخرى</option>
                            </select>
                        </div>

                        <!-- Skill Level -->
                        <div class="form-group">
                            <label for="skill_level" class="form-label">
                                <i class="fas fa-star"></i>
                                مستوى المهارة
                            </label>
                            <select name="skill_level" id="skill_level" class="form-select">
                                <option value="مبتدئ" {{ old('skill_level', $skill->skill_level ?? 'متوسط') == 'مبتدئ' ? 'selected' : '' }}>مبتدئ</option>
                                <option value="متوسط" {{ old('skill_level', $skill->skill_level ?? 'متوسط') == 'متوسط' ? 'selected' : '' }} selected>متوسط</option>
                                <option value="متقدم" {{ old('skill_level', $skill->skill_level ?? 'متوسط') == 'متقدم' ? 'selected' : '' }}>متقدم</option>
                                <option value="خبير" {{ old('skill_level', $skill->skill_level ?? 'متوسط') == 'خبير' ? 'selected' : '' }}>خبير</option>
                            </select>
                        </div>

                        <!-- Years of Experience -->
                        <div class="form-group">
                            <label for="experience_years" class="form-label">
                                <i class="fas fa-clock"></i>
                                سنوات الخبرة
                            </label>
                            <select name="experience_years" id="experience_years" class="form-select">
                                <option value="أقل من سنة" {{ old('experience_years', $skill->experience_years ?? '3-5 سنوات') == 'أقل من سنة' ? 'selected' : '' }}>أقل من سنة</option>
                                <option value="1-2 سنة" {{ old('experience_years', $skill->experience_years ?? '3-5 سنوات') == '1-2 سنة' ? 'selected' : '' }}>1-2 سنة</option>
                                <option value="3-5 سنوات" {{ old('experience_years', $skill->experience_years ?? '3-5 سنوات') == '3-5 سنوات' ? 'selected' : '' }} selected>3-5 سنوات</option>
                                <option value="6-10 سنوات" {{ old('experience_years', $skill->experience_years ?? '3-5 سنوات') == '6-10 سنوات' ? 'selected' : '' }}>6-10 سنوات</option>
                                <option value="أكثر من 10 سنوات" {{ old('experience_years', $skill->experience_years ?? '3-5 سنوات') == 'أكثر من 10 سنوات' ? 'selected' : '' }}>أكثر من 10 سنوات</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="form-group full-width">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left"></i>
                                وصف المهارة
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      class="form-textarea" 
                                      rows="4" 
                                      placeholder="وصف مختصر للمهارة ومجالات استخدامها والخبرات المكتسبة...">{{ old('description', $skill->description) }}</textarea>
                            <div class="input-help">
                                <i class="fas fa-info-circle"></i>
                                وصف اختياري للمهارة (أقصى 300 حرف)
                                <span class="char-count" id="charCount">0/300</span>
                            </div>
                        </div>

                        <!-- Certificates -->
                        <div class="form-group full-width">
                            <label for="certificates" class="form-label">
                                <i class="fas fa-certificate"></i>
                                الشهادات والدورات (اختياري)
                            </label>
                            <textarea name="certificates" 
                                      id="certificates" 
                                      class="form-textarea" 
                                      rows="3" 
                                      placeholder="اذكر الشهادات أو الدورات التدريبية المتعلقة بهذه المهارة...">{{ old('certificates', $skill->certificates ?? '') }}</textarea>
                        </div>

                        <!-- Additional Settings -->
                        <div class="form-group full-width">
                            <div class="settings-section">
                                <h4><i class="fas fa-cog"></i> إعدادات إضافية</h4>
                                <div class="settings-grid">
                                    <div class="setting-item">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="is_public" value="1" {{ old('is_public', $skill->is_public ?? true) ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                            <span class="setting-text">
                                                <strong>مهارة عامة</strong>
                                                <small>يمكن للآخرين رؤية هذه المهارة</small>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="setting-item">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="available_for_volunteering" value="1" {{ old('available_for_volunteering', $skill->available_for_volunteering ?? true) ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                            <span class="setting-text">
                                                <strong>متاح للتطوع</strong>
                                                <small>يمكن استخدام هذه المهارة في الأعمال التطوعية</small>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <div class="action-buttons">
                            <button type="button" class="btn btn-outline-primary" onclick="previewSkill()">
                                <i class="fas fa-eye"></i>
                                معاينة
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                حفظ التعديلات
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="preview-section" id="previewSection" style="display: none;">
            <div class="preview-card">
                <div class="preview-header">
                    <h3><i class="fas fa-eye"></i> معاينة المهارة</h3>
                    <button type="button" class="btn-close" onclick="hidePreview()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="preview-content" id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Skill Preview Modal -->
<div class="modal fade" id="skillPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye"></i>
                    معاينة المهارة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalPreviewContent">
                <!-- Modal preview content -->
            </div>
        </div>
    </div>
</div>

<style>
/* Skills Create Container */
.skills-create-container {
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
    font-size: 2rem;
    font-weight: 700;
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
    font-size: 1rem;
}

.header-action {
    flex-shrink: 0;
}

/* Content Wrapper */
.content-wrapper {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 25px;
}

/* Form Section */
.form-section {
    min-width: 0;
}

/* Alert Styles */
.alert {
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
    border: none;
}

.alert-danger {
    background: #fed7d7;
    color: #c53030;
}

.alert-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    font-weight: 600;
}

.error-list {
    margin: 0;
    padding-right: 20px;
}

.error-list li {
    margin-bottom: 5px;
}

/* Form Card */
.form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.form-header {
    background: linear-gradient(135deg, #4e73df, #224abe);
    color: white;
    padding: 25px;
    text-align: center;
}

.form-header h3 {
    margin: 0 0 8px 0;
    font-size: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.form-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.95rem;
}

/* Form Styles */
.skill-form {
    padding: 30px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
}

.form-label i {
    color: #4e73df;
    width: 16px;
}

.required {
    color: #e53e3e;
    font-weight: 700;
}

.form-input,
.form-select,
.form-textarea {
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #4e73df;
    box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.input-help {
    margin-top: 6px;
    font-size: 0.85rem;
    color: #718096;
    display: flex;
    align-items: center;
    gap: 6px;
}

.char-count {
    margin-right: auto;
    font-weight: 500;
}

/* Settings Section */
.settings-section {
    background: #f7fafc;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e2e8f0;
}

.settings-section h4 {
    margin: 0 0 15px 0;
    font-size: 1.1rem;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 8px;
}

.settings-grid {
    display: grid;
    gap: 15px;
}

.setting-item {
    display: flex;
    align-items: flex-start;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    cursor: pointer;
    width: 100%;
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid #cbd5e0;
    border-radius: 4px;
    position: relative;
    flex-shrink: 0;
    margin-top: 2px;
    transition: all 0.3s ease;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark {
    background: #4e73df;
    border-color: #4e73df;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.setting-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.setting-text strong {
    color: #2d3748;
    font-size: 0.95rem;
}

.setting-text small {
    color: #718096;
    font-size: 0.85rem;
}

/* Form Actions */
.form-actions {
    margin-top: 30px;
    padding-top: 25px;
    border-top: 1px solid #e2e8f0;
}

.action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
}

/* Buttons */
.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    cursor: pointer;
}

.btn-primary {
    background: #4e73df;
    border-color: #4e73df;
    color: white;
}

.btn-primary:hover {
    background: #224abe;
    border-color: #224abe;
    color: white;
    text-decoration: none;
}

.btn-outline-primary {
    background: transparent;
    border-color: #4e73df;
    color: #4e73df;
}

.btn-outline-primary:hover {
    background: #4e73df;
    border-color: #4e73df;
    color: white;
}

.btn-outline-secondary {
    background: transparent;
    border-color: #718096;
    color: #718096;
}

.btn-outline-secondary:hover {
    background: #718096;
    border-color: #718096;
    color: white;
}

/* Preview Section */
.preview-section {
    position: sticky;
    top: 20px;
    height: fit-content;
}

.preview-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.preview-header {
    background: #f7fafc;
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.preview-header h3 {
    margin: 0;
    font-size: 1.2rem;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-close {
    background: none;
    border: none;
    color: #718096;
    font-size: 1.1rem;
    cursor: pointer;
    padding: 5px;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.btn-close:hover {
    background: #e2e8f0;
    color: #2d3748;
}

.preview-content {
    padding: 20px;
}

/* Modal */
.modal-content {
    border-radius: 12px;
    border: none;
}

.modal-header {
    background: #4e73df;
    color: white;
    border-radius: 12px 12px 0 0;
    border: none;
}

.modal-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.btn-close {
    filter: invert(1);
    opacity: 0.8;
}

.modal-body {
    padding: 25px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .preview-section {
        position: static;
        order: -1;
    }
}

@media (max-width: 768px) {
    .skills-create-container {
        padding: 20px;
    }
    
    .header-content {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .page-title {
        font-size: 1.6rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .skill-form {
        padding: 20px;
    }
    
    .form-header {
        padding: 20px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .skills-create-container {
        padding: 15px;
    }
    
    .page-header {
        padding: 20px;
    }
    
    .skill-form {
        padding: 15px;
    }
    
    .form-header {
        padding: 15px;
    }
    
    .form-header h3 {
        font-size: 1.3rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const descriptionTextarea = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    
    // Character counter for description
    function updateCharCount() {
        const length = descriptionTextarea.value.length;
        charCount.textContent = `${length}/300`;
        
        if (length > 300) {
            charCount.style.color = '#e53e3e';
        } else {
            charCount.style.color = '#718096';
        }
    }
    
    // Initialize character count
    updateCharCount();
    
    descriptionTextarea.addEventListener('input', updateCharCount);
    
    // Form validation
    const form = document.getElementById('skillForm');
    form.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const category = document.getElementById('category').value;
        
        if (!name) {
            e.preventDefault();
            alert('يرجى إدخال اسم المهارة');
            return;
        }
        
        if (!category) {
            e.preventDefault();
            alert('يرجى اختيار فئة المهارة');
            return;
        }
        
        if (descriptionTextarea.value.length > 300) {
            e.preventDefault();
            alert('وصف المهارة يجب أن يكون أقل من 300 حرف');
            return;
        }
    });
});

function previewSkill() {
    const name = document.getElementById('name').value || 'اسم المهارة';
    const category = document.getElementById('category').value || 'الفئة';
    const skillLevel = document.getElementById('skill_level').value || 'متوسط';
    const experienceYears = document.getElementById('experience_years').value || '3-5 سنوات';
    const description = document.getElementById('description').value || 'لا يوجد وصف متاح';
    const certificates = document.getElementById('certificates').value || 'لا توجد شهادات';
    
    const previewContent = `
        <div class="skill-preview">
            <div class="preview-header-section">
                <div class="preview-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="preview-title">
                    <h4>${name}</h4>
                    <span class="preview-category">${category}</span>
                </div>
            </div>
            
            <div class="preview-details">
                <div class="detail-row">
                    <span class="detail-label">مستوى المهارة:</span>
                    <span class="detail-value">${skillLevel}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">سنوات الخبرة:</span>
                    <span class="detail-value">${experienceYears}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">الوصف:</span>
                    <p class="detail-description">${description}</p>
                </div>
                <div class="detail-row">
                    <span class="detail-label">الشهادات:</span>
                    <p class="detail-certificates">${certificates}</p>
                </div>
            </div>
        </div>
    `;
    
    // Show preview in sidebar
    document.getElementById('previewContent').innerHTML = previewContent;
    document.getElementById('previewSection').style.display = 'block';
    
    // Also show in modal for mobile
    if (window.innerWidth <= 1200) {
        document.getElementById('modalPreviewContent').innerHTML = previewContent;
        const modal = new bootstrap.Modal(document.getElementById('skillPreviewModal'));
        modal.show();
    }
}

function hidePreview() {
    document.getElementById('previewSection').style.display = 'none';
}

// Add CSS for preview
const previewStyle = document.createElement('style');
previewStyle.textContent = `
    .skill-preview {
        padding: 0;
    }
    
    .preview-header-section {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .preview-icon {
        width: 50px;
        height: 50px;
        background: #4e73df;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }
    
    .preview-title h4 {
        margin: 0 0 5px 0;
        color: #2d3748;
        font-size: 1.2rem;
    }
    
    .preview-category {
        background: #e2e8f0;
        color: #4a5568;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .preview-details {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .detail-row {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .detail-label {
        font-weight: 600;
        color: #4a5568;
        font-size: 0.9rem;
    }
    
    .detail-value {
        color: #2d3748;
        font-size: 0.95rem;
    }
    
    .detail-description,
    .detail-certificates {
        color: #718096;
        font-size: 0.9rem;
        line-height: 1.5;
        margin: 0;
        background: #f7fafc;
        padding: 10px;
        border-radius: 6px;
        border-right: 3px solid #4e73df;
    }
`;
document.head.appendChild(previewStyle);
</script>
@endsection 