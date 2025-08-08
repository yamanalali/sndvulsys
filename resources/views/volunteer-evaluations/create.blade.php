<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقييم سريع - {{ $volunteerRequest->full_name ?? 'متطوع' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .quick-eval { cursor: pointer; transition: all 0.3s; }
        .quick-eval:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .quick-eval.selected { border: 3px solid #007bff; background-color: #f8f9fa; }
        .score-display { font-size: 2rem; font-weight: bold; color: #007bff; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">
                                <i class="fas fa-bolt text-warning"></i> 
                                تقييم سريع - {{ $volunteerRequest->full_name ?? 'متطوع' }}
                            </h4>
                            <p class="card-subtitle mb-0">تقييم سريع في أقل من دقيقة</p>
                        </div>
                        <div>
                            <a href="{{ route('volunteer-requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> العودة
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

                        <!-- معلومات سريعة -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="fas fa-user"></i> معلومات المتطوع</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>الاسم:</strong> {{ $volunteerRequest->full_name ?? 'غير محدد' }}<br>
                                                <strong>التخصص:</strong> {{ $volunteerRequest->field_of_study ?? 'غير محدد' }}<br>
                                                <strong>المهارات:</strong> {{ Str::limit($volunteerRequest->skills ?? 'غير محدد', 50) }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>الدافع:</strong> {{ Str::limit($volunteerRequest->motivation ?? 'غير محدد', 50) }}<br>
                                                <strong>التوفر:</strong> {{ $volunteerRequest->availability ?? 'غير محدد' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-primary text-white text-center">
                                    <div class="card-body">
                                        <div class="score-display" id="totalScore">0</div>
                                        <div>النتيجة الإجمالية</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('volunteer-evaluations.store', $volunteerRequest->id) }}" id="evaluationForm">
                            @csrf
                            
                            <!-- التقييم السريع -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5><i class="fas fa-star"></i> التقييم السريع</h5>
                                    <p class="text-muted">اختر مستوى التقييم العام للمتطوع</p>
                                    
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <div class="card quick-eval text-center" data-score="90" data-recommendation="strong_approve">
                                                <div class="card-body">
                                                    <i class="fas fa-star text-warning" style="font-size: 2rem;"></i>
                                                    <h6 class="mt-2">ممتاز</h6>
                                                    <small class="text-muted">90-100</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card quick-eval text-center" data-score="75" data-recommendation="approve">
                                                <div class="card-body">
                                                    <i class="fas fa-thumbs-up text-success" style="font-size: 2rem;"></i>
                                                    <h6 class="mt-2">جيد</h6>
                                                    <small class="text-muted">70-89</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card quick-eval text-center" data-score="60" data-recommendation="conditional">
                                                <div class="card-body">
                                                    <i class="fas fa-question text-warning" style="font-size: 2rem;"></i>
                                                    <h6 class="mt-2">مقبول</h6>
                                                    <small class="text-muted">50-69</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card quick-eval text-center" data-score="40" data-recommendation="reject">
                                                <div class="card-body">
                                                    <i class="fas fa-times text-danger" style="font-size: 2rem;"></i>
                                                    <h6 class="mt-2">ضعيف</h6>
                                                    <small class="text-muted">0-49</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- التقييم التفصيلي (اختياري) -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">
                                                <i class="fas fa-cog"></i> 
                                                التقييم التفصيلي (اختياري)
                                                <button type="button" class="btn btn-sm btn-outline-primary float-start" onclick="toggleDetailed()">
                                                    <i class="fas fa-chevron-down"></i> تفاصيل أكثر
                                                </button>
                                            </h6>
                                        </div>
                                        <div class="card-body" id="detailedSection" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>المقابلة الشخصية</label>
                                                        <input type="range" class="form-range" name="interview_score" min="0" max="100" value="75" oninput="updateScore(this)">
                                                        <div class="d-flex justify-content-between">
                                                            <small>0</small>
                                                            <small id="interview_score_display">75</small>
                                                            <small>100</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>المهارات</label>
                                                        <input type="range" class="form-range" name="skills_assessment_score" min="0" max="100" value="75" oninput="updateScore(this)">
                                                        <div class="d-flex justify-content-between">
                                                            <small>0</small>
                                                            <small id="skills_assessment_score_display">75</small>
                                                            <small>100</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>الدافع</label>
                                                        <input type="range" class="form-range" name="motivation_score" min="0" max="100" value="75" oninput="updateScore(this)">
                                                        <div class="d-flex justify-content-between">
                                                            <small>0</small>
                                                            <small id="motivation_score_display">75</small>
                                                            <small>100</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>التوفر</label>
                                                        <input type="range" class="form-range" name="availability_score" min="0" max="100" value="75" oninput="updateScore(this)">
                                                        <div class="d-flex justify-content-between">
                                                            <small>0</small>
                                                            <small id="availability_score_display">75</small>
                                                            <small>100</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- الحقول المخفية -->
                            <input type="hidden" name="evaluation_date" value="{{ date('Y-m-d') }}">
                            <input type="hidden" name="recommendation" id="recommendation" value="approve">
                            <input type="hidden" name="interview_score" value="75">
                            <input type="hidden" name="skills_assessment_score" value="75">
                            <input type="hidden" name="motivation_score" value="75">
                            <input type="hidden" name="availability_score" value="75">
                            <input type="hidden" name="experience_score" value="75">
                            <input type="hidden" name="communication_score" value="75">
                            <input type="hidden" name="teamwork_score" value="75">
                            <input type="hidden" name="reliability_score" value="75">
                            <input type="hidden" name="adaptability_score" value="75">
                            <input type="hidden" name="leadership_score" value="75">
                            <input type="hidden" name="technical_skills_score" value="75">
                            <input type="hidden" name="cultural_fit_score" value="75">
                            <input type="hidden" name="commitment_score" value="75">

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="notes">ملاحظات سريعة (اختياري)</label>
                                        <textarea name="notes" class="form-control" rows="3" placeholder="أضف ملاحظات سريعة إذا لزم الأمر..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-check"></i> حفظ التقييم السريع
                                    </button>
                                    <a href="{{ route('volunteer-requests.index') }}" class="btn btn-secondary btn-lg">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // التقييم السريع
        document.querySelectorAll('.quick-eval').forEach(card => {
            card.addEventListener('click', function() {
                // إزالة التحديد من جميع البطاقات
                document.querySelectorAll('.quick-eval').forEach(c => c.classList.remove('selected'));
                // تحديد البطاقة المختارة
                this.classList.add('selected');
                
                // تحديث النتيجة والتوصية
                const score = this.dataset.score;
                const recommendation = this.dataset.recommendation;
                
                document.getElementById('totalScore').textContent = score;
                document.getElementById('recommendation').value = recommendation;
                
                // تحديث جميع النقاط
                updateAllScores(score);
            });
        });

        function updateAllScores(score) {
            // تحديث الحقول المخفية
            const hiddenInputs = document.querySelectorAll('input[type="hidden"][name*="_score"]');
            hiddenInputs.forEach(input => {
                input.value = score;
            });
            
            // تحديث الحقول المرئية
            const scoreInputs = document.querySelectorAll('input[type="range"]');
            scoreInputs.forEach(input => {
                input.value = score;
                const displayId = input.name + '_display';
                const display = document.getElementById(displayId);
                if (display) display.textContent = score;
            });
        }

        function updateScore(input) {
            const displayId = input.name + '_display';
            const display = document.getElementById(displayId);
            if (display) display.textContent = input.value;
            
            // حساب المتوسط
            const scoreInputs = document.querySelectorAll('input[type="range"]');
            let total = 0;
            scoreInputs.forEach(input => {
                total += parseInt(input.value);
            });
            const average = Math.round(total / scoreInputs.length);
            document.getElementById('totalScore').textContent = average;
        }

        function toggleDetailed() {
            const section = document.getElementById('detailedSection');
            const button = event.target.closest('button');
            const icon = button.querySelector('i');
            
            if (section.style.display === 'none') {
                section.style.display = 'block';
                icon.className = 'fas fa-chevron-up';
                button.innerHTML = '<i class="fas fa-chevron-up"></i> إخفاء التفاصيل';
            } else {
                section.style.display = 'none';
                icon.className = 'fas fa-chevron-down';
                button.innerHTML = '<i class="fas fa-chevron-down"></i> تفاصيل أكثر';
            }
        }

        // تحديد "جيد" افتراضياً
        document.querySelector('[data-score="75"]').click();
    </script>
</body>
</html> 