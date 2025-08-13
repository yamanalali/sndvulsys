<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل التقييم - {{ $evaluation->volunteerRequest->full_name ?? 'متطوع' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #feca57 100%);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
            --shadow-lg: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --border-radius: 16px;
        }

        body { 
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .main-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: var(--border-radius);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-lg);
            margin-bottom: 20px;
        }

        .question-card {
            background: var(--glass-bg);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: var(--border-radius);
            border: 1px solid var(--glass-border);
            margin-bottom: 25px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .question-card:nth-child(1) { animation-delay: 0.1s; }
        .question-card:nth-child(2) { animation-delay: 0.2s; }
        .question-card:nth-child(3) { animation-delay: 0.3s; }
        .question-card:nth-child(4) { animation-delay: 0.4s; }
        .question-card:nth-child(5) { animation-delay: 0.5s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .question-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .question-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .question-card:hover::before {
            opacity: 1;
        }

        .question-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .question-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 49%, rgba(255, 255, 255, 0.1) 50%, transparent 51%);
            pointer-events: none;
        }

        .question-header h6 {
            margin: 0;
            font-weight: 600;
            color: #2d3748;
            font-size: 1.1rem;
        }

        .answer-key {
            background: linear-gradient(135deg, rgba(79, 172, 254, 0.1) 0%, rgba(0, 242, 254, 0.1) 100%);
            border-radius: 12px;
            padding: 20px;
            margin: 20px;
            border-left: 4px solid #4facfe;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .answer-key strong {
            color: #2d3748;
            font-weight: 600;
        }

        .answer-key ul {
            margin: 12px 0 0 0;
            padding-right: 20px;
        }

        .answer-key li {
            color: #4a5568;
            margin-bottom: 8px;
            line-height: 1.6;
            position: relative;
        }

        .answer-key li::marker {
            color: #4facfe;
        }

        .score-selector {
            padding: 20px;
        }

        .score-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
            background: white;
            margin: 5px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .score-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-gradient);
            border-radius: 50%;
            transform: scale(0);
            transition: transform 0.3s ease;
            z-index: 1;
        }

        .score-btn span {
            position: relative;
            z-index: 2;
            transition: color 0.3s ease;
        }

        .score-btn:hover {
            transform: translateY(-3px) rotate(5deg);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            border-color: #667eea;
        }

        .score-btn:hover::before {
            transform: scale(1);
        }

        .score-btn:hover span {
            color: white;
        }

        .score-btn.selected {
            background: var(--primary-gradient);
            border-color: #667eea;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .score-btn.selected::before {
            transform: scale(1);
        }

        .score-btn.selected span {
            color: white;
        }

        .total-score-card {
            position: sticky;
            top: 20px;
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            border-radius: var(--border-radius);
            color: white;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0;
            overflow: hidden;
        }

        .total-score-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--success-gradient);
        }

        .score-display {
            font-size: 3rem;
            font-weight: 700;
            background: var(--success-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(79, 172, 254, 0.5);
        }

        .progress-indicator {
            height: 8px;
            background: var(--success-gradient);
            border-radius: 4px;
            transition: width 0.6s ease;
            box-shadow: 0 0 10px rgba(79, 172, 254, 0.5);
        }

        .info-badge {
            background: linear-gradient(135deg, rgba(79, 172, 254, 0.2) 0%, rgba(0, 242, 254, 0.2) 100%);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.9rem;
            border: 1px solid rgba(79, 172, 254, 0.3);
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .info-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);
        }

        .btn-success {
            background: var(--success-gradient);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.6);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }

        .alert {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 12px;
            border-left: 4px solid;
        }

        .alert-success { 
            background: rgba(72, 187, 120, 0.1);
            border-left-color: #48bb78;
            color: #2f855a;
        }

        .alert-warning { 
            background: rgba(237, 137, 54, 0.1);
            border-left-color: #ed8936;
            color: #c05621;
        }

        .alert-danger { 
            background: rgba(245, 101, 101, 0.1);
            border-left-color: #f56565;
            color: #c53030;
        }

        .alert-secondary { 
            background: rgba(160, 174, 192, 0.1);
            border-left-color: #a0aec0;
            color: #4a5568;
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid rgba(226, 232, 240, 0.6);
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        @media (max-width: 768px) {
            .score-btn {
                width: 40px;
                height: 40px;
                font-size: 0.9rem;
                margin: 3px;
            }
            
            .score-display {
                font-size: 2rem;
            }
            
            .question-header h6 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <!-- العمود الرئيسي للأسئلة -->
            <div class="col-lg-8">
                <div class="main-card">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-radius: 16px 16px 0 0; padding: 25px;">
                        <div>
                            <h4 class="card-title mb-2">
                                <i class="fas fa-edit text-primary"></i> 
                                تعديل التقييم - {{ $evaluation->volunteerRequest->full_name ?? 'متطوع' }}
                            </h4>
                            <p class="card-subtitle mb-0 text-muted">نظام التقييم المحدث بالأسئلة الخمسة</p>
                        </div>
                        <div>
                            <a href="{{ route('volunteer-evaluations.show', $evaluation->id) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> عرض التقييم
                            </a>
                            <a href="{{ route('volunteer-requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> العودة
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body" style="padding: 30px;">
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
                        <div class="info-badge mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                    <strong><i class="fas fa-user"></i> الاسم:</strong> {{ $evaluation->volunteerRequest->full_name ?? 'غير محدد' }}<br>
                                    <strong><i class="fas fa-envelope"></i> البريد:</strong> {{ $evaluation->volunteerRequest->email ?? 'غير محدد' }}
                                            </div>
                                            <div class="col-md-6">
                                    <strong><i class="fas fa-graduation-cap"></i> التخصص:</strong> {{ $evaluation->volunteerRequest->field_of_study ?? 'غير محدد' }}<br>
                                    <strong><i class="fas fa-clock"></i> التوفر:</strong> {{ $evaluation->volunteerRequest->availability ?? 'غير محدد' }}
                                </div>
                            </div>
                        </div>

                        @php
                            // Normalize legacy scores (e.g., 60 -> 6) to the new 1-10 scale
                            $normalize = function($score) {
                                $score = (int) ($score ?? 0);
                                if ($score > 10) {
                                    $score = (int) round($score / 10);
                                }
                                if ($score < 0) { $score = 0; }
                                if ($score > 10) { $score = 10; }
                                return $score;
                            };

                            $interviewN     = $normalize($evaluation->interview_score ?? 0);
                            $skillsN        = $normalize($evaluation->skills_assessment_score ?? 0);
                            $motivationN    = $normalize($evaluation->motivation_score ?? 0);
                            $availabilityN  = $normalize($evaluation->availability_score ?? 0);
                            $teamworkN      = $normalize($evaluation->teamwork_score ?? 0);
                        @endphp

                        <form method="POST" action="{{ route('volunteer-evaluations.update', $evaluation->id) }}" id="evaluationForm">
                            @csrf
                            @method('PATCH')
                            
                            <!-- أسئلة التقييم التفصيلية -->
                            
                            <!-- السؤال الأول: التعريف الشخصي -->
                            <div class="question-card">
                                <div class="question-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user"></i> 
                                        السؤال الأول: "حدثني عن نفسك وخلفيتك التعليمية والمهنية؟"
                                    </h6>
                                </div>
                                                <div class="card-body">
                                    <div class="answer-key">
                                        <strong><i class="fas fa-key"></i> النقاط المطلوب تقييمها:</strong>
                                        <ul class="mb-0">
                                            <li>وضوح في التعبير والتواصل</li>
                                            <li>ترتيب الأفكار والمعلومات</li>
                                            <li>الثقة بالنفس أثناء الحديث</li>
                                            <li>صلة الخلفية التعليمية/المهنية بالعمل التطوعي</li>
                                        </ul>
                                                </div>
                                    <div class="score-selector">
                                        <label class="form-label"><strong>درجة التقييم (1-10):</strong></label>
                                        <div class="d-flex flex-wrap justify-content-center">
                                            @for($i = 1; $i <= 10; $i++)
                                                <button type="button" class="btn score-btn {{ $interviewN == $i ? 'selected' : '' }}" data-score="{{ $i }}" data-field="interview_score"><span>{{ $i }}</span></button>
                                            @endfor
                                            </div>
                                        <input type="hidden" name="interview_score" id="interview_score" value="{{ $interviewN }}">
                                                </div>
                                            </div>
                                        </div>

                            <!-- السؤال الثاني: الدافع للتطوع -->
                            <div class="question-card">
                                <div class="question-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-heart"></i> 
                                        السؤال الثاني: "لماذا تريد أن تصبح متطوعاً في منظمتنا تحديداً؟"
                                    </h6>
                                        </div>
                                                <div class="card-body">
                                    <div class="answer-key">
                                        <strong><i class="fas fa-key"></i> النقاط المطلوب تقييمها:</strong>
                                        <ul class="mb-0">
                                            <li>معرفة المتطوع برسالة وأهداف المنظمة</li>
                                            <li>صدق الرغبة في المساعدة والعطاء</li>
                                            <li>وضوح الأهداف الشخصية من التطوع</li>
                                            <li>التطابق بين قيم المتطوع وقيم المنظمة</li>
                                        </ul>
                                                </div>
                                    <div class="score-selector">
                                        <label class="form-label"><strong>درجة التقييم (1-10):</strong></label>
                                        <div class="d-flex flex-wrap justify-content-center">
                                            @for($i = 1; $i <= 10; $i++)
                                                <button type="button" class="btn score-btn {{ $skillsN == $i ? 'selected' : '' }}" data-score="{{ $i }}" data-field="skills_assessment_score"><span>{{ $i }}</span></button>
                                            @endfor
                                            </div>
                                        <input type="hidden" name="skills_assessment_score" id="skills_assessment_score" value="{{ $skillsN }}">
                                    </div>
                                </div>
                            </div>

                            <!-- السؤال الثالث: الخبرات والمهارات -->
                            <div class="question-card">
                                <div class="question-header">
                                            <h6 class="mb-0">
                                        <i class="fas fa-tools"></i> 
                                        السؤال الثالث: "ما هي المهارات التي تمتلكها والتي تعتقد أنها ستفيد في العمل التطوعي؟"
                                            </h6>
                                        </div>
                                <div class="card-body">
                                    <div class="answer-key">
                                        <strong><i class="fas fa-key"></i> النقاط المطلوب تقييمها:</strong>
                                        <ul class="mb-0">
                                            <li>وعي المتطوع بمهاراته الشخصية</li>
                                            <li>ربط المهارات بالعمل التطوعي المطلوب</li>
                                            <li>ذكر أمثلة عملية أو خبرات سابقة</li>
                                            <li>الرغبة في تطوير مهارات جديدة</li>
                                        </ul>
                                                        </div>
                                    <div class="score-selector">
                                        <label class="form-label"><strong>درجة التقييم (1-10):</strong></label>
                                        <div class="d-flex flex-wrap justify-content-center">
                                            @for($i = 1; $i <= 10; $i++)
                                                <button type="button" class="btn score-btn {{ $motivationN == $i ? 'selected' : '' }}" data-score="{{ $i }}" data-field="motivation_score"><span>{{ $i }}</span></button>
                                            @endfor
                                                    </div>
                                        <input type="hidden" name="motivation_score" id="motivation_score" value="{{ $motivationN }}">
                                                        </div>
                                                    </div>
                                                </div>

                            <!-- السؤال الرابع: التوفر والالتزام -->
                            <div class="question-card">
                                <div class="question-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-clock"></i> 
                                        السؤال الرابع: "كم من الوقت يمكنك تخصيصه للعمل التطوعي أسبوعياً؟"
                                    </h6>
                                            </div>
                                <div class="card-body">
                                    <div class="answer-key">
                                        <strong><i class="fas fa-key"></i> النقاط المطلوب تقييمها:</strong>
                                        <ul class="mb-0">
                                            <li>واقعية الوقت المقترح مع الالتزامات الأخرى</li>
                                            <li>وضوح الأوقات المتاحة (صباح/مساء/نهاية أسبوع)</li>
                                            <li>المرونة في تعديل الأوقات عند الحاجة</li>
                                            <li>الاستعداد للالتزام طويل المدى</li>
                                        </ul>
                                                        </div>
                                    <div class="score-selector">
                                        <label class="form-label"><strong>درجة التقييم (1-10):</strong></label>
                                        <div class="d-flex flex-wrap justify-content-center">
                                            @for($i = 1; $i <= 10; $i++)
                                                <button type="button" class="btn score-btn {{ $availabilityN == $i ? 'selected' : '' }}" data-score="{{ $i }}" data-field="availability_score"><span>{{ $i }}</span></button>
                                            @endfor
                                                    </div>
                                        <input type="hidden" name="availability_score" id="availability_score" value="{{ $availabilityN }}">
                                                        </div>
                                                    </div>
                                                </div>

                            <!-- السؤال الخامس: التحديات والتعامل معها -->
                            <div class="question-card">
                                <div class="question-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-puzzle-piece"></i> 
                                        السؤال الخامس: "كيف تتعامل مع المواقف الصعبة أو التحديات في العمل؟"
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="answer-key">
                                        <strong><i class="fas fa-key"></i> النقاط المطلوب تقييمها:</strong>
                                        <ul class="mb-0">
                                            <li>نضج في التفكير وحل المشكلات</li>
                                            <li>القدرة على التحكم في الانفعالات</li>
                                            <li>طلب المساعدة عند الحاجة</li>
                                            <li>التعلم من التجارب والأخطاء</li>
                                        </ul>
                                            </div>
                                    <div class="score-selector">
                                        <label class="form-label"><strong>درجة التقييم (1-10):</strong></label>
                                        <div class="d-flex flex-wrap justify-content-center">
                                            @for($i = 1; $i <= 10; $i++)
                                                <button type="button" class="btn score-btn {{ $teamworkN == $i ? 'selected' : '' }}" data-score="{{ $i }}" data-field="teamwork_score"><span>{{ $i }}</span></button>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="teamwork_score" id="teamwork_score" value="{{ $teamworkN }}">
                                    </div>
                                </div>
                            </div>

                            <!-- الحقول المخفية -->
                            <input type="hidden" name="evaluation_date" value="{{ $evaluation->evaluation_date ? $evaluation->evaluation_date->format('Y-m-d') : date('Y-m-d') }}">
                            <input type="hidden" name="recommendation" id="recommendation" value="{{ $evaluation->recommendation ?? '' }}">
                            <input type="hidden" name="experience_score" value="{{ $evaluation->experience_score ?? 0 }}">
                            <input type="hidden" name="communication_score" value="{{ $evaluation->communication_score ?? 0 }}">
                            <input type="hidden" name="reliability_score" value="{{ $evaluation->reliability_score ?? 0 }}">
                            <input type="hidden" name="adaptability_score" value="{{ $evaluation->adaptability_score ?? 0 }}">
                            <input type="hidden" name="leadership_score" value="{{ $evaluation->leadership_score ?? 0 }}">
                            <input type="hidden" name="technical_skills_score" value="{{ $evaluation->technical_skills_score ?? 0 }}">
                            <input type="hidden" name="cultural_fit_score" value="{{ $evaluation->cultural_fit_score ?? 0 }}">
                            <input type="hidden" name="commitment_score" value="{{ $evaluation->commitment_score ?? 0 }}">

                            <!-- الملاحظات -->
                            <div class="question-card">
                                <div class="question-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-sticky-note"></i> 
                                        ملاحظات إضافية (اختياري)
                                    </h6>
                                    </div>
                                <div class="card-body">
                                    <textarea name="notes" class="form-control" rows="4" placeholder="أضف أي ملاحظات إضافية حول التقييم...">{{ $evaluation->notes ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success btn-lg me-3">
                                        <i class="fas fa-save"></i> حفظ التعديلات
                                    </button>
                                    <a href="{{ route('volunteer-evaluations.show', $evaluation->id) }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- العمود الجانبي للنتيجة الإجمالية -->
            <div class="col-lg-4">
                <div class="total-score-card text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-trophy"></i> النتيجة الإجمالية</h5>
                        <div class="score-display" id="totalScore">{{ $evaluation->overall_score ?? 0 }}</div>
                        <p class="mb-3">من 50 درجة</p>
                        
                        <!-- شريط التقدم -->
                        <div class="bg-light rounded" style="height: 12px;">
                            <div class="progress-indicator" id="progressBar" style="width: {{ (($evaluation->overall_score ?? 0) / 50) * 100 }}%;"></div>
                        </div>
                        <small class="d-block mt-2" id="progressText">
                            @php
                                $completedFields = 0;
                                if($evaluation->interview_score > 0) $completedFields++;
                                if($evaluation->skills_assessment_score > 0) $completedFields++;
                                if($evaluation->motivation_score > 0) $completedFields++;
                                if($evaluation->availability_score > 0) $completedFields++;
                                if($evaluation->teamwork_score > 0) $completedFields++;
                            @endphp
                            {{ $completedFields < 5 ? "تم تقييم $completedFields من 5 أسئلة" : "تم إكمال التقييم" }}
                        </small>
                        
                        <!-- معايير التقييم -->
                        <div class="mt-4">
                            <h6><i class="fas fa-info-circle"></i> معايير التقييم</h6>
                            <div class="text-start" style="font-size: 0.85rem;">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>🟢 قبول مباشر:</span>
                                        <span><strong>+37 درجة</strong></span>
                                    </div>
                                    <small class="text-muted">(أكثر من 75%)</small>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>🟡 كورسات تدريبية:</span>
                                        <span><strong>25-37 درجة</strong></span>
                                    </div>
                                    <small class="text-muted">(50% - 75%)</small>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>🔴 رفض:</span>
                                        <span><strong>أقل من 25</strong></span>
                                    </div>
                                    <small class="text-muted">(أقل من 50%)</small>
                                </div>
                            </div>
                        </div>

                        <!-- تفاصيل النقاط -->
                        <div class="mt-4">
                            <h6><i class="fas fa-list"></i> تفاصيل النقاط</h6>
                            <div class="text-start" style="font-size: 0.9rem;">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>التعريف الشخصي:</span>
                                    <span id="interview_display">{{ $evaluation->interview_score ?? 0 }}/10</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>الدافع للتطوع:</span>
                                    <span id="skills_display">{{ $evaluation->skills_assessment_score ?? 0 }}/10</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>المهارات:</span>
                                    <span id="motivation_display">{{ $evaluation->motivation_score ?? 0 }}/10</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>التوفر الزمني:</span>
                                    <span id="availability_display">{{ $evaluation->availability_score ?? 0 }}/10</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>التعامل مع التحديات:</span>
                                    <span id="teamwork_display">{{ $evaluation->teamwork_score ?? 0 }}/10</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- التوصية -->
                        <div class="mt-4">
                            <h6><i class="fas fa-recommendation"></i> التوصية النهائية</h6>
                            <div id="recommendationDisplay" class="alert alert-secondary">
                                {{ $evaluation->getRecommendationText() ?? 'في انتظار التقييم' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // نقاط التقييم
        document.querySelectorAll('.score-btn').forEach(button => {
            button.addEventListener('click', function() {
                const score = parseInt(this.dataset.score);
                const field = this.dataset.field;
                
                // إزالة الاختيار من جميع الأزرار في نفس المجموعة
                const fieldButtons = document.querySelectorAll(`[data-field="${field}"]`);
                fieldButtons.forEach(btn => btn.classList.remove('selected'));
                
                // إضافة الاختيار للزر المنقور
                this.classList.add('selected');
                
                // تحديث الحقل المخفي
                document.getElementById(field).value = score;
                
                // تحديث العرض الجانبي
                updateSidebarDisplay(field, score);
                
                // تحديث النتيجة الإجمالية
                updateTotalScore();
                
                // تحديث العرض الجانبي
                updateSidebarDisplay(field, score);
            });
        });

        function updateTotalScore() {
            const fields = ['interview_score', 'skills_assessment_score', 'motivation_score', 'availability_score', 'teamwork_score'];
            let total = 0;
            let completedFields = 0;
            
            fields.forEach(field => {
                const value = parseInt(document.getElementById(field).value) || 0;
                if (value > 0) {
                    total += value;
                    completedFields++;
                }
            });
            
            // تحديث العرض
            document.getElementById('totalScore').textContent = total;
            
            // تحديث شريط التقدم (من 50 درجة كحد أقصى)
            const percentage = (total / 50) * 100;
            document.getElementById('progressBar').style.width = percentage + '%';
            
            // تحديث نص التقدم
            const progressText = document.getElementById('progressText');
            if (completedFields === 0) {
                progressText.textContent = 'لم يتم التقييم بعد';
            } else if (completedFields < 5) {
                progressText.textContent = `تم تقييم ${completedFields} من 5 أسئلة`;
            } else {
                progressText.textContent = 'تم إكمال التقييم';
            }
            
            // تحديث التوصية
            updateRecommendation(total, completedFields);
        }

        function updateSidebarDisplay(field, score) {
            const displayMap = {
                'interview_score': 'interview_display',
                'skills_assessment_score': 'skills_display', 
                'motivation_score': 'motivation_display',
                'availability_score': 'availability_display',
                'teamwork_score': 'teamwork_display'
            };
            
            const displayElement = document.getElementById(displayMap[field]);
            if (displayElement) {
                displayElement.textContent = score + '/10';
            }
        }

        function updateRecommendation(total, completedFields) {
            const recommendationDiv = document.getElementById('recommendationDisplay');
            const recommendationInput = document.getElementById('recommendation');
            
            if (completedFields < 5) {
                recommendationDiv.innerHTML = '<i class="fas fa-hourglass-half"></i> في انتظار إكمال التقييم';
                recommendationDiv.className = 'alert alert-secondary';
                recommendationInput.value = '';
                return;
            }
            
            let recommendation = '';
            let className = '';
            let icon = '';
            
            if (total > 37) { // أكثر من 75% من 50 (37 درجة)
                recommendation = 'مقبول - مرشح ممتاز جاهز للعمل';
                className = 'alert alert-success';
                icon = '<i class="fas fa-check-circle"></i>';
                recommendationInput.value = 'accepted';
            } else if (total >= 25) { // بين 50% و 75% من 50 (25-37 درجة)
                recommendation = 'كورسات تدريبية - يحتاج تطوير مهارات';
                className = 'alert alert-warning';
                icon = '<i class="fas fa-graduation-cap"></i>';
                recommendationInput.value = 'training_required';
            } else {
                recommendation = 'مرفوض - لا يلبي الحد الأدنى من المتطلبات';
                className = 'alert alert-danger';
                icon = '<i class="fas fa-times-circle"></i>';
                recommendationInput.value = 'rejected';
            }
            
            recommendationDiv.innerHTML = icon + ' ' + recommendation;
            recommendationDiv.className = className;
        }

        // التحقق من صحة النموذج قبل الإرسال
        document.getElementById('evaluationForm').addEventListener('submit', function(e) {
            const fields = ['interview_score', 'skills_assessment_score', 'motivation_score', 'availability_score', 'teamwork_score'];
            let incompleteFields = [];
            
            fields.forEach(field => {
                const value = parseInt(document.getElementById(field).value) || 0;
                if (value === 0) {
                    incompleteFields.push(field);
                }
            });
            
            if (incompleteFields.length > 0) {
                e.preventDefault();
                alert('يرجى إكمال تقييم جميع الأسئلة قبل الحفظ');
                return false;
            }
        });

        // تهيئة النتيجة عند تحميل الصفحة
        updateTotalScore();
    </script>
</body>
</html> 