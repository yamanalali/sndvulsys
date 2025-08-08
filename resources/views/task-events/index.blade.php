@extends('layouts.master')

@section('title', 'أحداث المهام')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 rounded p-3">
                                <i data-feather="activity" class="text-primary" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h1 class="h3 mb-1 text-dark">أحداث المهام</h1>
                                <p class="text-muted mb-0">سجل جميع أحداث وتحديثات المهام</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('task-events.export') }}" class="btn btn-outline-success">
                                <i data-feather="download" class="me-2" style="width: 16px; height: 16px;"></i>
                                تصدير البيانات
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">إجمالي المهام</p>
                                    <p class="h4 mb-0 text-dark">{{ $stats['total_tasks'] }}</p>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <i data-feather="list" class="text-primary" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">مكتملة اليوم</p>
                                    <p class="h4 mb-0 text-success">{{ $stats['completed_today'] }}</p>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded p-2">
                                    <i data-feather="check-circle" class="text-success" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">متأخرة</p>
                                    <p class="h4 mb-0 text-danger">{{ $stats['overdue_tasks'] }}</p>
                                </div>
                                <div class="bg-danger bg-opacity-10 rounded p-2">
                                    <i data-feather="clock" class="text-danger" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">مستحقة هذا الأسبوع</p>
                                    <p class="h4 mb-0 text-warning">{{ $stats['due_this_week'] }}</p>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <i data-feather="calendar" class="text-warning" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">مكتملة هذا الشهر</p>
                                    <p class="h4 mb-0 text-info">{{ $stats['completed_this_month'] }}</p>
                                </div>
                                <div class="bg-info bg-opacity-10 rounded p-2">
                                    <i data-feather="bar-chart-2" class="text-info" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark">فلاتر البحث</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('task-events.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="project_id" class="form-label">المشروع</label>
                            <select name="project_id" id="project_id" class="form-select">
                                <option value="">جميع المشاريع</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="status" class="form-label">الحالة</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">جميع الحالات</option>
                                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جديدة</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>منجزة</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="priority" class="form-label">الأولوية</label>
                            <select name="priority" id="priority" class="form-select">
                                <option value="">جميع الأولويات</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">من تاريخ</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">إلى تاريخ</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-feather="search" style="width: 16px; height: 16px;"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tasks List -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark">قائمة المهام وأحداثها</h5>
                </div>
                
                @if($tasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>المهمة</th>
                                    <th>المشروع</th>
                                    <th>الحالة</th>
                                    <th>الأولوية</th>
                                    <th>الموعد النهائي</th>
                                    <th>المكلفون</th>
                                    <th>آخر تحديث</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : ($task->priority == 'medium' ? 'info' : 'secondary')) }} bg-opacity-10 rounded p-2 me-3">
                                                <i data-feather="list" class="text-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : ($task->priority == 'medium' ? 'info' : 'secondary')) }}" style="width: 16px; height: 16px;"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $task->title }}</h6>
                                                <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($task->project)
                                            <span class="badge bg-primary">{{ $task->project->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'new' => 'secondary',
                                                'in_progress' => 'primary',
                                                'pending' => 'warning',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusLabels = [
                                                'new' => 'جديدة',
                                                'in_progress' => 'قيد التنفيذ',
                                                'pending' => 'معلقة',
                                                'completed' => 'منجزة',
                                                'cancelled' => 'ملغاة'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$task->status] ?? $task->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $priorityColors = [
                                                'urgent' => 'danger',
                                                'high' => 'warning',
                                                'medium' => 'info',
                                                'low' => 'secondary'
                                            ];
                                            $priorityLabels = [
                                                'urgent' => 'عاجلة',
                                                'high' => 'عالية',
                                                'medium' => 'متوسطة',
                                                'low' => 'منخفضة'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $priorityColors[$task->priority] ?? 'secondary' }}">
                                            {{ $priorityLabels[$task->priority] ?? $task->priority }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($task->deadline)
                                            @php
                                                $isOverdue = $task->deadline < now() && $task->status != 'completed';
                                            @endphp
                                            <span class="{{ $isOverdue ? 'text-danger' : 'text-muted' }}">
                                                {{ $task->deadline->format('Y-m-d') }}
                                                @if($isOverdue)
                                                    <i data-feather="alert-triangle" class="ms-1" style="width: 14px; height: 14px;"></i>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->assignments->count() > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($task->assignments->take(3) as $assignment)
                                                    <span class="badge bg-light text-dark">{{ $assignment->user->name }}</span>
                                                @endforeach
                                                @if($task->assignments->count() > 3)
                                                    <span class="badge bg-light text-dark">+{{ $task->assignments->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">غير مكلفة</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $task->updated_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('task-events.show', $task) }}" class="btn btn-sm btn-outline-primary">
                                                <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                            </a>
                                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-info">
                                                <i data-feather="external-link" style="width: 14px; height: 14px;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer bg-white border-top">
                        {{ $tasks->links() }}
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <i data-feather="inbox" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                        <h5 class="text-muted">لا توجد مهام</h5>
                        <p class="text-muted">لم يتم العثور على مهام تطابق معايير البحث</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 