@extends('layouts.app')

@section('title', 'إضافة خبرة جديدة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">إضافة خبرة جديدة</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('previous-experiences.store') }}" method="POST">
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
                                    <label for="title" class="form-label">عنوان الخبرة <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="organization" class="form-label">المؤسسة <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('organization') is-invalid @enderror" 
                                           id="organization" 
                                           name="organization" 
                                           value="{{ old('organization') }}" 
                                           required>
                                    @error('organization')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position" class="form-label">المنصب <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('position') is-invalid @enderror" 
                                           id="position" 
                                           name="position" 
                                           value="{{ old('position') }}" 
                                           required>
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date" class="form-label">تاريخ البداية <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="{{ old('start_date') }}" 
                                           required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date" class="form-label">تاريخ النهاية</label>
                                    <input type="date" 
                                           class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" 
                                           name="end_date" 
                                           value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">اتركه فارغاً إذا كانت الخبرة حالية</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="is_current" class="form-label">نوع الخبرة</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_current" 
                                               name="is_current" 
                                               value="1" 
                                               {{ old('is_current') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_current">خبرة حالية</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="وصف مختصر للخبرة...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الخبرة
                            </button>
                            <a href="{{ route('previous-experiences.index') }}" class="btn btn-secondary">
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
    // التحكم في تاريخ النهاية بناءً على حالة الخبرة الحالية
    $('#is_current').change(function() {
        if ($(this).is(':checked')) {
            $('#end_date').prop('disabled', true).val('');
        } else {
            $('#end_date').prop('disabled', false);
        }
    });

    // التحقق من صحة النموذج
    $('form').on('submit', function(e) {
        var isValid = true;
        
        // التحقق من المتطوع
        var volunteer = $('#volunteer-request_id').val();
        if (!volunteer) {
            $('#volunteer-request_id').addClass('is-invalid');
            isValid = false;
        } else {
            $('#volunteer-request_id').removeClass('is-invalid');
        }
        
        // التحقق من عنوان الخبرة
        var title = $('#title').val().trim();
        if (title.length < 3) {
            $('#title').addClass('is-invalid');
            isValid = false;
        } else {
            $('#title').removeClass('is-invalid');
        }
        
        // التحقق من المؤسسة
        var organization = $('#organization').val().trim();
        if (organization.length < 2) {
            $('#organization').addClass('is-invalid');
            isValid = false;
        } else {
            $('#organization').removeClass('is-invalid');
        }
        
        // التحقق من المنصب
        var position = $('#position').val().trim();
        if (position.length < 2) {
            $('#position').addClass('is-invalid');
            isValid = false;
        } else {
            $('#position').removeClass('is-invalid');
        }
        
        // التحقق من تاريخ البداية
        var startDate = $('#start_date').val();
        if (!startDate) {
            $('#start_date').addClass('is-invalid');
            isValid = false;
        } else {
            $('#start_date').removeClass('is-invalid');
        }
        
        // التحقق من تاريخ النهاية إذا لم تكن الخبرة حالية
        if (!$('#is_current').is(':checked')) {
            var endDate = $('#end_date').val();
            if (!endDate) {
                $('#end_date').addClass('is-invalid');
                isValid = false;
            } else {
                $('#end_date').removeClass('is-invalid');
            }
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