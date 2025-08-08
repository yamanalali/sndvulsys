@extends('layouts.app')

@section('title', 'تعديل المهارة - ' . $skill->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">تعديل المهارة: {{ $skill->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('skills.update', $skill->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">اسم المهارة <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $skill->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category" class="form-label">الفئة <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category') is-invalid @enderror" 
                                            id="category" 
                                            name="category" 
                                            required>
                                        <option value="">اختر الفئة</option>
                                        @foreach($categories as $key => $category)
                                            <option value="{{ $key }}" {{ old('category', $skill->category) == $key ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="level" class="form-label">المستوى <span class="text-danger">*</span></label>
                                    <select class="form-control @error('level') is-invalid @enderror" 
                                            id="level" 
                                            name="level" 
                                            required>
                                        <option value="">اختر المستوى</option>
                                        @foreach($levels as $key => $level)
                                            <option value="{{ $key }}" {{ old('level', $skill->level) == $key ? 'selected' : '' }}>
                                                {{ $level }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active" class="form-label">الحالة</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1" 
                                               {{ old('is_active', $skill->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">مهارة نشطة</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      placeholder="وصف مختصر للمهارة...">{{ old('description', $skill->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">يمكنك إضافة وصف مفصل للمهارة (اختياري)</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التغييرات
                            </button>
                            <a href="{{ route('skills.show', $skill->id) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> عرض المهارة
                            </a>
                            <a href="{{ route('skills.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right"></i> رجوع
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // التحقق من صحة النموذج
    $('form').on('submit', function(e) {
        var isValid = true;
        
        // التحقق من اسم المهارة
        var name = $('#name').val().trim();
        if (name.length < 2) {
            $('#name').addClass('is-invalid');
            isValid = false;
        } else {
            $('#name').removeClass('is-invalid');
        }
        
        // التحقق من الفئة
        var category = $('#category').val();
        if (!category) {
            $('#category').addClass('is-invalid');
            isValid = false;
        } else {
            $('#category').removeClass('is-invalid');
        }
        
        // التحقق من المستوى
        var level = $('#level').val();
        if (!level) {
            $('#level').addClass('is-invalid');
            isValid = false;
        } else {
            $('#level').removeClass('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
            toastr.error('يرجى تصحيح الأخطاء في النموذج');
        }
    });
    
    // إزالة رسائل الخطأ عند الكتابة
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@endpush 