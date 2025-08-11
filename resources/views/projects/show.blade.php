@extends('layouts.master')

@section('content')
<div class="page-wrapper" dir="rtl">
    <div class="page-content">
        <div class="container-fluid">
            <!-- Project Header -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <i class="feather-folder text-white" style="font-size: 24px;"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h2 class="mb-1">{{ $project->name }}</h2>
                                            <span class="badge {{ $project->status_color }} fs-6 px-3 py-2">
                                                {{ $project->status_label }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($project->description)
                                        <p class="text-muted fs-5 mb-0">{{ $project->description }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
                                        <a href="{{ route('projects.edit', $project) }}" 
                                           class="btn btn-primary">
                                            <i class="feather-edit me-2"></i>
                                            تعديل المشروع
                                        </a>
                                        <a href="{{ route('projects.index') }}" 
                                           class="btn btn-secondary">
                                            <i class="feather-arrow-right me-2"></i>
                                            العودة للمشاريع
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Stats -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="feather-list" style="font-size: 32px;"></i>
                            </div>
                            <h3 class="mb-1">{{ $project->tasks->count() }}</h3>
                            <p class="mb-0">إجمالي المهام</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="feather-check-circle" style="font-size: 32px;"></i>
                            </div>
                            <h3 class="mb-1">{{ $project->progress }}%</h3>
                            <p class="mb-0">التقدم العام</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="feather-check" style="font-size: 32px;"></i>
                            </div>
                            <h3 class="mb-1">{{ $project->tasks->where('status', 'completed')->count() }}</h3>
                            <p class="mb-0">المهام المكتملة</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="feather-clock" style="font-size: 32px;"></i>
                            </div>
                            <h3 class="mb-1">{{ $project->tasks->where('status', 'in_progress')->count() }}</h3>
                            <p class="mb-0">قيد التنفيذ</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">التقدم العام</h5>
                                <span class="badge bg-primary fs-6">{{ $project->progress }}%</span>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $project->progress }}%" 
                                     aria-valuenow="{{ $project->progress }}" 
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Details and Team -->
            <div class="row mb-4">
                <div class="col-lg-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-info me-2"></i>
                                تفاصيل المشروع
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($project->manager)
                                <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                                    <div class="me-3">
                                        <i class="feather-user text-primary" style="font-size: 20px;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">مدير المشروع</div>
                                        <div class="text-muted">{{ $project->manager->name }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($project->start_date)
                                <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                                    <div class="me-3">
                                        <i class="feather-calendar text-success" style="font-size: 20px;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">تاريخ البداية</div>
                                        <div class="text-muted">{{ $project->start_date->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($project->end_date)
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="me-3">
                                        <i class="feather-calendar text-danger" style="font-size: 20px;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">تاريخ الانتهاء</div>
                                        <div class="text-muted">{{ $project->end_date->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-users me-2"></i>
                                معلومات إضافية
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($project->teamMembers->count() > 0)
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">أعضاء الفريق</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($project->teamMembers as $member)
                                            <span class="badge bg-primary fs-6 px-3 py-2">
                                                {{ $member->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div>
                                <h6 class="fw-bold mb-3">حالة المهام</h6>
                                <div class="space-y-2">
                                    @php
                                        $statusCounts = $project->tasks->groupBy('status')->map->count();
                                    @endphp
                                    @foreach(['new', 'in_progress', 'completed', 'cancelled'] as $status)
                                        @if(isset($statusCounts[$status]))
                                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded mb-2">
                                                <span class="text-muted">
                                                    {{ match($status) {
                                                        'new' => 'جديدة',
                                                        'in_progress' => 'قيد التنفيذ',
                                                        'completed' => 'مكتملة',
                                                        'cancelled' => 'ملغاة',
                                                        default => $status
                                                    } }}
                                                </span>
                                                <span class="badge bg-secondary fs-6">{{ $statusCounts[$status] }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Tasks -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="feather-list me-2"></i>
                                    مهام المشروع
                                </h5>
                                <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" 
                                   class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>
                                    إضافة مهمة جديدة
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المهمة</th>
                                            <th>الحالة</th>
                                            <th>المكلفون</th>
                                            <th>تاريخ الانتهاء</th>
                                            <th>إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($project->tasks as $task)
                                            @php
                                                $statusColor = match($task->status) {
                                                    'completed' => 'bg-success',
                                                    'pending' => 'bg-warning',
                                                    'in_progress' => 'bg-info',
                                                    'cancelled' => 'bg-secondary',
                                                    default => ($task->deadline && $task->deadline < now() && $task->status != 'completed' ? 'bg-danger' : 'bg-primary'),
                                                };
                                                $statusLabel = match($task->status) {
                                                    'completed' => 'مكتملة',
                                                    'pending' => 'معلقة',
                                                    'in_progress' => 'قيد التنفيذ',
                                                    'cancelled' => 'ملغاة',
                                                    default => ($task->deadline && $task->deadline < now() && $task->status != 'completed' ? 'متأخرة' : 'جديدة'),
                                                };
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $task->title }}</div>
                                                        @if($task->description)
                                                            <div class="text-muted small mt-1">{{ Str::limit($task->description, 50) }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $statusColor }} fs-6 px-3 py-2">
                                                        {{ $statusLabel }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @forelse($task->assignments as $assignment)
                                                            <span class="badge bg-primary fs-6 px-2 py-1">
                                                                {{ $assignment->user->name }}
                                                                @if($assignment->assigned_at)
                                                                    <div class="small text-white-50 mt-1">
                                                                        ({{ $assignment->assigned_at->format('Y-m-d H:i') }})
                                                                    </div>
                                                                @endif
                                                            </span>
                                                        @empty
                                                            <span class="text-muted small">غير مخصص</span>
                                                        @endforelse
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($task->deadline)
                                                        <span class="{{ $task->deadline < now() && $task->status != 'completed' ? 'text-danger' : 'text-muted' }} fw-medium">
                                                            {{ $task->deadline->format('Y-m-d') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">غير محدد</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('tasks.show', $task->id) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="feather-eye me-1"></i>
                                                        تفاصيل
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="text-center">
                                                        <div class="mb-4">
                                                            <i class="feather-list text-muted" style="font-size: 64px;"></i>
                                                        </div>
                                                        <h4 class="text-muted mb-3">لا توجد مهام في هذا المشروع</h4>
                                                        <p class="text-muted mb-4">ابدأ بإضافة مهمة جديدة للمشروع لبدء العمل عليه</p>
                                                        <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" 
                                                           class="btn btn-primary">
                                                            <i class="feather-plus me-2"></i>
                                                            إضافة مهمة جديدة
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 