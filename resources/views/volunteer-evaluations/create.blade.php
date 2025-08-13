<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقييم تفصيلي - {{ $volunteerRequest->full_name ?? 'متطوع' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --success-gradient: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            --danger-gradient: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%);
            --warning-gradient: linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%);
            --info-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            --light-bg: #fafbfc;
            --card-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            --card-shadow-hover: 0 15px 45px rgba(31, 38, 135, 0.25);
            --border-radius: 20px;
            --border-radius-small: 12px;
        }
        
        body { 
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .container-fluid {
            max-width: 1400px;
        }
        
        .main-card { 
            border-radius: var(--border-radius); 
            box-shadow: var(--card-shadow);
            margin-bottom: 30px; 
            border: none;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        .question-card { 
            border-radius: var(--border-radius-small); 
            box-shadow: var(--card-shadow);
            margin-bottom: 30px; 
            border: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            overflow: hidden;
            position: relative;
        }
        
        .question-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .question-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .question-card:hover::before {
            opacity: 1;
        }
        
        .score-display { 
            font-size: 4rem; 
            font-weight: 700; 
            background: var(--success-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
            font-family: 'Inter', sans-serif;
        }
        
        .question-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: var(--border-radius-small) var(--border-radius-small) 0 0;
            padding: 20px 25px;
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
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            pointer-events: none;
        }
        
        .question-header h6 {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .answer-key {
            background: rgba(255, 193, 7, 0.1);
            border: 2px solid rgba(255, 193, 7, 0.2);
            border-radius: var(--border-radius-small);
            padding: 20px;
            margin: 20px 0;
            position: relative;
            overflow: hidden;
        }
        
        .answer-key::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: var(--warning-gradient);
        }
        
        .answer-key strong {
            color: #e67e22;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .answer-key i {
            margin-left: 8px;
            font-size: 1.1rem;
        }
        
        .answer-key ul {
            margin: 0;
            padding-right: 20px;
        }
        
        .answer-key li {
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            position: relative;
        }
        
        .answer-key li::marker {
            color: #e67e22;
        }
        
        .score-selector {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(240, 240, 240, 0.9) 100%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: var(--border-radius-small);
            padding: 25px;
            margin-top: 25px;
            backdrop-filter: blur(10px);
        }
        
        .score-selector label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        .score-btn {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: 3px solid #e9ecef;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            color: #6c757d;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin: 0 8px 10px 8px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .score-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .score-btn span {
            position: relative;
            z-index: 1;
        }
        
        .score-btn:hover {
            border-color: #667eea;
            color: #667eea;
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .score-btn.selected {
            background: var(--primary-gradient);
            color: white;
            border-color: #667eea;
            transform: scale(1.1);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.4);
        }
        
        .score-btn.selected::before {
            opacity: 1;
        }
        
        .total-score-card {
            background: var(--dark-gradient);
            border-radius: var(--border-radius);
            color: white;
            position: sticky;
            top: 20px;
            z-index: 100;
            box-shadow: var(--card-shadow);
            backdrop-filter: blur(20px);
            overflow: hidden;
        }
        
        .total-score-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            pointer-events: none;
        }
        
        .total-score-card .card-body {
            padding: 30px;
            position: relative;
            z-index: 1;
        }
        
        .total-score-card .card-title {
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        
        .progress-indicator {
            background: var(--success-gradient);
            height: 12px;
            border-radius: 6px;
            transition: width 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: 0 4px 15px rgba(132, 250, 176, 0.3);
        }
        
        .info-badge {
            background: var(--info-gradient);
            color: #2c3e50;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 0.95rem;
            font-weight: 500;
            margin: 8px;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(168, 237, 234, 0.3);
            transition: transform 0.3s ease;
        }
        
        .info-badge:hover {
            transform: translateY(-2px);
        }
        
        .btn-success, .btn-secondary {
            border-radius: 50px;
            padding: 15px 40px;
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-success {
            background: var(--success-gradient);
            box-shadow: 0 8px 25px rgba(132, 250, 176, 0.3);
        }
        
        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(132, 250, 176, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
            box-shadow: 0 8px 25px rgba(149, 165, 166, 0.3);
        }
        
        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(149, 165, 166, 0.4);
        }
        
        .alert {
            border: none;
            border-radius: var(--border-radius-small);
            padding: 15px 20px;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }
        
        .alert-success {
            background: rgba(132, 250, 176, 0.2);
            color: #27ae60;
            border-left: 4px solid #27ae60;
        }
        
        .alert-info {
            background: rgba(168, 237, 234, 0.2);
            color: #3498db;
            border-left: 4px solid #3498db;
        }
        
        .alert-warning {
            background: rgba(253, 187, 45, 0.2);
            color: #f39c12;
            border-left: 4px solid #f39c12;
        }
        
        .alert-danger {
            background: rgba(252, 70, 107, 0.2);
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
        }
        
        .alert-secondary {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
            border-left: 4px solid #6c757d;
        }
        
        .form-control, .form-select {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: var(--border-radius-small);
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .text-start {
            font-size: 0.95rem;
        }
        
        .text-start .d-flex {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .text-start .d-flex:last-child {
            border-bottom: none;
        }
        
        /* تحسينات الرسوم المتحركة */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .question-card {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .question-card:nth-child(1) { animation-delay: 0.1s; }
        .question-card:nth-child(2) { animation-delay: 0.2s; }
        .question-card:nth-child(3) { animation-delay: 0.3s; }
        .question-card:nth-child(4) { animation-delay: 0.4s; }
        .question-card:nth-child(5) { animation-delay: 0.5s; }
        
        /* تحسينات متجاوبة */
        @media (max-width: 768px) {
            .score-btn {
                width: 45px;
                height: 45px;
                margin: 0 5px 8px 5px;
                font-size: 1rem;
            }
            
            .score-display {
                font-size: 3rem;
            }
            
            .total-score-card .card-body {
                padding: 20px;
            }
            
            .question-header {
                padding: 15px 20px;
            }
            
            .answer-key {
                padding: 15px;
            }
            
            .score-selector {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- العمود الرئيسي للأسئلة -->
            <div class="col-lg-8">
                <div class="card main-card">
                    <div class="card-header question-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="fas fa-clipboard-list"></i> 
                                تقييم تفصيلي - {{ $volunteerRequest->full_name ?? 'متطوع' }}
                            </h4>
                            <p class="mb-0 opacity-75">تقييم شامل مع مفاتيح الإجابة</p>
                        </div>
                        <div>
                            <a href="{{ route('volunteer-requests.index') }}" class="btn btn-light">
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

                        <!-- معلومات المتطوع -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card" style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border: none;">
                                    <div class="card-body">
                                        <h6><i class="fas fa-user-circle text-primary"></i> معلومات المتطوع</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <span class="info-badge">{{ $volunteerRequest->full_name ?? 'غير محدد' }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="info-badge">{{ $volunteerRequest->field_of_study ?? 'غير محدد' }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="info-badge">{{ $volunteerRequest->email ?? 'غير محدد' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('volunteer-evaluations.store', $volunteerRequest->id) }}" id="evaluationForm">
                            @csrf
                            
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
                                                <button type="button" class="btn score-btn" data-score="{{ $i }}" data-field="interview_score"><span>{{ $i }}</span></button>
                                            @endfor
                                            </div>
                                        <input type="hidden" name="interview_score" id="interview_score" value="0">
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
                                                <button type="button" class="btn score-btn" data-score="{{ $i }}" data-field="skills_assessment_score"><span>{{ $i }}</span></button>
                                            @endfor
                                            </div>
                                        <input type="hidden" name="skills_assessment_score" id="skills_assessment_score" value="0">
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
                                                <button type="button" class="btn score-btn" data-score="{{ $i }}" data-field="motivation_score"><span>{{ $i }}</span></button>
                                            @endfor
                                                    </div>
                                        <input type="hidden" name="motivation_score" id="motivation_score" value="0">
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
                                                <button type="button" class="btn score-btn" data-score="{{ $i }}" data-field="availability_score"><span>{{ $i }}</span></button>
                                            @endfor
                                                    </div>
                                        <input type="hidden" name="availability_score" id="availability_score" value="0">
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
                                                <button type="button" class="btn score-btn" data-score="{{ $i }}" data-field="teamwork_score"><span>{{ $i }}</span></button>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="teamwork_score" id="teamwork_score" value="0">
                                    </div>
                                </div>
                            </div>

                            <!-- الحقول المخفية -->
                            <input type="hidden" name="evaluation_date" value="{{ date('Y-m-d') }}">
                            <input type="hidden" name="recommendation" id="recommendation" value="">
                            <input type="hidden" name="experience_score" value="0">
                            <input type="hidden" name="communication_score" value="0">
                            <input type="hidden" name="reliability_score" value="0">
                            <input type="hidden" name="adaptability_score" value="0">
                            <input type="hidden" name="leadership_score" value="0">
                            <input type="hidden" name="technical_skills_score" value="0">
                            <input type="hidden" name="cultural_fit_score" value="0">
                            <input type="hidden" name="commitment_score" value="0">

                            <!-- ملاحظات التقييم -->
                            <div class="question-card">
                                <div class="question-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-sticky-note"></i> 
                                        ملاحظات إضافية
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="notes">أضف أي ملاحظات مهمة حول المتطوع:</label>
                                        <textarea name="notes" class="form-control" rows="4" placeholder="مثال: أظهر المتطوع حماساً كبيراً، يحتاج إلى تدريب إضافي في..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- أزرار الحفظ -->
                            <div class="text-center mt-4 mb-5">
                                <button type="submit" class="btn btn-success btn-lg" style="min-width: 200px; padding: 15px 30px;">
                                    <i class="fas fa-save"></i> حفظ التقييم
                                    </button>
                                <a href="{{ route('volunteer-requests.index') }}" class="btn btn-secondary btn-lg" style="min-width: 200px; padding: 15px 30px;">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
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
                        <div class="score-display" id="totalScore">0</div>
                        <p class="mb-3">من 50 درجة</p>
                        
                        <!-- شريط التقدم -->
                        <div class="bg-light rounded" style="height: 12px;">
                            <div class="progress-indicator" id="progressBar" style="width: 0%;"></div>
                        </div>
                        <small class="d-block mt-2" id="progressText">لم يتم التقييم بعد</small>
                        
                        <!-- تفاصيل النقاط -->
                        <div class="mt-4">
                            <h6><i class="fas fa-list"></i> تفاصيل النقاط</h6>
                            <div class="text-start" style="font-size: 0.9rem;">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>التعريف الشخصي:</span>
                                    <span id="interview_display">0/10</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>الدافع للتطوع:</span>
                                    <span id="skills_display">0/10</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>المهارات:</span>
                                    <span id="motivation_display">0/10</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>التوفر الزمني:</span>
                                    <span id="availability_display">0/10</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>التعامل مع التحديات:</span>
                                    <span id="teamwork_display">0/10</span>
                                </div>
                            </div>
                        </div>
                        
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

                        <!-- التوصية -->
                        <div class="mt-4">
                            <h6><i class="fas fa-recommendation"></i> التوصية النهائية</h6>
                            <div id="recommendationDisplay" class="alert alert-secondary">
                                <i class="fas fa-hourglass-half"></i> في انتظار التقييم
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // نظام النقاط الجديد
        document.querySelectorAll('.score-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const score = this.dataset.score;
                const field = this.dataset.field;
                
                // إزالة التحديد من الأزرار الأخرى في نفس المجموعة
                const sameLevelBtns = document.querySelectorAll(`[data-field="${field}"]`);
                sameLevelBtns.forEach(b => b.classList.remove('selected'));
                
                // تحديد الزر المختار
                this.classList.add('selected');
                
                // تحديث القيمة المخفية
                document.getElementById(field).value = score;
                
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
                alert('يرجى إكمال تقييم جميع المعايير قبل الحفظ');
                return false;
            }
        });

        // تهيئة العرض عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            updateTotalScore();
        });
    </script>
</body>
</html> 