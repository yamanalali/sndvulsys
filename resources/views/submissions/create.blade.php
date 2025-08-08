@extends('layouts.app')

@section('title', 'إنشاء إرسال جديد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">إنشاء إرسال جديد</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="volunteer-request_id" class="form-label">طلب التطوع *</label>
                                    <select name="volunteer-request_id" id="volunteer-request_id" class="form-control @error('volunteer-request_id') is-invalid @enderror" required>
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
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="assigned_to" class="form-label">تعيين إلى</label>
                                    <select name="assigned_to" id="assigned_to" class="form-control @error('assigned_to') is-invalid @enderror">
                                        <option value="">اختر المراجع</option>
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
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">الأولوية *</label>
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
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
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">تاريخ الاستحقاق</label>
                                    <input type="date" name="due_date" id="due_date" 
                                           class="form-control @error('due_date') is-invalid @enderror" 
                                           value="{{ old('due_date') }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="attachments" class="form-label">المرفقات</label>
                            <input type="file" name="attachments[]" id="attachments" 
                                   class="form-control @error('attachments.*') is-invalid @enderror" 
                                   multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="form-text text-muted">
                                يمكنك رفع عدة ملفات. الأنواع المسموحة: PDF, DOC, DOCX, JPG, JPEG, PNG. الحد الأقصى: 2MB لكل ملف.
                            </small>
                            @error('attachments.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('submissions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> رجوع
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الإرسال
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
// تحديث قائمة طلبات التطوع عند تغيير الحالة
document.getElementById('volunteer-request_id').addEventListener('change', function() {
    const selectedRequest = this.options[this.selectedIndex];
    if (selectedRequest.value) {
        // يمكن إضافة منطق إضافي هنا
        console.log('تم اختيار طلب التطوع:', selectedRequest.text);
    }
});

// التحقق من تاريخ الاستحقاق
document.getElementById('due_date').addEventListener('change', function() {
    const selectedDate = new Date(this.value);
    const now = new Date();
    
    if (selectedDate <= now) {
        alert('يجب أن يكون تاريخ الاستحقاق في المستقبل');
        this.value = '';
    }
});
</script>
@endpush 