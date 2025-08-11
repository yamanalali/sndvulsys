@extends('layouts.app')

@section('title', 'الجدول الزمني للمهمة')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">
                    <i class="fas fa-clock text-primary"></i>
                    الجدول الزمني للمهمة
                </h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('volunteer.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('task-history.index') }}">تاريخ المهام</a></li>
                    <li class="breadcrumb-item active">الجدول الزمني</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Task Info -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4>{{ $task->title }}</h4>
                    <div class="task-meta">
                        <span class="badge bg-{{ $task->status_color }}">{{ $task->status_label }}</span>
                        <span class="badge bg-{{ $task->priority_color }}">{{ $task->priority_label }}</span>
                        @if($task->project)
                            <span class="text-muted">{{ $task->project->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>سجل التغييرات</h5>
                </div>
                <div class="card-body">
                    @if(count($timeline) > 0)
                        <div class="timeline">
                            @foreach($timeline as $record)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $record['color'] }}">
                                        <i class="{{ $record['icon'] }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>{{ $record['action_description'] }}</h6>
                                        <p>{{ $record['description'] }}</p>
                                        @if($record['field_name'])
                                            <div class="changes">
                                                <strong>{{ $record['field_name'] }}:</strong>
                                                {{ $record['old_value'] }} → {{ $record['new_value'] }}
                                            </div>
                                        @endif
                                        <small class="text-muted">
                                            {{ $record['user_name'] }} - {{ $record['time_ago'] }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-muted">لا توجد تغييرات مسجلة</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
}

.changes {
    background: white;
    padding: 8px;
    border-radius: 4px;
    margin: 10px 0;
}
</style>
@endpush 