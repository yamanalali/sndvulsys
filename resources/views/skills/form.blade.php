<div class="skill-form-simple">
    <form method="POST" action="{{ isset($skill) ? route('skills.update', $skill->id) : route('skills.store') }}" class="simple-form">
        @csrf
        @if(isset($skill))
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="name" class="form-label">
                <i class="fas fa-tag"></i>
                اسم المهارة
                <span class="required">*</span>
            </label>
            <input type="text" 
                   name="name" 
                   id="name" 
                   class="form-input" 
                   value="{{ old('name', $skill->name ?? '') }}" 
                   placeholder="أدخل اسم المهارة..."
                   required>
        </div>

        <div class="form-group">
            <label for="category" class="form-label">
                <i class="fas fa-folder"></i>
                الفئة
            </label>
            <select name="category" id="category" class="form-select">
                <option value="">اختر الفئة</option>
                <option value="تقنية" {{ old('category', $skill->category ?? '') == 'تقنية' ? 'selected' : '' }}>تقنية</option>
                <option value="تعليمية" {{ old('category', $skill->category ?? '') == 'تعليمية' ? 'selected' : '' }}>تعليمية</option>
                <option value="طبية" {{ old('category', $skill->category ?? '') == 'طبية' ? 'selected' : '' }}>طبية</option>
                <option value="اجتماعية" {{ old('category', $skill->category ?? '') == 'اجتماعية' ? 'selected' : '' }}>اجتماعية</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">
                <i class="fas fa-align-left"></i>
                الوصف (اختياري)
            </label>
            <textarea name="description" 
                      id="description" 
                      class="form-textarea" 
                      rows="3" 
                      placeholder="وصف مختصر للمهارة...">{{ old('description', $skill->description ?? '') }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($skill) ? 'تحديث المهارة' : 'إضافة المهارة' }}
            </button>
        </div>
    </form>
</div>

<style>
.skill-form-simple {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.simple-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
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
    border-radius: 6px;
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
    min-height: 80px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    padding-top: 10px;
    border-top: 1px solid #e2e8f0;
}

.btn {
    padding: 12px 24px;
    border-radius: 6px;
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
</style> 