@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('files/assets/css/progress-tracking.css') }}">
@endsection

@section('content')
<div class="pcoded-content">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <div class="page-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title">
                                <div class="d-inline">
                                    <h4 class="text-primary">
                                        <i class="feather icon-file-text"></i>
                                        تفاصيل المهمة
                                    </h4>
                                    <span class="text-muted">عرض معلومات المهمة والتفاصيل</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="page-header-breadcrumb">
                                <ul class="breadcrumb-title">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('home') }}">
                                            <i class="feather icon-home"></i>
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('tasks.index') }}">المهام</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <span>تفاصيل المهمة</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task Header Card -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-0">
                                            <i class="feather icon-file-text mr-2"></i>
                                            {{ $task->title }}
                                        </h5>
                                        <small class="text-white-50">
                                            <i class="feather icon-hash mr-1"></i>
                                            رقم المهمة: #{{ $task->id }}
                                            <span class="mx-2">|</span>
                                            <i class="feather icon-calendar mr-1"></i>
                                            تاريخ الإنشاء: {{ $task->created_at->format('Y-m-d') }}
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-md-right">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-light btn-sm">
                                                <i class="feather icon-edit"></i>
                                                تعديل
                                            </a>
                                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-light btn-sm">
                                                <i class="feather icon-arrow-right"></i>
                                                رجوع
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        @php
                                            $statusColors = [
                                                'completed' => 'success',
                                                'pending' => 'warning',
                                                'in_progress' => 'info',
                                                'cancelled' => 'secondary',
                                                'new' => 'primary',
                                            ];
                                            $statusLabel = [
                                                'completed' => 'منجزة',
                                                'pending' => 'معلقة',
                                                'in_progress' => 'قيد التنفيذ',
                                                'cancelled' => 'ملغاة',
                                                'new' => 'جديدة',
                                            ][$task->status] ?? $task->status;
                                        @endphp
                                        <span class="badge badge-{{ $statusColors[$task->status] ?? 'primary' }} badge-lg">
                                            <i class="feather icon-{{ $task->status == 'completed' ? 'check-circle' : ($task->status == 'in_progress' ? 'play-circle' : 'clock') }}"></i>
                                            {{ $statusLabel }}
                                        </span>
                                        @if($task->priority)
                                            <span class="badge badge-warning badge-lg ml-2">
                                                <i class="feather icon-flag"></i>
                                                {{ $task->priority }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $task->progress }}%">
                                                <span class="progress-text">{{ $task->progress }}% مكتمل</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="row">
                    <!-- Left Column - Task Details -->
                    <div class="col-lg-8">
                        <!-- Task Information Card -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="feather icon-info text-primary"></i>
                                    معلومات المهمة
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="feather icon-file-text text-muted"></i>
                                                الوصف
                                            </label>
                                            <p class="form-control-static">{{ $task->description ?: 'لا يوجد وصف' }}</p>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="feather icon-briefcase text-muted"></i>
                                                المشروع
                                            </label>
                                            <p class="form-control-static">
                                                <span class="badge badge-info">
                                                    {{ $task->project ? $task->project->name : 'غير محدد' }}
                                                </span>
                                            </p>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="feather icon-tag text-muted"></i>
                                                الفئة
                                            </label>
                                            <p class="form-control-static">
                                                <span class="badge badge-secondary">
                                                    {{ optional($task->category)->name ?? 'غير محدد' }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="feather icon-calendar text-muted"></i>
                                                تاريخ البدء
                                            </label>
                                            <p class="form-control-static">
                                                <i class="feather icon-calendar text-success"></i>
                                                {{ $task->start_date ? $task->start_date->format('Y-m-d') : 'غير محدد' }}
                                            </p>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="feather icon-clock text-muted"></i>
                                                تاريخ الانتهاء
                                            </label>
                                            <p class="form-control-static">
                                                <i class="feather icon-clock {{ $task->deadline && $task->deadline < now() && $task->status != 'completed' ? 'text-danger' : 'text-success' }}"></i>
                                                {{ $task->deadline ? $task->deadline->format('Y-m-d') : 'غير محدد' }}
                                                @if($task->deadline && $task->deadline < now() && $task->status != 'completed')
                                                    <span class="badge badge-danger ml-1">متأخرة</span>
                                                @endif
                                            </p>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="feather icon-bar-chart-2 text-muted"></i>
                                                نسبة الإنجاز
                                            </label>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $task->progress }}%">
                                                    {{ $task->progress }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Tracking Card -->
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="feather icon-bar-chart-2"></i>
                                    تتبع التقدم التفصيلي
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="progress-tracking-section">
                                            <h6 class="text-muted mb-3">
                                                <i class="feather icon-target"></i>
                                                نسبة الإنجاز الحالية
                                            </h6>
                                            <div class="progress mb-3" style="height: 30px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $task->progress }}%" 
                                                     id="taskProgressBar">
                                                    <span class="progress-text">{{ $task->progress }}% مكتمل</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Progress Update Form -->
                                            <form id="progressUpdateForm" class="mt-3">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">تحديث نسبة التقدم</label>
                                                            <div class="input-group">
                                                                <input type="range" class="form-range" 
                                                                       id="progressSlider" 
                                                                       min="0" max="100" 
                                                                       value="{{ $task->progress }}"
                                                                       step="5">
                                                                <span class="input-group-text" id="progressValue">{{ $task->progress }}%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">إضافة ملاحظة</label>
                                                            <textarea class="form-control" id="progressNote" 
                                                                      rows="2" placeholder="أضف ملاحظة حول التقدم..."></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-success btn-block mt-3">
                                                    <i class="feather icon-save"></i>
                                                    تحديث التقدم
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="progress-stats">
                                            <h6 class="text-muted mb-3">
                                                <i class="feather icon-info"></i>
                                                إحصائيات التقدم
                                            </h6>
                                            
                                            <div class="stat-item mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">الوقت المتبقي:</span>
                                                    <span class="fw-bold" id="timeRemaining">
                                                        @if($task->deadline)
                                                            @php
                                                                $daysLeft = now()->diffInDays($task->deadline, false);
                                                            @endphp
                                                            @if($daysLeft > 0)
                                                                <span class="text-success">{{ $daysLeft }} يوم</span>
                                                            @elseif($daysLeft == 0)
                                                                <span class="text-warning">اليوم</span>
                                                            @else
                                                                <span class="text-danger">متأخرة {{ abs($daysLeft) }} يوم</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">غير محدد</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="stat-item mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">معدل التقدم:</span>
                                                    <span class="fw-bold" id="progressRate">
                                                        @php
                                                            $startDate = $task->start_date ? $task->start_date : $task->created_at;
                                                            $totalDays = $task->deadline ? $startDate->diffInDays($task->deadline) : 0;
                                                            $elapsedDays = $startDate->diffInDays(now());
                                                            $expectedProgress = $totalDays > 0 ? min(100, ($elapsedDays / $totalDays) * 100) : 0;
                                                            $progressRate = $task->progress > 0 ? ($task->progress / $expectedProgress) * 100 : 0;
                                                        @endphp
                                                        @if($expectedProgress > 0)
                                                            @if($progressRate >= 100)
                                                                <span class="text-success">ممتاز</span>
                                                            @elseif($progressRate >= 80)
                                                                <span class="text-info">جيد</span>
                                                            @elseif($progressRate >= 60)
                                                                <span class="text-warning">متوسط</span>
                                                            @else
                                                                <span class="text-danger">بطيء</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">غير محدد</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="stat-item mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">آخر تحديث:</span>
                                                    <span class="fw-bold text-muted">
                                                        {{ $task->updated_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Progress History -->
                                <div class="progress-history mt-4">
                                    <h6 class="text-muted mb-3">
                                        <i class="feather icon-clock"></i>
                                        سجل التقدم
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>التاريخ</th>
                                                    <th>نسبة التقدم</th>
                                                    <th>التغيير</th>
                                                    <th>الملاحظة</th>
                                                    <th>المستخدم</th>
                                                </tr>
                                            </thead>
                                            <tbody id="progressHistoryTable">
                                                <!-- سيتم ملؤها بواسطة JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const progressSlider = document.getElementById('progressSlider');
                            const progressValue = document.getElementById('progressValue');
                            const progressBar = document.getElementById('taskProgressBar');
                            const progressUpdateForm = document.getElementById('progressUpdateForm');
                            const progressNote = document.getElementById('progressNote');
                            
                            // تحديث قيمة التقدم عند تحريك الشريط
                            progressSlider.addEventListener('input', function() {
                                const value = this.value;
                                progressValue.textContent = value + '%';
                                progressBar.style.width = value + '%';
                                progressBar.querySelector('.progress-text').textContent = value + '% مكتمل';
                            });
                            
                            // معالجة تحديث التقدم
                            progressUpdateForm.addEventListener('submit', function(e) {
                                e.preventDefault();
                                
                                const newProgress = progressSlider.value;
                                const note = progressNote.value;
                                
                                // تعطيل النموذج أثناء التحديث
                                const submitBtn = this.querySelector('button[type="submit"]');
                                const originalText = submitBtn.innerHTML;
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = '<i class="feather icon-loader"></i> جاري التحديث...';
                                
                                // إرسال طلب التحديث
                                fetch('{{ route("tasks.updateProgress", $task->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        progress: newProgress,
                                        note: note
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // عرض رسالة النجاح
                                        showAlert('success', data.message);
                                        
                                        // تحديث البيانات في الصفحة
                                        updateProgressDisplay(data.task);
                                        
                                        // مسح الملاحظة
                                        progressNote.value = '';
                                        
                                        // تحديث سجل التقدم
                                        loadProgressHistory();
                                    } else {
                                        showAlert('error', data.message || 'حدث خطأ أثناء التحديث');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    showAlert('error', 'حدث خطأ في الاتصال بالخادم');
                                })
                                .finally(() => {
                                    // إعادة تفعيل النموذج
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = originalText;
                                });
                            });
                            
                            // تحميل سجل التقدم
                            loadProgressHistory();
                            
                            function loadProgressHistory() {
                                fetch('{{ route("tasks.progressHistory", $task->id) }}')
                                .then(response => response.json())
                                .then(data => {
                                    const tableBody = document.getElementById('progressHistoryTable');
                                    tableBody.innerHTML = '';
                                    
                                    if (data.history && data.history.length > 0) {
                                        data.history.forEach(item => {
                                            const row = document.createElement('tr');
                                            row.innerHTML = `
                                                <td>${item.created_at}</td>
                                                <td>
                                                    <span class="badge bg-success">${item.progress}%</span>
                                                </td>
                                                <td>
                                                    ${item.change > 0 ? '+' : ''}${item.change}%
                                                </td>
                                                <td>${item.note || '-'}</td>
                                                <td>${item.user_name}</td>
                                            `;
                                            tableBody.appendChild(row);
                                        });
                                    } else {
                                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">لا يوجد سجل تقدم</td></tr>';
                                    }
                                })
                                .catch(error => {
                                    console.error('Error loading progress history:', error);
                                });
                            }
                            
                            function updateProgressDisplay(task) {
                                // تحديث شريط التقدم في الهيدر
                                const headerProgressBar = document.querySelector('.progress-bar.bg-success');
                                if (headerProgressBar) {
                                    headerProgressBar.style.width = task.progress + '%';
                                    const progressText = headerProgressBar.querySelector('.progress-text');
                                    if (progressText) {
                                        progressText.textContent = task.progress + '% مكتمل';
                                    }
                                }
                                
                                // تحديث شريط التقدم في قسم المعلومات
                                const infoProgressBar = document.querySelector('.form-group .progress-bar');
                                if (infoProgressBar) {
                                    infoProgressBar.style.width = task.progress + '%';
                                    infoProgressBar.textContent = task.progress + '%';
                                }
                                
                                // تحديث آخر تحديث
                                const lastUpdateElement = document.querySelector('.stat-item .fw-bold.text-muted');
                                if (lastUpdateElement) {
                                    lastUpdateElement.textContent = 'الآن';
                                }
                            }
                            
                            function showAlert(type, message) {
                                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                                const alertHtml = `
                                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                                        <i class="feather icon-${type === 'success' ? 'check-circle' : 'alert-triangle'}"></i>
                                        ${message}
                                        <button type="button" class="close" data-dismiss="alert">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                `;
                                
                                const container = document.querySelector('.page-wrapper');
                                if (container) {
                                    container.insertAdjacentHTML('afterbegin', alertHtml);
                                }
                                
                                setTimeout(() => {
                                    const alerts = document.querySelectorAll('.alert');
                                    alerts.forEach(alert => {
                                        if (alert) {
                                            alert.remove();
                                        }
                                    });
                                }, 5000);
                            }
                        });
                        </script>

                        <!-- Assignments Card -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="feather icon-users text-primary"></i>
                                    المكلفون
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">
                                            <i class="feather icon-user-check"></i>
                                            المكلفون الحاليون
                                        </h6>
                                        @if($task->assignments->count() > 0)
                                            <div class="list-group">
                                                @foreach($task->assignments as $assignment)
                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i class="feather icon-user text-primary"></i>
                                                            <strong>{{ $assignment->user->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="feather icon-calendar"></i>
                                                                {{ $assignment->assigned_at->format('Y-m-d') }}
                                                            </small>
                                                        </div>
                                                        <span class="badge badge-success badge-pill">
                                                            <i class="feather icon-check"></i>
                                                            مكلف
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="feather icon-users text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">لا يوجد مكلفين حالياً</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">
                                            <i class="feather icon-user-plus"></i>
                                            إضافة مكلف جديد
                                        </h6>
                                        <form action="{{ route('tasks.assign', $task->id) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label class="form-label">اختر متطوع</label>
                                                <select name="user_id" class="form-control" required>
                                                    <option value="">-- اختر متطوع --</option>
                                                    @foreach($availableVolunteers as $volunteer)
                                                        <option value="{{ $volunteer->id }}">{{ $volunteer->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="feather icon-plus"></i>
                                                تخصيص
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dependencies Card -->
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="feather icon-link"></i>
                                    تبعيات المهمة
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($task->taskDependencies && $task->taskDependencies->count())
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th><i class="feather icon-hash"></i> المهمة</th>
                                                    <th><i class="feather icon-link"></i> نوع التبعية</th>
                                                    <th><i class="feather icon-trash-2"></i> إجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($task->taskDependencies as $dep)
                                                    <tr>
                                                        <td>
                                                            <i class="feather icon-file-text text-info"></i>
                                                            {{ optional($dep->prerequisiteTask)->title ?? 'غير محدد' }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $types = [
                                                                    'finish_to_start' => 'إنهاء-لبدء',
                                                                    'start_to_start' => 'بدء-لبدء',
                                                                    'finish_to_finish' => 'إنهاء-لإنهاء',
                                                                    'start_to_finish' => 'بدء-لإنهاء',
                                                                ];
                                                            @endphp
                                                            <span class="badge badge-info">{{ $types[$dep->dependency_type] ?? $dep->dependency_type }}</span>
                                                        </td>
                                                        <td>
                                                            <form action="{{ route('tasks.dependencies.destroy', [$task->id, $dep->id]) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل تريد حذف التبعية؟')">
                                                                    <i class="feather icon-trash-2"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="feather icon-link text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">لا توجد تبعيات حالياً لهذه المهمة</p>
                                    </div>
                                @endif
                                
                                <hr>
                                
                                <h6 class="text-muted mb-3">
                                    <i class="feather icon-plus-circle"></i>
                                    إضافة تبعية جديدة
                                </h6>
                                <form action="{{ route('tasks.dependencies.store', $task->id) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">المهمة المعتمَد عليها</label>
                                                <select name="depends_on_id" class="form-control" required>
                                                    <option value="">-- اختر مهمة --</option>
                                                    @foreach($allTasks as $t)
                                                        @if($t->id != $task->id && !$task->taskDependencies->pluck('depends_on_task_id')->contains($t->id))
                                                            <option value="{{ $t->id }}">{{ $t->title }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">نوع التبعية</label>
                                                <select name="dependency_type" class="form-control">
                                                    <option value="finish_to_start">إنهاء-لبدء</option>
                                                    <option value="start_to_start">بدء-لبدء</option>
                                                    <option value="finish_to_finish">إنهاء-لإنهاء</option>
                                                    <option value="start_to_finish">بدء-لإنهاء</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="submit" class="btn btn-info btn-block">
                                                    <i class="feather icon-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Actions & Status -->
                    <div class="col-lg-4">
                        <!-- Status Update Card -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="feather icon-refresh-cw text-primary"></i>
                                    تحديث الحالة
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('tasks.updateStatus', $task->id) }}" id="statusUpdateForm">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">الحالة الجديدة</label>
                                        <select name="status" class="form-control" required id="statusSelect">
                                            <option value="new" {{ $task->status == 'new' ? 'selected' : '' }}>جديدة</option>
                                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>معلقة</option>
                                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>منجزة</option>
                                            <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block" id="updateStatusBtn">
                                        <i class="feather icon-save"></i>
                                        تحديث الحالة
                                    </button>
                                    
                                    <!-- زر اختبار بسيط -->
                                    <button type="button" class="btn btn-info btn-block mt-2" id="simpleUpdateBtn">
                                        <i class="feather icon-refresh-cw"></i>
                                        تحديث بسيط (اختبار)
                                    </button>
                                </form>
                                
                                <!-- رسالة الحالة الحالية -->
                                <div class="alert alert-info mt-3" id="currentStatusAlert" style="display: none;">
                                    <i class="feather icon-info"></i>
                                    <span id="currentStatusMessage"></span>
                                </div>
                            </div>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const statusSelect = document.getElementById('statusSelect');
                            const updateStatusBtn = document.getElementById('updateStatusBtn');
                            const currentStatusAlert = document.getElementById('currentStatusAlert');
                            const currentStatusMessage = document.getElementById('currentStatusMessage');
                                                         const statusUpdateForm = document.getElementById('statusUpdateForm');
                             const currentStatus = '{{ $task->status }}';
                             
                             // تخزين الحالة الحالية في متغير عام
                             window.currentTaskStatus = currentStatus;
                            
                                                         function checkStatusChange() {
                                 const selectedStatus = statusSelect.value;
                                 const currentTaskStatus = window.currentTaskStatus || currentStatus;
                                 
                                 if (selectedStatus === currentTaskStatus) {
                                     updateStatusBtn.disabled = false;
                                     updateStatusBtn.innerHTML = '<i class="feather icon-save"></i> تحديث الحالة';
                                     updateStatusBtn.className = 'btn btn-info btn-block';
                                     
                                     currentStatusAlert.style.display = 'block';
                                     currentStatusMessage.textContent = 'هذه هي الحالة الحالية للمهمة، يمكنك التحديث إذا كنت تريد إعادة تأكيد الحالة.';
                                 } else {
                                     updateStatusBtn.disabled = false;
                                     updateStatusBtn.innerHTML = '<i class="feather icon-save"></i> تحديث الحالة';
                                     updateStatusBtn.className = 'btn btn-primary btn-block';
                                     
                                     currentStatusAlert.style.display = 'none';
                                 }
                             }
                             
                             function updateTaskStatusInPage(task) {
                                 // تحديث شارة الحالة في الهيدر
                                 const statusBadges = document.querySelectorAll('.badge-lg');
                                 const statusBadge = Array.from(statusBadges).find(badge => 
                                     badge.textContent.includes('منجزة') || 
                                     badge.textContent.includes('قيد التنفيذ') || 
                                     badge.textContent.includes('معلقة') || 
                                     badge.textContent.includes('ملغاة') || 
                                     badge.textContent.includes('جديدة')
                                 );
                                 
                                 if (statusBadge) {
                                     const statusColors = {
                                         'completed': 'success',
                                         'pending': 'warning',
                                         'in_progress': 'info',
                                         'cancelled': 'secondary',
                                         'new': 'primary',
                                     };
                                     const statusLabels = {
                                         'completed': 'منجزة',
                                         'pending': 'معلقة',
                                         'in_progress': 'قيد التنفيذ',
                                         'cancelled': 'ملغاة',
                                         'new': 'جديدة',
                                     };
                                     const statusIcons = {
                                         'completed': 'check-circle',
                                         'in_progress': 'play-circle',
                                         'pending': 'clock',
                                         'cancelled': 'x-circle',
                                         'new': 'clock',
                                     };
                                     
                                     const newColor = statusColors[task.status] || 'primary';
                                     const newLabel = statusLabels[task.status] || task.status;
                                     const newIcon = statusIcons[task.status] || 'clock';
                                     
                                     statusBadge.className = `badge badge-${newColor} badge-lg`;
                                     statusBadge.innerHTML = `<i class="feather icon-${newIcon}"></i> ${newLabel}`;
                                 }
                                 
                                 // تحديث خيار الحالة المحدد في القائمة المنسدلة
                                 const statusSelect = document.getElementById('statusSelect');
                                 if (statusSelect) {
                                     statusSelect.value = task.status;
                                 }
                                 
                                 // تحديث شارة الأولوية إذا كانت موجودة
                                 const priorityBadge = document.querySelector('.badge-warning.badge-lg');
                                 if (priorityBadge && task.priority) {
                                     priorityBadge.innerHTML = `<i class="feather icon-flag"></i> ${task.priority}`;
                                 }
                                 
                                 // تحديث شارة "متأخرة" إذا لزم الأمر
                                 updateOverdueBadge(task);
                             }
                             
                             function updateOverdueBadge(task) {
                                 const deadlineElement = document.querySelector('.form-control-static i.feather.icon-clock');
                                 const overdueBadge = document.querySelector('.badge-danger');
                                 
                                 if (deadlineElement && task.deadline) {
                                     const deadline = new Date(task.deadline);
                                     const now = new Date();
                                     const isOverdue = deadline < now && task.status !== 'completed';
                                     
                                     if (isOverdue) {
                                         deadlineElement.className = 'feather icon-clock text-danger';
                                         if (!overdueBadge) {
                                             const badge = document.createElement('span');
                                             badge.className = 'badge badge-danger ml-1';
                                             badge.textContent = 'متأخرة';
                                             deadlineElement.parentNode.appendChild(badge);
                                         }
                                     } else {
                                         deadlineElement.className = 'feather icon-clock text-success';
                                         if (overdueBadge) {
                                             overdueBadge.remove();
                                         }
                                     }
                                 }
                             }
                            
                            // فحص التغيير عند تحميل الصفحة
                            checkStatusChange();
                            
                            // فحص التغيير عند تغيير الاختيار
                            statusSelect.addEventListener('change', checkStatusChange);
                            
                            // زر التحديث البسيط للاختبار
                            document.getElementById('simpleUpdateBtn').addEventListener('click', function() {
                                const selectedStatus = statusSelect.value;
                                console.log('Simple update clicked, status:', selectedStatus);
                                
                                $.post('{{ route("tasks.updateStatus", $task->id) }}', {
                                    status: selectedStatus,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                })
                                .done(function(data) {
                                    console.log('Simple update success:', data);
                                    alert('تم التحديث بنجاح: ' + data.message);
                                })
                                .fail(function(xhr) {
                                    console.log('Simple update failed:', xhr);
                                    alert('فشل التحديث: ' + xhr.responseText);
                                });
                            });
                            
                            // معالجة تقديم النموذج
                            statusUpdateForm.addEventListener('submit', function(e) {
                                e.preventDefault();
                                
                                const selectedStatus = statusSelect.value;
                                console.log('Submitting status update:', selectedStatus);
                                console.log('Form action:', this.action);
                                
                                // السماح بالتحديث حتى لو كانت الحالة نفسها
                                
                                // تعطيل الزر أثناء التحديث
                                updateStatusBtn.disabled = true;
                                updateStatusBtn.innerHTML = '<i class="feather icon-loader"></i> جاري التحديث...';
                                
                                // إرسال الطلب
                                const formData = new FormData(this);
                                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                                
                                fetch(this.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.json().then(data => {
                                            throw new Error(data.message || 'حدث خطأ في الخادم');
                                        });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Response data:', data);
                                    if (data.success) {
                                        // عرض رسالة النجاح
                                        showAlert('success', data.message);
                                        
                                        // تحديث الحالة الحالية في الصفحة بدون إعادة تحميل
                                        updateTaskStatusInPage(data.task);
                                        
                                        // تحديث الحالة المحلية
                                        window.currentTaskStatus = data.task.status;
                                        checkStatusChange();
                                    } else {
                                        console.error('Update failed:', data);
                                        showAlert('error', data.message || 'حدث خطأ أثناء التحديث');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error updating status:', error);
                                    showAlert('error', error.message || 'حدث خطأ في الاتصال بالخادم');
                                })
                                .finally(() => {
                                    // إعادة تفعيل الزر
                                    updateStatusBtn.disabled = false;
                                    updateStatusBtn.innerHTML = '<i class="feather icon-save"></i> تحديث الحالة';
                                });
                            });
                            
                                                         function showAlert(type, message) {
                                 const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                                 const alertHtml = `
                                     <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                                         <i class="feather icon-${type === 'success' ? 'check-circle' : 'alert-triangle'}"></i>
                                         ${message}
                                         <button type="button" class="close" data-dismiss="alert">
                                             <span>&times;</span>
                                         </button>
                                     </div>
                                 `;
                                 
                                 // إضافة التنبيه في أعلى الصفحة
                                 const container = document.querySelector('.page-wrapper');
                                 if (container) {
                                     container.insertAdjacentHTML('afterbegin', alertHtml);
                                 } else {
                                     // إذا لم يتم العثور على الحاوية، أضف التنبيه في بداية body
                                     document.body.insertAdjacentHTML('afterbegin', alertHtml);
                                 }
                                 
                                 // إزالة التنبيه تلقائياً بعد 5 ثوان
                                 setTimeout(() => {
                                     const alerts = document.querySelectorAll('.alert');
                                     alerts.forEach(alert => {
                                         if (alert) {
                                             alert.remove();
                                         }
                                     });
                                 }, 5000);
                             }
                        });
                        </script>

                        <!-- Quick Actions Card -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="feather icon-zap text-warning"></i>
                                    إجراءات سريعة
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-outline-primary">
                                        <i class="feather icon-edit"></i>
                                        تعديل المهمة
                                    </a>
                                    
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-block" onclick="return confirm('هل أنت متأكد من حذف هذه المهمة؟')">
                                            <i class="feather icon-trash-2"></i>
                                            حذف المهمة
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Task Statistics Card -->
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="feather icon-bar-chart-2"></i>
                                    إحصائيات المهمة
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="stat-item">
                                            <i class="feather icon-users text-primary" style="font-size: 2rem;"></i>
                                            <h4 class="text-primary">{{ $task->assignments->count() }}</h4>
                                            <small class="text-muted">المكلفون</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-item">
                                            <i class="feather icon-link text-info" style="font-size: 2rem;"></i>
                                            <h4 class="text-info">{{ $task->taskDependencies->count() }}</h4>
                                            <small class="text-muted">التبعيات</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                   @php
                                            $daysLeft = now()->diffInDays($task->deadline, false);
                                        @endphp
                                        @if($daysLeft > 0)
                                            <h3 class="text-success">
                                                <i class="feather icon-calendar"></i>
                                                {{ $daysLeft }} يوم
                                            </h3>     <div class="text-center">
                                    <h6 class="text-muted">الأيام المتبقية</h6>
                                    @if($task->deadline)
                                
                                        @elseif($daysLeft == 0)
                                            <h3 class="text-warning">
                                                <i class="feather icon-clock"></i>
                                                اليوم
                                            </h3>
                                        @else
                                            <h3 class="text-danger">
                                                <i class="feather icon-alert-triangle"></i>
                                                متأخرة {{ abs($daysLeft) }} يوم
                                            </h3>
                                        @endif
                                    @else
                                        <h3 class="text-muted">
                                            <i class="feather icon-minus"></i>
                                            غير محدد
                                        </h3>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-weight: bold;
    font-size: 12px;
}

.stat-item {
    padding: 15px 0;
}

.stat-item i {
    margin-bottom: 10px;
}

.stat-item h4 {
    margin: 10px 0 5px 0;
    font-weight: bold;
}

.form-control-static {
    padding: 0.375rem 0.75rem;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    min-height: 38px;
    display: flex;
    align-items: center;
}

.form-control-static i {
    margin-left: 8px;
}
</style>
@endsection
