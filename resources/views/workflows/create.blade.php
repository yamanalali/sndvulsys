@extends('layouts.app')

@section('title', 'إنشاء سير عمل جديد')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        إنشاء سير عمل جديد
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('workflows.store') }}" method="POST" id="workflowForm">
                        @csrf
                        
                        <!-- طلب التطوع -->
                        <div class="mb-4">
                            <label for="volunteer-request_id" class="form-label fw-bold">
                                طلب التطوع <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('volunteer-request_id') is-invalid @enderror" 
                                    id="volunteer-request_id" 
                                    name="volunteer-request_id" 
                                    required>
                                <option value="">اختر طلب التطوع</option>
                                @foreach($volunteerRequests as $request)
                                    <option value="{{ $request->id }}" {{ old('volunteer-request_id') == $request->id ? 'selected' : '' }}>
                                        {{ $request->full_name }} - {{ $request->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('volunteer-request_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الحالة والخطوة -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-bold">
                                    الحالة <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="">اختر الحالة</option>
                                    @foreach($statuses as $key => $status)
                                        <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="step" class="form-label fw-bold">
                                    الخطوة <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('step') is-invalid @enderror" 
                                        id="step" 
                                        name="step" 
                                        required>
                                    <option value="">اختر الخطوة</option>
                                    @foreach($steps as $key => $step)
                                        <option value="{{ $key }}" {{ old('step') == $key ? 'selected' : '' }}>
                                            {{ $step }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('step')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- الأولوية والمعني -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="priority" class="form-label fw-bold">
                                    الأولوية <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" 
                                        name="priority" 
                                        required>
                                    <option value="">اختر الأولوية</option>
                                    @foreach($priorities as $key => $priority)
                                        <option value="{{ $key }}" {{ old('priority') == $key ? 'selected' : '' }}>
                                            {{ $priority }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="assigned_to" class="form-label fw-bold">المعني</label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                        id="assigned_to" 
                                        name="assigned_to">
                                    <option value="">اختر المستخدم</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- تاريخ الاستحقاق -->
                        <div class="mb-4">
                            <label for="due_date" class="form-label fw-bold">تاريخ الاستحقاق</label>
                            <input type="datetime-local" 
                                   class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" 
                                   name="due_date" 
                                   value="{{ old('due_date') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الملاحظات -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold">ملاحظات</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="ملاحظات إضافية حول سير العمل...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('workflows.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                رجوع
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                إنشاء سير العمل
                            </button>
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
    $('#workflowForm').on('submit', function(e) {
        var isValid = true;
        
        // التحقق من الحقول المطلوبة
        var requiredFields = ['volunteer-request_id', 'status', 'step', 'priority'];
        
        requiredFields.forEach(function(field) {
            var value = $('#' + field).val();
            if (!value) {
                $('#' + field).addClass('is-invalid');
                isValid = false;
            } else {
                $('#' + field).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            // إظهار رسالة خطأ
            if (typeof toastr !== 'undefined') {
                toastr.error('يرجى ملء جميع الحقول المطلوبة');
            } else {
                alert('يرجى ملء جميع الحقول المطلوبة');
            }
        }
    });
    
    // إزالة رسائل الخطأ عند الكتابة
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });

    // تعيين تاريخ افتراضي للاستحقاق (بعد أسبوع)
    if (!$('#due_date').val()) {
        var nextWeek = new Date();
        nextWeek.setDate(nextWeek.getDate() + 7);
        var formattedDate = nextWeek.toISOString().slice(0, 16);
        $('#due_date').val(formattedDate);
    }
});
</script>
@endpush