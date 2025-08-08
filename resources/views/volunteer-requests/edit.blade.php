<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل طلب التطوع - {{ $request->full_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { font-weight: bold; margin-bottom: 0.5rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">تعديل طلب التطوع</h4>
                        <p class="card-subtitle mb-0">تحديث معلومات طلب التطوع</p>
                    </div>
                    <div>
                        <a href="{{ route('volunteer-requests.show', $request->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> العودة للتفاصيل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('volunteer-requests.update', $request->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- المعلومات الشخصية -->
                            <div class="col-md-6">
                                <h5>المعلومات الشخصية</h5>
                                
                                <div class="form-group">
                                    <label>الاسم الكامل *</label>
                                    <input type="text" name="full_name" class="form-control" value="{{ $request->full_name }}" required>
                                </div>

                                <div class="form-group">
                                    <label>البريد الإلكتروني *</label>
                                    <input type="email" name="email" class="form-control" value="{{ $request->email }}" required>
                                </div>

                                <div class="form-group">
                                    <label>رقم الهاتف *</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $request->phone }}" required>
                                </div>

                                <div class="form-group">
                                    <label>رقم الهوية الوطنية</label>
                                    <input type="text" name="national_id" class="form-control" value="{{ $request->national_id }}">
                                </div>

                                <div class="form-group">
                                    <label>تاريخ الميلاد</label>
                                    <input type="date" name="birth_date" class="form-control" value="{{ $request->birth_date }}">
                                </div>

                                <div class="form-group">
                                    <label>الجنس</label>
                                    <select name="gender" class="form-control">
                                        <option value="">اختر الجنس</option>
                                        <option value="male" {{ $request->gender === 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ $request->gender === 'female' ? 'selected' : '' }}>أنثى</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>الحالة الاجتماعية</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="social_status" value="single" {{ $request->social_status === 'single' ? 'checked' : '' }}>
                                        <label class="form-check-label">عازب/عازبة</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="social_status" value="married" {{ $request->social_status === 'married' ? 'checked' : '' }}>
                                        <label class="form-check-label">متزوج/متزوجة</label>
                                    </div>
                                </div>
                            </div>

                            <!-- العنوان والمعلومات التعليمية -->
                            <div class="col-md-6">
                                <h5>العنوان والمعلومات التعليمية</h5>
                                
                                <div class="form-group">
                                    <label>العنوان</label>
                                    <input type="text" name="address" class="form-control" value="{{ $request->address }}">
                                </div>

                                <div class="form-group">
                                    <label>الدولة</label>
                                    <input type="text" name="country" class="form-control" value="{{ $request->country }}">
                                </div>

                                <div class="form-group">
                                    <label>المدينة</label>
                                    <input type="text" name="city" class="form-control" value="{{ $request->city }}">
                                </div>

                                <div class="form-group">
                                    <label>المستوى التعليمي</label>
                                    <input type="text" name="education_level" class="form-control" value="{{ $request->education_level }}">
                                </div>

                                <div class="form-group">
                                    <label>التخصص الدراسي</label>
                                    <input type="text" name="field_of_study" class="form-control" value="{{ $request->field_of_study }}">
                                </div>

                                <div class="form-group">
                                    <label>المهنة</label>
                                    <input type="text" name="occupation" class="form-control" value="{{ $request->occupation }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- المهارات واللغات -->
                            <div class="col-md-6">
                                <h5>المهارات واللغات</h5>
                                
                                <div class="form-group">
                                    <label>المهارات</label>
                                    <textarea name="skills" class="form-control" rows="3">{{ $request->skills }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>اللغات</label>
                                    <textarea name="languages" class="form-control" rows="3">{{ $request->languages }}</textarea>
                                </div>
                            </div>

                            <!-- معلومات التطوع -->
                            <div class="col-md-6">
                                <h5>معلومات التطوع</h5>
                                
                                <div class="form-group">
                                    <label>المجال المفضل</label>
                                    <input type="text" name="preferred_area" class="form-control" value="{{ $request->preferred_area }}">
                                </div>

                                <div class="form-group">
                                    <label>التوفر</label>
                                    <input type="text" name="availability" class="form-control" value="{{ $request->availability }}">
                                </div>

                                <div class="form-group">
                                    <label>نوع المنظمة المفضلة</label>
                                    <input type="text" name="preferred_organization_type" class="form-control" value="{{ $request->preferred_organization_type }}">
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_previous_volunteering" value="1" {{ $request->has_previous_volunteering ? 'checked' : '' }}>
                                        <label class="form-check-label">هل سبق له التطوع؟</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>سبب التقديم والخبرة</h5>
                                
                                <div class="form-group">
                                    <label>سبب التقديم</label>
                                    <textarea name="motivation" class="form-control" rows="4">{{ $request->motivation }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>الخبرة السابقة</label>
                                    <textarea name="previous_experience" class="form-control" rows="4">{{ $request->previous_experience }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>السيرة الذاتية</h5>
                                
                                @if($request->cv)
                                    <div class="alert alert-info">
                                        <strong>السيرة الذاتية الحالية:</strong> 
                                        <a href="{{ asset('storage/' . $request->cv) }}" target="_blank">عرض الملف</a>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label>رفع سيرة ذاتية جديدة (PDF)</label>
                                    <input type="file" name="cv" class="form-control" accept=".pdf">
                                    <small class="form-text text-muted">حجم الملف يجب أن يكون أقل من 2 ميجابايت</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ التغييرات
                                </button>
                                <a href="{{ route('volunteer-requests.show', $request->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-group {
    margin-bottom: 1rem;
}
.form-group label {
    font-weight: bold;
    margin-bottom: 0.5rem;
}
.form-check {
    margin-bottom: 0.5rem;
}
</style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 