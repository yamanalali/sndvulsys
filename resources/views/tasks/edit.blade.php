@extends('layouts.master')
@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-10 col-lg-8">
        <div class="card user-card">
            <div class="card-header bg-c-blue text-white d-flex align-items-center">
                <i class="feather icon-edit mr-2" style="font-size:24px;"></i>
                <h4 class="mb-0">تعديل المهمة: {{ $task->title }}</h4>
            </div>
            <div class="card-block">
                @if(session('success'))
                    <div class="alert alert-success text-center">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('tasks.update', $task->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>العنوان <span class="text-danger">*</span></label>
                            <input name="title" type="text" class="form-control" required value="{{ old('title', $task->title) }}" placeholder="مثال: تطوير صفحة الهبوط">
                        </div>
                        <div class="form-group col-md-6">
                            <label>المشروع <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-control" required>
                                <option value="">اختر المشروع</option>
                                @foreach(\App\Models\Project::all() as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>تاريخ البدء</label>
                            <input name="start_date" type="date" class="form-control" value="{{ old('start_date', $task->start_date ? $task->start_date->format('Y-m-d') : '') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>تاريخ الانتهاء <span class="text-danger">*</span></label>
                            <input name="deadline" type="date" class="form-control" required value="{{ old('deadline', $task->deadline ? $task->deadline->format('Y-m-d') : '') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>الحالة <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="new" {{ old('status', $task->status) == 'new' ? 'selected' : '' }}>جديدة</option>
                                <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>معلقة</option>
                                <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>منجزة</option>
                                <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>الأولوية</label>
                            <select name="priority" class="form-control">
                                <option value="low" {{ old('priority', $task->priority ?? '') == 'low' ? 'selected' : '' }}>منخفضة</option>
                                <option value="medium" {{ old('priority', $task->priority ?? 'medium') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                <option value="high" {{ old('priority', $task->priority ?? '') == 'high' ? 'selected' : '' }}>عالية</option>
                                <option value="urgent" {{ old('priority', $task->priority ?? '') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label>الوصف</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="اكتب تفاصيل المهمة هنا...">{{ old('description', $task->description) }}</textarea>
                        </div>
                        <div class="form-group col-md-12">
                            <label>المكلفون (Assignees)</label>
                            <select name="user_ids[]" class="form-control" multiple>
                                @php
                                    $assignedIds = $task->assignments->pluck('user_id')->toArray();
                                @endphp
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" {{ in_array($user->id, $assignedIds) ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">يمكنك اختيار أكثر من مكلف</small>
                            @if($task->assignments->count() > 0)
                                <div class="mt-2">
                                    <span class="text-sm font-semibold">المكلفون الحاليون:</span>
                                    <div class="mt-1">
                                        @foreach($task->assignments as $assignment)
                                            <span class="badge badge-primary mr-1">{{ $assignment->user->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('tasks.index') }}" class="btn btn-light mr-2"><i class="feather icon-x"></i> إلغاء</a>
                        <button type="submit" class="btn btn-success"><i class="feather icon-check"></i> حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 