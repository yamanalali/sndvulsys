@extends('layouts.app')

@section('title', 'إضافة توفر جديد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">إضافة توفر جديد</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('availabilities.store') }}" method="POST">
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
                                    <label for="day" class="form-label">اليوم <span class="text-danger">*</span></label>
                                    <select class="form-control @error('day') is-invalid @enderror" 
                                            id="day" 
                                            name="day" 
                                            required>
                                        <option value="">اختر اليوم</option>
                                        @foreach($days as $key => $day)
                                            <option value="{{ $key }}" {{ old('day') == $key ? 'selected' : '' }}>
                                                {{ $day }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="time_slot" class="form-label">فترة الوقت</label>
                                    <select class="form-control @error('time_slot') is-invalid @enderror" 
                                            id="time_slot" 
                                            name="time_slot">
                                        <option value="">اختر فترة الوقت</option>
                                        @foreach($timeSlots as $key => $timeSlot)
                                            <option value="{{ $key }}" {{ old('time_slot') == $key ? 'selected' : '' }}>
                                                {{ $timeSlot }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('time_slot')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_time" class="form-label">وقت البداية</label>
                                    <input type="time" 
                                           class="form-control @error('start_time') is-invalid @enderror" 
                                           id="start_time" 
                                           name="start_time" 
                                           value="{{ old('start_time') }}">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_time" class="form-label">وقت النهاية</label>
                                    <input type="time" 
                                           class="form-control @error('end_time') is-invalid @enderror" 
                                           id="end_time" 
                                           name="end_time" 
                                           value="{{ old('end_time') }}">
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preferred_hours_per_week" class="form-label">الساعات المفضلة في الأسبوع</label>
                                    <input type="number" 
                                           class="form-control @error('preferred_hours_per_week') is-invalid @enderror" 
                                           id="preferred_hours_per_week" 
                                           name="preferred_hours_per_week" 
                                           value="{{ old('preferred_hours_per_week') }}" 
                                           min="1" 
                                           max="168" 
                                           placeholder="عدد الساعات">
                                    @error('preferred_hours_per_week')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">عدد الساعات التي يفضل المتطوع العمل بها أسبوعياً</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_available" class="form-label">حالة التوفر</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_available" 
                                               name="is_available" 
                                               value="1" 
                                               {{ old('is_available', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_available">متاح</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="ملاحظات إضافية حول التوفر...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التوفر
                            </button>
                            <a href="{{ route('availabilities.index') }}" class="btn btn-secondary">
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
    // التحكم في أوقات البداية والنهاية بناءً على فترة الوقت المختارة
    $('#time_slot').change(function() {
        var selectedSlot = $(this).val();
        var startTime = $('#start_time');
        var endTime = $('#end_time');
        
        // إعادة تعيين الأوقات
        startTime.val('');
        endTime.val('');
        
        // تعيين الأوقات بناءً على الفترة المختارة
        switch(selectedSlot) {
            case 'morning':
                startTime.val('08:00');
                endTime.val('12:00');
                break;
            case 'afternoon':
                startTime.val('12:00');
                endTime.val('16:00');
                break;
            case 'evening':
                startTime.val('16:00');
                endTime.val('20:00');
                break;
            case 'night':
                startTime.val('20:00');
                endTime.val('24:00');
                break;
            case 'flexible':
                // لا نضع أوقات محددة للتوفر المرن
                break;
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
        
        // التحقق من اليوم
        var day = $('#day').val();
        if (!day) {
            $('#day').addClass('is-invalid');
            isValid = false;
        } else {
            $('#day').removeClass('is-invalid');
        }
        
        // التحقق من أوقات البداية والنهاية
        var startTime = $('#start_time').val();
        var endTime = $('#end_time').val();
        
        if (startTime && endTime) {
            if (startTime >= endTime) {
                $('#end_time').addClass('is-invalid');
                isValid = false;
            } else {
                $('#end_time').removeClass('is-invalid');
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