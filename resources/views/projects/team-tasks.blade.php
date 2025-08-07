@extends('layouts.master')

@section('content')
<div class="page-wrapper" dir="rtl">
    <div class="page-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1">مهام فريقي</h2>
                                    <p class="text-muted mb-0">المهام في المشاريع التي أشارك فيها مع الفريق</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('projects.index') }}" 
                                       class="btn btn-secondary">
                                        <i class="feather-list me-2"></i>
                                        جميع المشاريع
                                    </a>
                                    <a href="{{ route('projects.my-projects') }}" 
                                       class="btn btn-primary">
                                        <i class="feather-folder me-2"></i>
                                        مشاريعي
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Summary -->
            @php
                $statusLabels = [
                    'new' => ['label' => 'جديدة', 'color' => 'bg-secondary'],
                    'in_progress' => ['label' => 'قيد التنفيذ', 'color' => 'bg-info'],
                    'testing' => ['label' => 'اختبار', 'color' => 'bg-warning'],
                    'awaiting_feedback' => ['label' => 'في انتظار التغذية الراجعة', 'color' => 'bg-warning'],
                    'completed' => ['label' => 'مكتملة', 'color' => 'bg-success'],
                ];
                $summary = [];
                foreach ($statusLabels as $status => $info) {
                    $summary[$status] = [
                        'count' => $teamTasks->where('status', $status)->count(),
                        'label' => $info['label'],
                        'color' => $info['color'],
                    ];
                }
            @endphp

            <div class="row mb-4">
                @foreach($summary as $item)
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <div class="card text-center {{ $item['color'] }} text-white">
                            <div class="card-body">
                                <h3 class="mb-1 fw-bold">{{ $item['count'] }}</h3>
                                <p class="mb-0 small">{{ $item['label'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tasks List -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-list me-2"></i>
                                قائمة المهام
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المشروع</th>
                                            <th>المهمة</th>
                                            <th>الحالة</th>
                                            <th>المكلفون</th>
                                            <th>تاريخ الانتهاء</th>
                                            <th>إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($teamTasks as $task)
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
                                                    @if($task->project)
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-primary rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                                                            <span class="fw-medium">{{ $task->project->name }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">بدون مشروع</span>
                                                    @endif
                                                </td>
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
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="text-center">
                                                        <div class="mb-4">
                                                            <i class="feather-list text-muted" style="font-size: 64px;"></i>
                                                        </div>
                                                        <h4 class="text-muted mb-3">لا توجد مهام في فريقك</h4>
                                                        <p class="text-muted mb-4">لم يتم العثور على مهام في المشاريع التي تشارك فيها</p>
                                                        <a href="{{ route('projects.index') }}" class="btn btn-primary">
                                                            <i class="feather-folder me-2"></i>
                                                            عرض المشاريع
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