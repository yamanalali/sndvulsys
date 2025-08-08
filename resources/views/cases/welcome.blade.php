@extends('layouts.app')

@section('title', 'مرحباً بك في نظام إدارة الحالات')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h3>مرحباً بك في نظام إدارة الحالات</h3>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-clipboard-list fa-4x text-primary"></i>
                    </div>
                    
                    <h4>نظام إدارة الحالات جاهز للاستخدام</h4>
                    <p class="text-muted mb-4">
                        هذا النظام يتيح لك إدارة طلبات التطوع كحالات قابلة للمراجعة والتتبع
                    </p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>الميزات المتاحة:</h5>
                                    <ul class="text-right list-unstyled">
                                        <li>✅ عرض جميع طلبات التطوع</li>
                                        <li>✅ تحديث حالة الطلبات</li>
                                        <li>✅ تعيين مراجعين للطلبات</li>
                                        <li>✅ تتبع تقدم المراجعة</li>
                                        <li>✅ إضافة ملاحظات وتعليقات</li>
                                        <li>✅ تصدير التقارير</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>إحصائيات النظام:</h5>
                                    <ul class="text-right list-unstyled">
                                        <li>📊 إجمالي الطلبات: {{ \App\Models\VolunteerRequest::count() }}</li>
                                        <li>👥 عدد المستخدمين: {{ \App\Models\User::count() }}</li>
                                        <li>📋 عدد سير العمل: {{ \App\Models\Workflow::count() }}</li>
                                        <li>📤 عدد الإرسالات: {{ \App\Models\Submission::count() }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="btn-group" role="group">
                        <a href="{{ route('volunteer-requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> إنشاء طلب تطوع جديد
                        </a>
                        <a href="{{ route('case-management.index') }}" class="btn btn-info">
                            <i class="fas fa-list"></i> عرض جميع الحالات
                        </a>
                        <a href="{{ route('case-management.dashboard') }}" class="btn btn-success">
                            <i class="fas fa-chart-bar"></i> لوحة التحكم
                        </a>
                        <a href="{{ route('cases.test') }}" class="btn btn-warning">
                            <i class="fas fa-bug"></i> اختبار النظام
                        </a>
                    </div>

                    <hr>

                    <div class="alert alert-info">
                        <h6>كيفية الاستخدام:</h6>
                        <ol class="text-right">
                            <li>انقر على "إنشاء طلب تطوع جديد" لإضافة طلب جديد</li>
                            <li>اذهب إلى "عرض جميع الحالات" لمراجعة الطلبات الموجودة</li>
                            <li>استخدم "لوحة التحكم" لرؤية الإحصائيات والتقارير</li>
                            <li>يمكنك تحديث حالة الطلبات وتعيين مراجعين لها</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 