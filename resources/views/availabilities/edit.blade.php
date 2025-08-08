@extends('layouts.app')

@section('title', 'تعديل التوفر')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-edit"></i> تعديل التوفر
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('availabilities.update', $availability->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- معلومات المتطوع -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-user"></i> معلومات المتطوع
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="volunteer-request_id" class="font-weight-bold">
                                        المتطوع <span class="text-danger">*</span>
                                    </label>
                                    <select name="volunteer-request_id" id="volunteer-request_id" 
                                            class="form-control @error('volunteer-request_id') is-invalid @enderror" required>
                                        <option value="">اختر المتطوع</option>
                                        @foreach($volunteerRequests as $request)
                                            <option value="{{ $request->id }}" {{ $availability->volunteer_request_id == $request->id ? 'selected' : '' }}>
                                                {{ $request->full_name }} ({{ $request->email }})
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
                                    <label for="day" class="font-weight-bold">
                                        اليوم <span class="text-danger">*</span>
                                    </label>
                                    <select name="day" id="day" 
                                            class="form-control @error('day') is-invalid @enderror" required>
                                        <option value="">اختر اليوم</option>
                                        @foreach($days as $key => $day)
                                            <option value="{{ $key }}" {{ $availability->day == $key ? 'selected' : '' }}>
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

                        <!-- تفاصيل الوقت -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-clock"></i> تفاصيل الوقت
                                </h5>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="time_slot" class="font-weight-bold">فترة الوقت</label>
                                    <select name="time_slot" id="time_slot" 
                                            class="form-control @error('time_slot') is-invalid @enderror">
                                        <option value="">اختر فترة الوقت</option>
                                        @foreach($timeSlots as $key => $slot)
                                            <option value="{{ $key }}" {{ $availability->time_slot == $key ? 'selected' : '' }}>
                                                {{ $slot }}
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
                                    <label for="start_time" class="font-weight-bold">وقت البداية</label>
                                    <input type="time" name="start_time" id="start_time" 
                                           class="form-control @error('start_time') is-invalid @enderror"
                                           value="{{ $availability->start_time }}">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_time" class="font-weight-bold">وقت النهاية</label>
                                    <input type="time" name="end_time" id="end_time" 
                                           class="form-control @error('end_time') is-invalid @enderror"
                                           value="{{ $availability->end_time }}">
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- إعدادات إضافية -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-cog"></i> إعدادات إضافية
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preferred_hours_per_week" class="font-weight-bold">الساعات المفضلة أسبوعياً</label>
                                    <div class="input-group">
                                        <input type="number" name="preferred_hours_per_week" id="preferred_hours_per_week" 
                                               class="form-control @error('preferred_hours_per_week') is-invalid @enderror"
                                               value="{{ $availability->preferred_hours_per_week }}" min="1" max="168">
                                        <div class="input-group-append">
                                            <span class="input-group-text">ساعة/أسبوع</span>
                                        </div>
                                    </div>
                                    @error('preferred_hours_per_week')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">حالة التوفر</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="is_available" id="is_available" 
                                               class="custom-control-input" value="1"
                                               {{ $availability->is_available ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_available">
                                            متاح للتطوع
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ملاحظات -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-sticky-note"></i> ملاحظات
                                </h5>
                                <div class="form-group">
                                    <label for="notes" class="font-weight-bold">ملاحظات إضافية</label>
                                    <textarea name="notes" id="notes" rows="4" 
                                              class="form-control @error('notes') is-invalid @enderror">{{ $availability->notes }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-save"></i> حفظ التعديلات
                                </button>
                                <a href="{{ route('availabilities.index') }}" class="btn btn-secondary btn-lg px-5 ml-2">
                                    <i class="fas fa-arrow-right"></i> رجوع
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