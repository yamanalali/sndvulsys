@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>تعديل طلب التطوع</h4>
                    <a href="{{ route('applicationtovolunteer.show', $application->uuid) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> رجوع
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('applicationtovolunteer.update', $application->uuid) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="full_name">الاسم الأول *</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="{{ old('full_name', $application->full_name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">الكنية *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $application->last_name) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">البريد الإلكتروني *</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $application->email) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">رقم الهاتف *</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $application->phone) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="national_id">رقم الهوية الوطنية</label>
                                    <input type="text" class="form-control" id="national_id" name="national_id" value="{{ old('national_id', $application->national_id) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country">البلد</label>
                                    <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $application->country) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birth_date">تاريخ الميلاد</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date', $application->birth_date) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender">الجنس</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="">اختر الجنس</option>
                                        <option value="male" {{ old('gender', $application->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ old('gender', $application->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                                        <option value="other" {{ old('gender', $application->gender) == 'other' ? 'selected' : '' }}>آخر</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">المدينة</label>
                                    <select class="form-control" id="city" name="city">
                                        <option value="">اختر المدينة</option>
                                        @foreach($cities as $key => $city)
                                            <option value="{{ $key }}" {{ old('city', $application->city) == $key ? 'selected' : '' }}>{{ $city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="education_level">المستوى التعليمي</label>
                                    <select class="form-control" id="education_level" name="education_level">
                                        <option value="">اختر المستوى التعليمي</option>
                                        @foreach($educationLevels as $key => $level)
                                            <option value="{{ $key }}" {{ old('education_level', $application->education_level) == $key ? 'selected' : '' }}>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">العنوان</label>
                            <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $application->address) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="occupation">المهنة</label>
                                    <input type="text" class="form-control" id="occupation" name="occupation" value="{{ old('occupation', $application->occupation) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preferred_area">المجال المفضل</label>
                                    <select class="form-control" id="preferred_area" name="preferred_area">
                                        <option value="">اختر المجال المفضل</option>
                                        @foreach($preferredAreas as $key => $area)
                                            <option value="{{ $key }}" {{ old('preferred_area', $application->preferred_area) == $key ? 'selected' : '' }}>{{ $area }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="skills">المهارات</label>
                            <textarea class="form-control" id="skills" name="skills" rows="3" placeholder="اذكر مهاراتك وخبراتك">{{ old('skills', $application->skills) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="motivation">الدافع للتطوع</label>
                            <textarea class="form-control" id="motivation" name="motivation" rows="3" placeholder="ما هو دافعك للتطوع؟">{{ old('motivation', $application->motivation) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="previous_experience">الخبرات السابقة</label>
                            <textarea class="form-control" id="previous_experience" name="previous_experience" rows="3" placeholder="اذكر خبراتك السابقة في التطوع أو العمل التطوعي">{{ old('previous_experience', $application->previous_experience) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="availability">التوفر</label>
                            <textarea class="form-control" id="availability" name="availability" rows="2" placeholder="أوقات توفرك للتطوع">{{ old('availability', $application->availability) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preferred_organization_type">نوع المؤسسة المفضلة</label>
                                    <select class="form-control" id="preferred_organization_type" name="preferred_organization_type">
                                        <option value="">اختر نوع المؤسسة</option>
                                        @foreach($organizationTypes as $key => $type)
                                            <option value="{{ $key }}" {{ old('preferred_organization_type', $application->preferred_organization_type) == $key ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="has_previous_volunteering" name="has_previous_volunteering" value="1" {{ old('has_previous_volunteering', $application->has_previous_volunteering) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_previous_volunteering">
                                        لدي خبرة سابقة في التطوع
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_name">اسم جهة الاتصال في الطوارئ</label>
                                    <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $application->emergency_contact_name) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_phone">رقم هاتف جهة الاتصال في الطوارئ</label>
                                    <input type="text" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $application->emergency_contact_phone) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                تحديث طلب التطوع
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 