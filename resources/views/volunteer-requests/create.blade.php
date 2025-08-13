@extends('layouts.app')

@section('content')
<style>
    /* RTL Styles for Volunteer Request Form */
    .rtl-volunteer-form {
        direction: rtl;
        text-align: right;
        font-family: 'Cairo', 'Amiri', 'Arabic Typesetting', 'Tahoma', sans-serif;
    }
    
    .rtl-volunteer-form .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .rtl-volunteer-form h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 30px;
        font-weight: bold;
        font-size: 28px;
        border-bottom: 3px solid #3498db;
        padding-bottom: 15px;
    }
    
    .rtl-volunteer-form h4.sub-title {
        color: #34495e;
        margin-bottom: 25px;
        font-size: 20px;
        text-align: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px;
        border-radius: 8px;
        border-right: 4px solid #3498db;
    }
    
    .rtl-volunteer-form .form-group {
        margin-bottom: 20px;
    }
    
    .rtl-volunteer-form .form-group label {
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .rtl-volunteer-form .form-control {
        direction: rtl;
        text-align: right;
        border: 2px solid #e1e8ed;
        border-radius: 6px;
        padding: 12px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
        background-color: #fff;
    }
    
    .rtl-volunteer-form .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        outline: none;
    }
    
    .rtl-volunteer-form .form-control::placeholder {
        text-align: right;
        color: #95a5a6;
        font-style: italic;
    }
    
    .rtl-volunteer-form select.form-control {
        background-position: left 12px center;
        padding-left: 40px;
    }
    
    .rtl-volunteer-form textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    
    /* Radio buttons and checkboxes RTL styling */
    .rtl-volunteer-form .form-check {
        text-align: right;
        padding-right: 0;
        padding-left: 1.25rem;
        margin-bottom: 10px;
    }
    
    .rtl-volunteer-form .form-check-input {
        margin-right: -1.25rem;
        margin-left: 0;
        float: right;
        transform: scale(1.2);
        cursor: pointer;
    }
    
    .rtl-volunteer-form .form-check-input:checked {
        background-color: #3498db;
        border-color: #3498db;
    }
    
    .rtl-volunteer-form .form-check-input:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
    }
    
    .rtl-volunteer-form .form-check-label {
        padding-right: 15px;
        padding-left: 0;
        color: #2c3e50;
        margin-right: 0.5rem;
        cursor: pointer;
        user-select: none;
    }
    
    .rtl-volunteer-form .form-check {
        padding-right: 1.5rem;
        padding-left: 0;
        margin-bottom: 0.5rem;
    }
    
    /* Skills container styling */
    .rtl-volunteer-form .skills-container label {
        display: inline-block;
        margin: 5px 10px 5px 0;
        padding: 8px 12px;
        border: 1px solid #e3e6f0;
        border-radius: 6px;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .rtl-volunteer-form .skills-container label:hover {
        background: #e3f2fd;
        border-color: #3498db;
    }
    
    .rtl-volunteer-form .skills-container input[type="checkbox"] {
        margin-left: 8px;
        margin-right: 0;
        transform: scale(1.2);
    }
    
    .rtl-volunteer-form .skills-container input[type="checkbox"]:checked + span {
        color: #3498db;
        font-weight: 600;
    }
    
    .rtl-volunteer-form .form-check-inline {
        margin-left: 0;
        margin-right: 0.75rem;
    }
    
    /* Skills checkboxes styling */
    .rtl-volunteer-form .skills-container {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .rtl-volunteer-form .skills-container label {
        display: inline-block;
        margin: 5px 10px 5px 0;
        padding: 8px 12px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: normal;
    }
    
    .rtl-volunteer-form .skills-container label:hover {
        background: #e3f2fd;
        border-color: #3498db;
    }
    
    .rtl-volunteer-form .skills-container input[type="checkbox"] {
        margin-left: 5px;
        margin-right: 0;
    }
    
    .rtl-volunteer-form .skills-container input[type="checkbox"]:checked + span {
        color: #3498db;
        font-weight: bold;
    }
    
    /* Language section styling */
    .rtl-volunteer-form .language-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        margin-bottom: 20px;
    }
    
    .rtl-volunteer-form .language-section .form-group label {
        color: #495057;
        margin-bottom: 5px;
    }
    
    /* Submit button styling */
    .rtl-volunteer-form .btn-primary {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        font-weight: bold;
        border-radius: 25px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        min-width: 150px;
    }
    
    .rtl-volunteer-form .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
    }
    
    /* File upload styling */
    .rtl-volunteer-form input[type="file"] {
        direction: ltr;
        text-align: left;
    }
    
    .rtl-volunteer-form .file-upload-container {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .rtl-volunteer-form .file-upload-container:hover {
        border-color: #3498db;
        background: #e3f2fd;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .rtl-volunteer-form .container {
            padding: 15px;
        }
        
        .rtl-volunteer-form .col-sm-2,
        .rtl-volunteer-form .col-sm-10 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        .rtl-volunteer-form .form-group.row .col-sm-2 {
            margin-bottom: 5px;
        }
    }
</style>

<div class="rtl-volunteer-form">
    <div class="container">
        <h2>إضافة طلب تطوع جديد</h2>
    <div class="card-block">
        <h4 class="sub-title">نموذج طلب التطوع</h4>
        @if(session('success'))
            <div class="alert alert-success">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>{{ session('success') }}</strong>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li><i class="fas fa-exclamation-triangle me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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
                <label class="col-sm-2 col-form-label">رقم الهوية الوطنية</label>
                <div class="col-sm-10">
                    <input type="text" name="national_id" class="form-control" placeholder="أدخل رقم الهوية الوطنية">
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
                <div class="col-sm-10 pt-2">
                    <div class="form-check form-check-inline" style="margin-left: 15px;">
                        <input class="form-check-input" type="radio" name="gender" value="male" id="gender_male" style="margin-left: 8px;">
                        <label class="form-check-label" for="gender_male" style="margin-right: 20px;">ذكر</label>
                    </div>
                    <div class="form-check form-check-inline" style="margin-left: 15px;">
                        <input class="form-check-input" type="radio" name="gender" value="female" id="gender_female" style="margin-left: 8px;">
                        <label class="form-check-label" for="gender_female" style="margin-right: 20px;">أنثى</label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">* الحالة الاجتماعية</label>
                <div class="col-sm-10 pt-2">
                    <div class="form-check form-check-inline" style="margin-left: 15px;">
                        <input class="form-check-input" type="radio" name="social_status" value="single" id="social_single" style="margin-left: 8px;">
                        <label class="form-check-label" for="social_single" style="margin-right: 20px;">عازبة / أعزب</label>
                    </div>
                    <div class="form-check form-check-inline" style="margin-left: 15px;">
                        <input class="form-check-input" type="radio" name="social_status" value="married" id="social_married" style="margin-left: 8px;">
                        <label class="form-check-label" for="social_married" style="margin-right: 20px;">متزوجة / متزوج</label>
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
                <div class="col-sm-10">
                    <div class="skills-container">
                        @if(isset($skills) && $skills->count() > 0)
                            @foreach($skills as $skill)
                                <label>
                                    <input type="checkbox" name="skills[]" value="{{ $skill->name }}" />
                                    <span>{{ $skill->name }}</span>
                                    <small class="text-muted">({{ $skill->category }})</small>
                                </label>
                            @endforeach
                        @else
                            <!-- المهارات الافتراضية إذا لم تكن موجودة في قاعدة البيانات -->
                            <label>
                                <input type="checkbox" name="skills[]" value="العمل الجماعي" />
                                <span>العمل الجماعي</span>
                            </label>
                            <label>
                                <input type="checkbox" name="skills[]" value="القيادة" />
                                <span>القيادة</span>
                            </label>
                            <label>
                                <input type="checkbox" name="skills[]" value="التواصل" />
                                <span>التواصل</span>
                            </label>
                            <label>
                                <input type="checkbox" name="skills[]" value="التنظيم" />
                                <span>التنظيم</span>
                            </label>
                            <label>
                                <input type="checkbox" name="skills[]" value="حل المشكلات" />
                                <span>حل المشكلات</span>
                            </label>
                            <label>
                                <input type="checkbox" name="skills[]" value="استخدام الحاسوب" />
                                <span>استخدام الحاسوب</span>
                            </label>
                            <label>
                                <input type="checkbox" name="skills[]" value="التصميم" />
                                <span>التصميم</span>
                            </label>
                            <label>
                                <input type="checkbox" name="skills[]" value="الترجمة" />
                                <span>الترجمة</span>
                            </label>
                            <label>
                                <input type="checkbox" name="skills[]" value="البرمجة" />
                                <span>البرمجة</span>
                            </label>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">اللغات ومستوى الإتقان</label>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="arabic_auth" class="form-label">العربية</label>
                            <select name="languages[العربية]" class="form-control" id="arabic_auth">
                                <option value="">اختر المستوى</option>
                                <option value="لغة أم">لغة أم</option>
                                <option value="ممتاز">ممتاز</option>
                                <option value="جيد جداً">جيد جداً</option>
                                <option value="جيد">جيد</option>
                                <option value="مبتدئ">مبتدئ</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="english_auth" class="form-label">الإنجليزية</label>
                            <select name="languages[الإنجليزية]" class="form-control" id="english_auth">
                                <option value="">اختر المستوى</option>
                                <option value="لغة أم">لغة أم</option>
                                <option value="ممتاز">ممتاز</option>
                                <option value="جيد جداً">جيد جداً</option>
                                <option value="جيد">جيد</option>
                                <option value="مبتدئ">مبتدئ</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="french_auth" class="form-label">الفرنسية</label>
                            <select name="languages[الفرنسية]" class="form-control" id="french_auth">
                                <option value="">اختر المستوى</option>
                                <option value="لغة أم">لغة أم</option>
                                <option value="ممتاز">ممتاز</option>
                                <option value="جيد جداً">جيد جداً</option>
                                <option value="جيد">جيد</option>
                                <option value="مبتدئ">مبتدئ</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="german_auth" class="form-label">الألمانية</label>
                            <select name="languages[الألمانية]" class="form-control" id="german_auth">
                                <option value="">اختر المستوى</option>
                                <option value="لغة أم">لغة أم</option>
                                <option value="ممتاز">ممتاز</option>
                                <option value="جيد جداً">جيد جداً</option>
                                <option value="جيد">جيد</option>
                                <option value="مبتدئ">مبتدئ</option>
                            </select>
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
                    <div class="file-upload-container">
                        <input type="file" name="cv" class="form-control" accept="application/pdf">
                        <small class="form-text text-muted">يرجى رفع ملف PDF فقط.</small>
                    </div>
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-sm-10 offset-sm-2" style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">إرسال الطلب</button>
                </div>
            </div>
        </form>
    </div>
    </div>
</div>

@endsection 