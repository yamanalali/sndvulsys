@extends('layouts.master')
@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-10 col-lg-8">
        <div class="card user-card">
            <div class="card-header bg-c-blue text-white d-flex align-items-center">
                <i class="feather icon-plus mr-2" style="font-size:24px;"></i>
                <h4 class="mb-0">إضافة مهمة جديدة</h4>
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
                <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>المشروع</label>
                            <select name="project_id" class="form-control">
                                <option value="">اختر المشروع</option>
                                @foreach(\App\Models\Project::all() as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>العنوان <span class="text-danger">*</span></label>
                            <input name="title" type="text" class="form-control" required value="{{ old('title') }}" placeholder="مثال: تطوير صفحة الهبوط">
                        </div>
                        <div class="form-group col-md-6">
                            <label>الموضوع (Subject)</label>
                            <input name="subject" type="text" class="form-control" value="{{ old('subject') }}" placeholder="مثال: مشروع الموقع الجديد">
                        </div>
                        <div class="form-group col-md-3">
                            <label>تاريخ البدء</label>
                            <input name="startdate" type="date" class="form-control" value="{{ old('startdate') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>تاريخ الانتهاء <span class="text-danger">*</span></label>
                            <input name="deadline" type="date" class="form-control" required value="{{ old('deadline') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>الحالة <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="new" {{ old('status') == 'new' ? 'selected' : '' }}>جديدة</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>منجزة</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>الأولوية</label>
                            <select name="priority" class="form-control">
                                <option value="low">منخفضة</option>
                                <option value="medium" selected>متوسطة</option>
                                <option value="high">عالية</option>
                                <option value="urgent">عاجلة</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>تكرار المهمة</label>
                            <select name="repeat_every" class="form-control">
                                <option value="">بدون تكرار</option>
                                <option value="1-week">أسبوع</option>
                                <option value="2-week">أسبوعين</option>
                                <option value="1-month">شهر</option>
                                <option value="2-month">شهرين</option>
                                <option value="3-month">3 أشهر</option>
                                <option value="6-month">6 أشهر</option>
                                <option value="1-year">سنة</option>
                                <option value="custom">مخصص</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label>الوصف</label>
                            <textarea name="description" class="form-control" placeholder="اكتب تفاصيل المهمة هنا...">{{ old('description') }}</textarea>
                        </div>
                        <div class="form-group col-md-12">
                            <label>المرفقات</label>
                            <input type="file" name="attachments[]" multiple class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                            <label>المكلفون (Assignees)</label>
                            <select name="user_ids[]" class="form-control" multiple required>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">يمكنك اختيار أكثر من مكلف</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label>الوسوم (Tags)</label>
                            <input name="tags" type="text" class="form-control" placeholder="أدخل الوسوم مفصولة بفواصل">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Checklist</label>
                            <textarea name="checklist" class="form-control" placeholder="عنصر 1\nعنصر 2\nعنصر 3"></textarea>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" name="is_public" class="form-check-input" id="is_public">
                                <label class="form-check-label" for="is_public">مهمة عامة (Public)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" name="billable" class="form-check-input" id="billable" checked>
                                <label class="form-check-label" for="billable">قابلة للفوترة (Billable)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" name="visible_to_client" class="form-check-input" id="visible_to_client">
                                <label class="form-check-label" for="visible_to_client">مرئية للعميل</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('tasks.index') }}" class="btn btn-light mr-2"><i class="feather icon-x"></i> إلغاء</a>
                        <button type="submit" class="btn btn-success"><i class="feather icon-check"></i> حفظ المهمة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 