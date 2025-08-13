<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب التطوع</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<style>
    /* Guest Mode - Full Page Styles */
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        margin: 0;
        font-family: 'Cairo', 'Amiri', 'Arabic Typesetting', 'Tahoma', sans-serif;
    }
    
    .guest-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        max-width: 700px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        padding: 0;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .guest-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center;
        padding: 30px 20px;
        border-radius: 20px 20px 0 0;
        margin-bottom: 0;
    }
    
    .guest-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .guest-header p {
        margin: 10px 0 0 0;
        opacity: 0.9;
        font-size: 14px;
    }
    
    /* RTL Styles for Volunteer Request Form */
    .rtl-volunteer-form {
        direction: rtl;
        text-align: right;
        font-family: 'Cairo', 'Amiri', 'Arabic Typesetting', 'Tahoma', sans-serif;
    }
    
    .rtl-volunteer-form .container {
        padding: 30px;
    }
    
    .rtl-volunteer-form h2 {
        display: none;
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
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 12px 15px;
        transition: all 0.3s ease;
        text-align: right;
        font-family: 'Cairo', 'Amiri', 'Arabic Typesetting', 'Tahoma', sans-serif;
    }
    
    .rtl-volunteer-form .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
    
    .rtl-volunteer-form .form-control::placeholder {
        text-align: right;
        color: #adb5bd;
        font-style: italic;
    }
    
    .rtl-volunteer-form .form-check {
        text-align: right;
        padding-right: 0;
        padding-left: 1.25rem;
    }
    
    .rtl-volunteer-form .form-check-input {
        float: right;
        margin-right: 0;
        margin-left: -1.25rem;
        transform: scale(1.2);
        cursor: pointer;
    }
    
    .rtl-volunteer-form .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    .rtl-volunteer-form .form-check-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }
    
    .rtl-volunteer-form .form-check-label {
        padding-right: 1rem;
        color: #495057;
        margin-right: 0.5rem;
        cursor: pointer;
        user-select: none;
    }
    
    .rtl-volunteer-form .form-check {
        padding-right: 1.5rem;
        padding-left: 0;
        margin-bottom: 0.5rem;
    }
    
    .rtl-volunteer-form .btn-primary {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        color: white;
    }
    
    .rtl-volunteer-form .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
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
    
    /* Guest Submit Button Hover Effect */
    .btn-primary:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6) !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .rtl-volunteer-form .container {
            padding: 15px;
        }
        
        .guest-container {
            margin: 10px;
            max-height: 95vh;
        }
        
        .guest-header h2 {
            font-size: 20px;
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

<div class="guest-container">
    <div class="guest-header">
        <h2><i class="fas fa-heart"></i> طلب التطوع</h2>
        <p>انضم إلينا واجعل الفرق في المجتمع</p>
    </div>

<div class="rtl-volunteer-form">
    <div class="container">
        <h2>إضافة طلب تطوع جديد</h2>
    <div class="card-block">
        @if(session('success'))
            <div class="alert alert-success" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); border: 1px solid #10b981; border-radius: 12px; color: #065f46;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2" style="color: #10b981; font-size: 1.2rem;"></i>
                    <strong>{{ session('success') }}</strong>
                </div>
                <div class="mt-2" style="font-size: 0.9rem; opacity: 0.8;">
                    <i class="fas fa-info-circle"></i> سيتم مراجعة طلبك خلال 3-5 أيام عمل وسنتواصل معك عبر البريد الإلكتروني المقدم.
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%); border: 1px solid #ef4444; border-radius: 12px; color: #7f1d1d;">
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
                    <input type="tel" name="phone" class="form-control" placeholder="أدخل رقم الجوال" required>
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
                        <input class="form-check-input" type="radio" name="gender" id="male" value="male" style="margin-left: 8px;">
                        <label class="form-check-label" for="male" style="margin-right: 20px;">ذكر</label>
                    </div>
                    <div class="form-check form-check-inline" style="margin-left: 15px;">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="female" style="margin-left: 8px;">
                        <label class="form-check-label" for="female" style="margin-right: 20px;">أنثى</label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">الحالة الاجتماعية</label>
                <div class="col-sm-10 pt-2">
                    <div class="form-check form-check-inline" style="margin-left: 15px;">
                        <input class="form-check-input" type="radio" name="social_status" value="single" id="social_single_guest" style="margin-left: 8px;">
                        <label class="form-check-label" for="social_single_guest" style="margin-right: 20px;">أعزب/عزباء</label>
                    </div>
                    <div class="form-check form-check-inline" style="margin-left: 15px;">
                        <input class="form-check-input" type="radio" name="social_status" value="married" id="social_married_guest" style="margin-left: 8px;">
                        <label class="form-check-label" for="social_married_guest" style="margin-right: 20px;">متزوج/متزوجة</label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">العنوان</label>
                <div class="col-sm-10">
                    <textarea name="address" class="form-control" rows="2" placeholder="أدخل عنوانك الكامل"></textarea>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">المدينة</label>
                <div class="col-sm-10">
                    <input type="text" name="city" class="form-control" placeholder="أدخل المدينة">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">الدولة</label>
                <div class="col-sm-10">
                    <input type="text" name="country" class="form-control" placeholder="أدخل الدولة">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">المستوى التعليمي</label>
                <div class="col-sm-10">
                    <select name="education_level" class="form-control">
                        <option value="">اختر المستوى التعليمي</option>
                        <option value="high_school">ثانوية عامة</option>
                        <option value="diploma">دبلوم</option>
                        <option value="bachelor">بكالوريوس</option>
                        <option value="master">ماجستير</option>
                        <option value="phd">دكتوراه</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">مجال الدراسة</label>
                <div class="col-sm-10">
                    <input type="text" name="field_of_study" class="form-control" placeholder="أدخل مجال دراستك">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">المهنة</label>
                <div class="col-sm-10">
                    <input type="text" name="occupation" class="form-control" placeholder="أدخل مهنتك الحالية">
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">المهارات</label>
                <div class="col-sm-10">
                    <div class="row">
                        @if(isset($skills) && $skills->count() > 0)
                            @foreach($skills as $skill)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="form-check" style="padding: 8px; border: 1px solid #e3e6f0; border-radius: 6px; background: #f8f9fa;">
                                        <input class="form-check-input" type="checkbox" name="skills[]" value="{{ $skill->name }}" id="skill_{{ $skill->id }}" style="margin-top: 4px;">
                                        <label class="form-check-label" for="skill_{{ $skill->id }}" style="font-weight: 500; margin-right: 8px;">
                                            {{ $skill->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> لا توجد مهارات محددة مسبقاً. يمكنك إضافة مهاراتك في حقل الدوافع والخبرات.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">اللغات ومستوى الإتقان</label>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="arabic" class="form-label">العربية</label>
                            <select name="languages[العربية]" class="form-control" id="arabic">
                                <option value="">اختر المستوى</option>
                                <option value="لغة أم">لغة أم</option>
                                <option value="ممتاز">ممتاز</option>
                                <option value="جيد جداً">جيد جداً</option>
                                <option value="جيد">جيد</option>
                                <option value="مبتدئ">مبتدئ</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="english" class="form-label">الإنجليزية</label>
                            <select name="languages[الإنجليزية]" class="form-control" id="english">
                                <option value="">اختر المستوى</option>
                                <option value="لغة أم">لغة أم</option>
                                <option value="ممتاز">ممتاز</option>
                                <option value="جيد جداً">جيد جداً</option>
                                <option value="جيد">جيد</option>
                                <option value="مبتدئ">مبتدئ</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="french" class="form-label">الفرنسية</label>
                            <select name="languages[الفرنسية]" class="form-control" id="french">
                                <option value="">اختر المستوى</option>
                                <option value="لغة أم">لغة أم</option>
                                <option value="ممتاز">ممتاز</option>
                                <option value="جيد جداً">جيد جداً</option>
                                <option value="جيد">جيد</option>
                                <option value="مبتدئ">مبتدئ</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="german" class="form-label">الألمانية</label>
                            <select name="languages[الألمانية]" class="form-control" id="german">
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
                <label class="col-sm-2 col-form-label">الدوافع للتطوع</label>
                <div class="col-sm-10">
                    <textarea name="motivation" class="form-control" rows="4" placeholder="لماذا تريد أن تصبح متطوعاً؟ ما هي دوافعك؟"></textarea>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">الخبرات السابقة</label>
                <div class="col-sm-10">
                    <textarea name="previous_experience" class="form-control" rows="4" placeholder="أذكر خبراتك السابقة في التطوع أو العمل المجتمعي"></textarea>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">المجال المفضل للتطوع</label>
                <div class="col-sm-10">
                    <input type="text" name="preferred_area" class="form-control" placeholder="مثل: التعليم، الصحة، البيئة، إلخ">
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">التوفر الزمني</label>
                <div class="col-sm-10">
                    <textarea name="availability" class="form-control" rows="3" placeholder="متى تكون متاحاً للتطوع؟ (الأيام، الأوقات، عدد الساعات)"></textarea>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">هل لديك خبرة تطوعية سابقة؟</label>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="has_previous_volunteering" id="has_exp_yes" value="1">
                        <label class="form-check-label" for="has_exp_yes">نعم</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="has_previous_volunteering" id="has_exp_no" value="0">
                        <label class="form-check-label" for="has_exp_no">لا</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">نوع المنظمة المفضل</label>
                <div class="col-sm-10">
                    <select name="preferred_organization_type" class="form-control">
                        <option value="">اختر نوع المنظمة</option>
                        <option value="جمعيات خيرية">جمعيات خيرية</option>
                        <option value="منظمات غير ربحية">منظمات غير ربحية</option>
                        <option value="مؤسسات تعليمية">مؤسسات تعليمية</option>
                        <option value="مؤسسات صحية">مؤسسات صحية</option>
                        <option value="منظمات بيئية">منظمات بيئية</option>
                        <option value="منظمات رياضية">منظمات رياضية</option>
                        <option value="منظمات ثقافية">منظمات ثقافية</option>
                    </select>
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
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 15px 40px; font-size: 16px; font-weight: 600; border-radius: 25px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); transition: all 0.3s ease;">
                        <i class="fas fa-paper-plane"></i> إرسال طلب التطوع
                    </button>
                </div>
            </div>
        </form>
    </div>
    </div>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // إضافة تأثيرات بصرية للضيوف
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
            submitBtn.disabled = true;
        });
    });
</script>
</body>
</html>
