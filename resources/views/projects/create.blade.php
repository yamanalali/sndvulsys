@extends('layouts.master')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-10 col-lg-8">
        <div class="card user-card">
            <div class="card-header bg-c-blue text-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="bg-info rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:48px;height:48px;">
                        <i class="feather icon-plus text-white" style="font-size:24px;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">إنشاء مشروع جديد</h4>
                        <small class="text-light">أضف مشروعاً جديداً مع تفاصيله الكاملة</small>
                    </div>
                </div>
                <a href="{{ route('projects.index') }}" class="btn btn-light"><i class="feather icon-arrow-left"></i> العودة للمشاريع</a>
            </div>
            <div class="card-block">
                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="name">اسم المشروع <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" placeholder="أدخل اسم المشروع" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="status">حالة المشروع <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="">اختر حالة المشروع</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>معلق</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                            @error('status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group col-md-12">
                            <label for="description">وصف المشروع</label>
                            <textarea id="description" name="description" rows="3" class="form-control" placeholder="أدخل وصفاً مفصلاً للمشروع وأهدافه">{{ old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="manager_id">مدير المشروع</label>
                            <select id="manager_id" name="manager_id" class="form-control">
                                <option value="">اختر مدير المشروع</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('manager_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('manager_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label for="start_date">تاريخ البداية</label>
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" class="form-control">
                            @error('start_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label for="end_date">تاريخ الانتهاء</label>
                            <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" class="form-control">
                            @error('end_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group col-md-12">
                            <label for="project_members">أعضاء الفريق</label>
                            <select id="project_members" name="project_members[]" class="form-control" multiple>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ (collect(old('project_members'))->contains($user->id)) ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label>إعدادات المشروع</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="settings[view_tasks]" class="form-check-input" id="view_tasks" checked>
                                        <label class="form-check-label" for="view_tasks">السماح للعميل بعرض المهام</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="settings[create_tasks]" class="form-check-input" id="create_tasks" checked>
                                        <label class="form-check-label" for="create_tasks">السماح للعميل بإنشاء مهام</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="settings[edit_tasks]" class="form-check-input" id="edit_tasks" checked>
                                        <label class="form-check-label" for="edit_tasks">السماح للعميل بتعديل المهام</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="settings[comment_on_tasks]" class="form-check-input" id="comment_on_tasks" checked>
                                        <label class="form-check-label" for="comment_on_tasks">السماح للعميل بالتعليق على المهام</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="settings[view_task_attachments]" class="form-check-input" id="view_task_attachments" checked>
                                        <label class="form-check-label" for="view_task_attachments">السماح للعميل بعرض مرفقات المهام</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="settings[upload_files]" class="form-check-input" id="upload_files" checked>
                                        <label class="form-check-label" for="upload_files">السماح للعميل برفع ملفات</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="settings[view_gantt]" class="form-check-input" id="view_gantt" checked>
                                        <label class="form-check-label" for="view_gantt">السماح للعميل بعرض مخطط جانت</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="settings[view_team_members]" class="form-check-input" id="view_team_members" checked>
                                        <label class="form-check-label" for="view_team_members">السماح للعميل بعرض أعضاء الفريق</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('projects.index') }}" class="btn btn-light mr-2"><i class="feather icon-x"></i> إلغاء</a>
                        <button type="submit" class="btn btn-success"><i class="feather icon-check"></i> إنشاء المشروع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 