@extends('layouts.app')

@section('title', 'إضافة مهارة جديدة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">إضافة مهارة جديدة</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('skills.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="volunteer-request_id" class="form-label">المتطوع <span class="text-danger">*</span></label>
                                    <select class="form-control @error('volunteer-request_id') is-invalid @enderror" 
                                            id="volunteer-request_id" 
                                            name="volunteer-request_id" 
                                            required>
                                        <option value="">اختر المتطوع</option>
                                        @foreach($volunteerRequests as $request)
                                            <option value="{{ $request->id }}" {{ old('volunteer-request_id') == $request->id ? 'selected' : '' }}>
                                                {{ $request->full_name ?? 'غير محدد' }} - {{ $request->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('volunteer-request_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">اسم المهارة <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="أدخل اسم المهارة"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category" class="form-label">الفئة <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category') is-invalid @enderror" 
                                            id="category" 
                                            name="category" 
                                            required>
                                        <option value="">اختر الفئة</option>
                                        @foreach($categories as $key => $category)
                                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="level" class="form-label">المستوى <span class="text-danger">*</span></label>
                                    <select class="form-control @error('level') is-invalid @enderror" 
                                            id="level" 
                                            name="level" 
                                            required>
                                        <option value="">اختر المستوى</option>
                                        @foreach($levels as $key => $level)
                                            <option value="{{ $key }}" {{ old('level') == $key ? 'selected' : '' }}>
                                                {{ $level }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="years_experience" class="form-label">سنوات الخبرة <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('years_experience') is-invalid @enderror" 
                                           id="years_experience" 
                                           name="years_experience" 
                                           value="{{ old('years_experience', 1) }}" 
                                           min="0" 
                                           max="50" 
                                           required>
                                    @error('years_experience')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active" class="form-label">الحالة</label>
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               class="form-check-input @error('is_active') is-invalid @enderror" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1" 
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            تفعيل المهارة
                                        </label>
                                    </div>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">الوصف</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4" 
                                              placeholder="أدخل وصف مختصر للمهارة">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    حفظ المهارة
                                </button>
                                <a href="{{ route('skills.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-right"></i>
                                    رجوع
                                </a>
                            </div>
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