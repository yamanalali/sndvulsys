@extends('layouts.app')

@section('content')
<div class="card-block">
    {{-- رسالة النجاح --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- رسالة الخطأ --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h4 class="sub-title">نموذج طلب التطوع</h4>
    <form method="POST" action="{{ route('volunteer-requests.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">الاسم الكامل</label>
            <div class="col-sm-10">
                <input type="text" name="full_name" class="form-control" placeholder="أدخل اسمك الكامل" required>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">البريد الإلكتروني</label>
            <div class="col-sm-10">
                <input type="email" name="email" class="form-control" placeholder="أدخل بريدك الإلكتروني" required>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">رقم الجوال</label>
            <div class="col-sm-10">
                <input type="text" name="phone" class="form-control" placeholder="أدخل رقم الجوال" required>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">رقم الهوية</label>
            <div class="col-sm-10">
                <input type="text" name="national_id" class="form-control" placeholder="أدخل رقم الهوية">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">تاريخ الميلاد</label>
            <div class="col-sm-10">
                <input type="date" name="birth_date" class="form-control">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">الجنس</label>
            <div class="col-sm-10">
                <select name="gender" class="form-control">
                    <option value="">اختر الجنس</option>
                    <option value="male">ذكر</option>
                    <option value="female">أنثى</option>
                </select>
            </div>
        </div>
                    <div class="form-group row">
                <label class="col-sm-2 col-form-label">* الحالة الاجتماعية</label>
                <div class="col-sm-10 pt-2">
                    <div class="form-check form-check-inline" style="margin-left: 15px;">
                        <input class="form-check-input" type="radio" name="social_status" value="single" id="social_single" style="margin-left: 8px;">
                        <label class="form-check-label" for="social_single" style="margin-right: 15px;">عازبة / أعزب</label>
                    </div>
                    <div class="form-check form-check-inline" style="margin-left: 15px;">
                        <input class="form-check-input" type="radio" name="social_status" value="married" id="social_married" style="margin-left: 8px;">
                        <label class="form-check-label" for="social_married" style="margin-right: 15px;">متزوجة / متزوج</label>
                    </div>
                </div>
            </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">العنوان</label>
            <div class="col-sm-10">
                <input type="text" name="address" class="form-control" placeholder="أدخل العنوان">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">الدولة</label>
            <div class="col-sm-10">
                <input type="text" name="country" class="form-control" placeholder="أدخل الدولة">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">المدينة</label>
            <div class="col-sm-10">
                <input type="text" name="city" class="form-control" placeholder="أدخل المدينة">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">* آخر مستوى تعليمي حصلت عليه</label>
            <div class="col-sm-10">
                <select name="education_level" class="form-control">
                    <option value="">اختر المستوى التعليمي</option>
                    <option value="uneducated">غير متعلم</option>
                    <option value="primary">ابتدائي</option>
                    <option value="middle">متوسط</option>
                    <option value="high_school">ثانوي</option>
                    <option value="bachelor">بكالوريوس</option>
                    <option value="master">ماجستير</option>
                    <option value="phd">دكتوراه</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">* التخصص الدراسي</label>
            <div class="col-sm-10">
                <input type="text" name="field_of_study" class="form-control" placeholder="أدخل التخصص الدراسي">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">الوظيفة الحالية</label>
            <div class="col-sm-10">
                <input type="text" name="occupation" class="form-control" placeholder="أدخل الوظيفة الحالية">
            </div>
        </div>
        <div class="form-group row">
                <label class="col-sm-2 col-form-label">المهارات</label>
                <div class="col-sm-10" style="padding-top: 8px;">
                    <label class="inline-flex items-center" style="margin-left: 15px;">
                        <input type="checkbox" name="skills[]" value="العمل الجماعي" class="form-checkbox" />
                        <span style="margin-right: 5px;">العمل الجماعي</span>
                    </label>
                    <label class="inline-flex items-center" style="margin-left: 15px;">
                        <input type="checkbox" name="skills[]" value="القيادة" class="form-checkbox" />
                        <span style="margin-right: 5px;">القيادة</span>
                    </label>
                    <label class="inline-flex items-center" style="margin-left: 15px;">
                        <input type="checkbox" name="skills[]" value="التواصل" class="form-checkbox" />
                        <span style="margin-right: 5px;">التواصل</span>
                    </label>
                    <label class="inline-flex items-center" style="margin-left: 15px;">
                        <input type="checkbox" name="skills[]" value="التنظيم" class="form-checkbox" />
                        <span style="margin-right: 5px;">التنظيم</span>
                    </label>
                    <label class="inline-flex items-center" style="margin-left: 15px;">
                        <input type="checkbox" name="skills[]" value="حل المشكلات" class="form-checkbox" />
                        <span style="margin-right: 5px;">حل المشكلات</span>
                    </label>
                    <label class="inline-flex items-center" style="margin-left: 15px;">
                        <input type="checkbox" name="skills[]" value="استخدام الحاسوب" class="form-checkbox" />
                        <span style="margin-right: 5px;">استخدام الحاسوب</span>
                    </label>
                    <label class="inline-flex items-center" style="margin-left: 15px;">
                        <input type="checkbox" name="skills[]" value="التصميم" class="form-checkbox" />
                        <span style="margin-right: 5px;">التصميم</span>
                    </label>
                    <label class="inline-flex items-center" style="margin-left: 15px;">
                        <input type="checkbox" name="skills[]" value="الترجمة" class="form-checkbox" />
                        <span style="margin-right: 5px;">الترجمة</span>
                    </label>
                    <label class="inline-flex items-center" style="margin-left: 15px;">
                        <input type="checkbox" name="skills[]" value="البرمجة" class="form-checkbox" />
                        <span style="margin-right: 5px;">البرمجة</span>
                    </label>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">اللغات ومستوياتها</label>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>العربية</label>
                                <select name="languages[arabic]" class="form-control">
                                    <option value="">اختر المستوى</option>
                                    <option value="none">لا أتكلم هذه اللغة</option>
                                    <option value="مبتدئ">مبتدئ</option>
                                    <option value="متوسط">متوسط</option>
                                    <option value="متقدم">متقدم</option>
                                    <option value="ممتاز">ممتاز</option>
                                    <option value="لغة أم">لغة أم</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>الإنجليزية</label>
                                <select name="languages[english]" class="form-control">
                                    <option value="">اختر المستوى</option>
                                    <option value="none">لا أتكلم هذه اللغة</option>
                                    <option value="مبتدئ">مبتدئ</option>
                                    <option value="متوسط">متوسط</option>
                                    <option value="متقدم">متقدم</option>
                                    <option value="ممتاز">ممتاز</option>
                                    <option value="لغة أم">لغة أم</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>أخرى</label>
                                <input type="text" name="languages[other]" class="form-control" placeholder="اذكر اللغة والمستوى">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">سبب التقديم</label>
            <div class="col-sm-10">
                <textarea name="motivation" class="form-control" rows="3" placeholder="لماذا ترغب في التطوع؟"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">الخبرة السابقة في العمل التطوعي</label>
            <div class="col-sm-10">
                <textarea name="previous_experience" class="form-control" rows="3" placeholder="اذكر خبراتك السابقة في العمل التطوعي"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">المجال التطوعي المفضل</label>
            <div class="col-sm-10">
                <input type="text" name="preferred_area" class="form-control" placeholder="أدخل المجال التطوعي المفضل">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">مدى التفرغ (أيام/ساعات التطوع)</label>
            <div class="col-sm-10">
                <input type="text" name="availability" class="form-control" placeholder="مثال: يومان في الأسبوع، 3 ساعات يومياً ...">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">هل سبق لك التطوع؟</label>
            <div class="col-sm-10">
                <select name="has_previous_volunteering" class="form-control">
                    <option value="0">لا</option>
                    <option value="1">نعم</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">نوع المنظمة المفضلة</label>
            <div class="col-sm-10">
                <input type="text" name="preferred_organization_type" class="form-control" placeholder="أدخل نوع المنظمة المفضلة">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">رفع السيرة الذاتية (PDF فقط)</label>
            <div class="col-sm-10">
                <input type="file" name="cv" class="form-control" accept="application/pdf">
                <small class="form-text text-muted">يرجى رفع ملف PDF فقط.</small>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-10 offset-sm-2">
                <button type="submit" class="btn btn-primary">إرسال الطلب</button>
            </div>
        </div>
    </form>
</div>
@endsection 