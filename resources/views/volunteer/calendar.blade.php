@extends('layouts.master')

@section('title', 'التقويم التفاعلي')

@section('content')
<div class="pcoded-content">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <!-- Header -->
                <div class="page-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title">
                                <div class="d-inline">
                                    <h4 class="text-primary">
                                        <i class="feather icon-calendar"></i>
                                        التقويم التفاعلي
                                    </h4>
                                    <span class="text-muted">عرض مهامك حسب التاريخ</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="page-header-breadcrumb">
                                <ul class="breadcrumb-title">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('volunteer.dashboard') }}">
                                            <i class="feather icon-home"></i>
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <span>التقويم التفاعلي</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Controls -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="card-title mb-0">
                                            <i class="feather icon-calendar text-primary"></i>
                                            {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                                        </h5>
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        <div class="btn-group" role="group">
                                            <a href="?month={{ \Carbon\Carbon::parse($month)->subMonth()->format('Y-m') }}" 
                                               class="btn btn-outline-primary">
                                                <i class="feather icon-chevron-left"></i>
                                                الشهر السابق
                                            </a>
                                            <a href="?month={{ \Carbon\Carbon::now()->format('Y-m') }}" 
                                               class="btn btn-outline-secondary">
                                                الشهر الحالي
                                            </a>
                                            <a href="?month={{ \Carbon\Carbon::parse($month)->addMonth()->format('Y-m') }}" 
                                               class="btn btn-outline-primary">
                                                الشهر التالي
                                                <i class="feather icon-chevron-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Legend -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="bg-success rounded-circle mr-2" style="width: 20px; height: 20px;"></div>
                                            <span class="text-muted">مكتملة</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="bg-primary rounded-circle mr-2" style="width: 20px; height: 20px;"></div>
                                            <span class="text-muted">قيد التنفيذ</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="bg-warning rounded-circle mr-2" style="width: 20px; height: 20px;"></div>
                                            <span class="text-muted">معلقة</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="bg-danger rounded-circle mr-2" style="width: 20px; height: 20px;"></div>
                                            <span class="text-muted">متأخرة</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="calendar-container">
                                    <!-- Calendar Header -->
                                    <div class="calendar-header mb-3">
                                        <div class="row text-center">
                                            <div class="col">الأحد</div>
                                            <div class="col">الاثنين</div>
                                            <div class="col">الثلاثاء</div>
                                            <div class="col">الأربعاء</div>
                                            <div class="col">الخميس</div>
                                            <div class="col">الجمعة</div>
                                            <div class="col">السبت</div>
                                        </div>
                                    </div>

                                    <!-- Calendar Grid -->
                                    <div class="calendar-grid">
                                        @php
                                            $startOfMonth = \Carbon\Carbon::parse($month)->startOfMonth();
                                            $endOfMonth = \Carbon\Carbon::parse($month)->endOfMonth();
                                            $startOfWeek = $startOfMonth->copy()->startOfWeek();
                                            $endOfWeek = $endOfMonth->copy()->endOfWeek();
                                            $currentDate = $startOfWeek->copy();
                                        @endphp

                                        @while($currentDate <= $endOfWeek)
                                            <div class="calendar-week">
                                                @for($i = 0; $i < 7; $i++)
                                                    @php
                                                        $dateKey = $currentDate->format('Y-m-d');
                                                        $isCurrentMonth = $currentDate->month == $startOfMonth->month;
                                                        $isToday = $currentDate->isToday();
                                                        $dayTasks = $calendarData[$dateKey] ?? [];
                                                        
                                                        // تصنيف المهام حسب الحالة
                                                        $completedTasks = collect($dayTasks)->where('status', 'completed');
                                                        $inProgressTasks = collect($dayTasks)->where('status', 'in_progress');
                                                        $pendingTasks = collect($dayTasks)->where('status', 'pending');
                                                        $overdueTasks = collect($dayTasks)->filter(function($task) {
                                                            return $task->deadline && $task->deadline->isPast() && $task->status !== 'completed';
                                                        });
                                                    @endphp
                                                    
                                                    <div class="calendar-day {{ $isCurrentMonth ? '' : 'other-month' }} {{ $isToday ? 'today' : '' }}">
                                                        <div class="calendar-day-header">
                                                            <span class="calendar-day-number">{{ $currentDate->format('j') }}</span>
                                                            @if($isToday)
                                                                <span class="today-indicator"></span>
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="calendar-day-content">
                                                            @if($dayTasks->count() > 0)
                                                                <div class="task-indicators">
                                                                    @if($completedTasks->count() > 0)
                                                                        <div class="task-indicator completed" title="{{ $completedTasks->count() }} مهام مكتملة">
                                                                            <span class="indicator-dot bg-success"></span>
                                                                            <small>{{ $completedTasks->count() }}</small>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    @if($inProgressTasks->count() > 0)
                                                                        <div class="task-indicator in-progress" title="{{ $inProgressTasks->count() }} مهام قيد التنفيذ">
                                                                            <span class="indicator-dot bg-primary"></span>
                                                                            <small>{{ $inProgressTasks->count() }}</small>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    @if($pendingTasks->count() > 0)
                                                                        <div class="task-indicator pending" title="{{ $pendingTasks->count() }} مهام معلقة">
                                                                            <span class="indicator-dot bg-warning"></span>
                                                                            <small>{{ $pendingTasks->count() }}</small>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    @if($overdueTasks->count() > 0)
                                                                        <div class="task-indicator overdue" title="{{ $overdueTasks->count() }} مهام متأخرة">
                                                                            <span class="indicator-dot bg-danger"></span>
                                                                            <small>{{ $overdueTasks->count() }}</small>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                
                                                                <div class="task-preview">
                                                                    @foreach($dayTasks->take(2) as $task)
                                                                        <div class="task-item" data-toggle="tooltip" title="{{ $task->title }}">
                                                                            <small class="task-title">{{ Str::limit($task->title, 15) }}</small>
                                                                            @if($task->priority == 'high')
                                                                                <span class="priority-indicator bg-danger"></span>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                    @if($dayTasks->count() > 2)
                                                                        <div class="more-tasks">
                                                                            <small class="text-muted">+{{ $dayTasks->count() - 2 }} أكثر</small>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="calendar-day-footer">
                                                            <button class="btn btn-sm btn-outline-primary add-task-btn" 
                                                                    data-date="{{ $dateKey }}"
                                                                    data-toggle="modal" 
                                                                    data-target="#addTaskModal">
                                                                <i class="feather icon-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    @php
                                                        $currentDate->addDay();
                                                    @endphp
                                                @endfor
                                            </div>
                                        @endwhile
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tasks Summary -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="feather icon-list text-primary"></i>
                                    ملخص المهام لهذا الشهر
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-success">{{ collect($calendarData)->flatten()->where('status', 'completed')->count() }}</h4>
                                            <span class="text-muted">مكتملة</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-primary">{{ collect($calendarData)->flatten()->where('status', 'in_progress')->count() }}</h4>
                                            <span class="text-muted">قيد التنفيذ</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-warning">{{ collect($calendarData)->flatten()->where('status', 'pending')->count() }}</h4>
                                            <span class="text-muted">معلقة</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-danger">{{ collect($calendarData)->flatten()->filter(function($task) {
                                                return $task->deadline && $task->deadline->isPast() && $task->status !== 'completed';
                                            })->count() }}</h4>
                                            <span class="text-muted">متأخرة</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة مهمة جديدة</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addTaskForm">
                    <div class="form-group">
                        <label>عنوان المهمة</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>الوصف</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>التاريخ المحدد</label>
                        <input type="date" class="form-control" name="deadline" id="taskDeadline">
                    </div>
                    <div class="form-group">
                        <label>الأولوية</label>
                        <select class="form-control" name="priority">
                            <option value="low">منخفضة</option>
                            <option value="medium">متوسطة</option>
                            <option value="high">عالية</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="submitTask()">إضافة المهمة</button>
            </div>
        </div>
    </div>
</div>

<style>
.calendar-container {
    background: #fff;
    border-radius: 8px;
}

.calendar-header {
    background: #f8f9fa;
    padding: 15px 0;
    border-radius: 8px 8px 0 0;
    font-weight: bold;
    color: #495057;
}

.calendar-grid {
    padding: 0;
}

.calendar-week {
    display: flex;
    border-bottom: 1px solid #e9ecef;
}

.calendar-week:last-child {
    border-bottom: none;
}

.calendar-day {
    flex: 1;
    min-height: 120px;
    border-right: 1px solid #e9ecef;
    padding: 8px;
    position: relative;
    background: #fff;
    transition: all 0.3s ease;
}

.calendar-day:last-child {
    border-right: none;
}

.calendar-day:hover {
    background: #f8f9fa;
    transform: scale(1.02);
    z-index: 1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.calendar-day.other-month {
    background: #f8f9fa;
    color: #adb5bd;
}

.calendar-day.today {
    background: #e3f2fd;
    border: 2px solid #2196f3;
}

.calendar-day-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.calendar-day-number {
    font-weight: bold;
    font-size: 16px;
}

.today-indicator {
    width: 8px;
    height: 8px;
    background: #2196f3;
    border-radius: 50%;
}

.calendar-day-content {
    min-height: 60px;
}

.task-indicators {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 8px;
}

.task-indicator {
    display: flex;
    align-items: center;
    gap: 2px;
    font-size: 10px;
}

.indicator-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.task-preview {
    font-size: 11px;
}

.task-item {
    padding: 2px 4px;
    margin-bottom: 2px;
    background: #f8f9fa;
    border-radius: 3px;
    border-left: 3px solid #007bff;
    position: relative;
}

.task-item.completed {
    border-left-color: #28a745;
    background: #d4edda;
}

.task-item.overdue {
    border-left-color: #dc3545;
    background: #f8d7da;
}

.priority-indicator {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    right: 2px;
}

.more-tasks {
    text-align: center;
    padding: 2px;
}

.calendar-day-footer {
    position: absolute;
    bottom: 4px;
    right: 4px;
}

.add-task-btn {
    width: 24px;
    height: 24px;
    padding: 0;
    border-radius: 50%;
    font-size: 12px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.calendar-day:hover .add-task-btn {
    opacity: 1;
}

@media (max-width: 768px) {
    .calendar-day {
        min-height: 80px;
        padding: 4px;
    }
    
    .calendar-day-number {
        font-size: 14px;
    }
    
    .task-preview {
        font-size: 10px;
    }
    
    .add-task-btn {
        opacity: 1;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تهيئة tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // إعداد التاريخ المحدد عند فتح modal
    $('#addTaskModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var date = button.data('date');
        $('#taskDeadline').val(date);
    });
});

function submitTask() {
    // هنا يمكن إضافة كود لإرسال المهمة الجديدة
    alert('سيتم إضافة المهمة الجديدة قريباً');
    $('#addTaskModal').modal('hide');
}

// إضافة تأثيرات تفاعلية
document.querySelectorAll('.calendar-day').forEach(day => {
    day.addEventListener('click', function(e) {
        if (!e.target.closest('.add-task-btn')) {
            const date = this.querySelector('.calendar-day-number').textContent;
            const month = '{{ \Carbon\Carbon::parse($month)->format("F Y") }}';
            console.log(`تم النقر على ${date} ${month}`);
        }
    });
});
</script>
@endsection 