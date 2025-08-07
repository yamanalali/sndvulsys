@extends('layouts.app')

@section('title', 'تفاصيل التوفر')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">تفاصيل التوفر</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>معلومات المتطوع</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>الاسم:</strong></td>
                                    <td>{{ $availability->volunteerRequest->full_name ?? 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>البريد الإلكتروني:</strong></td>
                                    <td>{{ $availability->volunteerRequest->email ?? 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>رقم الهاتف:</strong></td>
                                    <td>{{ $availability->volunteerRequest->phone ?? 'غير محدد' }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>تفاصيل التوفر</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>اليوم:</strong></td>
                                    <td>
                                        <span class="badge badge-primary">{{ $days[$availability->day] ?? $availability->day }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>فترة الوقت:</strong></td>
                                    <td>
                                        @if($availability->time_slot)
                                            <span class="badge badge-info">{{ $timeSlots[$availability->time_slot] ?? $availability->time_slot }}</span>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>وقت البداية:</strong></td>
                                    <td>
                                        @if($availability->start_time)
                                            {{ \Carbon\Carbon::parse($availability->start_time)->format('H:i') }}
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>وقت النهاية:</strong></td>
                                    <td>
                                        @if($availability->end_time)
                                            {{ \Carbon\Carbon::parse($availability->end_time)->format('H:i') }}
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>الحالة:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $availability->is_available ? 'success' : 'danger' }}">
                                            {{ $availability->is_available ? 'متاح' : 'غير متاح' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>الساعات المفضلة:</strong></td>
                                    <td>
                                        @if($availability->preferred_hours_per_week)
                                            <span class="badge badge-secondary">{{ $availability->preferred_hours_per_week }} ساعة/أسبوع</span>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($availability->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>ملاحظات</h5>
                            <div class="alert alert-info">
                                {{ $availability->notes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-12">
                            <a href="{{ route('availabilities.edit', $availability->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <a href="{{ route('availabilities.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> رجوع
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 