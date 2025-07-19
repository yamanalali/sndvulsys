@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>تفاصيل طلب التطوع</h4>
                    <div>
                        <a href="{{ route('applicationtovolunteer.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> رجوع
                        </a>
                        <a href="{{ route('applicationtovolunteer.edit', $application->uuid) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Status Update Form -->
                    @if(auth()->check())
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6>تحديث الحالة</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('applicationtovolunteer.updateStatus', $application->uuid) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>الحالة</label>
                                                <select class="form-control" name="status" required>
                                                    <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>معلق</option>
                                                    <option value="approved" {{ $application->status == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                                    <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                                    <option value="withdrawn" {{ $application->status == 'withdrawn' ? 'selected' : '' }}>مسحوب</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>ملاحظات الإدارة</label>
                                                <textarea class="form-control" name="admin_notes" rows="2">{{ $application->admin_notes }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">تحديث</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Application Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5>المعلومات الشخصية</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>الاسم الكامل:</strong></td>
                                    <td>{{ $application->full_name_with_last_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>البريد الإلكتروني:</strong></td>
                                    <td>{{ $application->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>رقم الهاتف:</strong></td>
                                    <td>{{ $application->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>رقم الهوية الوطنية:</strong></td>
                                    <td>{{ $application->national_id ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>تاريخ الميلاد:</strong></td>
                                    <td>{{ $application->birth_date ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>الجنس:</strong></td>
                                    <td>{{ $application->gender_text ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>العمر:</strong></td>
                                    <td>{{ $application->age ? $application->age . ' سنة' : 'غير محدد' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>معلومات الاتصال والعنوان</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>العنوان:</strong></td>
                                    <td>{{ $application->address ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>المدينة:</strong></td>
                                    <td>{{ $application->city ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>البلد:</strong></td>
                                    <td>{{ $application->country ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>المستوى التعليمي:</strong></td>
                                    <td>{{ $application->education_level ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>المهنة:</strong></td>
                                    <td>{{ $application->occupation ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>المجال المفضل:</strong></td>
                                    <td>{{ $application->preferred_area ?: 'غير محدد' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>المهارات والخبرات</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>المهارات:</strong></td>
                                    <td>{{ $application->skills ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>الخبرات السابقة:</strong></td>
                                    <td>{{ $application->previous_experience ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>خبرة سابقة في التطوع:</strong></td>
                                    <td>{{ $application->has_previous_volunteering ? 'نعم' : 'لا' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>نوع المنظمة المفضلة:</strong></td>
                                    <td>{{ $application->preferred_organization_type ?: 'غير محدد' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>معلومات إضافية</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>الدافع للتطوع:</strong></td>
                                    <td>{{ $application->motivation ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>التوفر:</strong></td>
                                    <td>{{ $application->availability ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>جهة الاتصال في الطوارئ:</strong></td>
                                    <td>{{ $application->emergency_contact_name ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>هاتف الطوارئ:</strong></td>
                                    <td>{{ $application->emergency_contact_phone ?: 'غير محدد' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Application Status and Review Info -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5>معلومات الطلب</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>رقم الطلب:</strong></td>
                                    <td>{{ $application->uuid }}</td>
                                </tr>
                                <tr>
                                    <td><strong>الحالة:</strong></td>
                                    <td>
                                        @switch($application->status)
                                            @case('pending')
                                                <span class="badge badge-warning">{{ $application->status_text }}</span>
                                                @break
                                            @case('approved')
                                                <span class="badge badge-success">{{ $application->status_text }}</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge badge-danger">{{ $application->status_text }}</span>
                                                @break
                                            @case('withdrawn')
                                                <span class="badge badge-secondary">{{ $application->status_text }}</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>الأيام منذ التقديم:</strong></td>
                                    <td>{{ $application->days_since_created }} يوم</td>
                                </tr>
                                <tr>
                                    <td><strong>تاريخ التقديم:</strong></td>
                                    <td>{{ $application->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>آخر تحديث:</strong></td>
                                    <td>{{ $application->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>معلومات المراجعة</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>تمت المراجعة:</strong></td>
                                    <td>{{ $application->reviewed_at ? 'نعم' : 'لا' }}</td>
                                </tr>
                                @if($application->reviewed_at)
                                    <tr>
                                        <td><strong>تاريخ المراجعة:</strong></td>
                                        <td>{{ $application->reviewed_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>تمت المراجعة بواسطة:</strong></td>
                                        <td>{{ $application->reviewer ? $application->reviewer->name : 'غير محدد' }}</td>
                                    </tr>
                                @endif
                                @if($application->admin_notes)
                                    <tr>
                                        <td><strong>ملاحظات الإدارة:</strong></td>
                                        <td>{{ $application->admin_notes }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 