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
                            <label>المشروع <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-control" required>
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
                            <input name="start_date" type="date" class="form-control" value="{{ old('start_date') }}">
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
                            <select name="user_ids[]" class="form-control" multiple>
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
                        <!-- Recurring Task Options -->
                        <div class="form-group col-md-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_recurring" class="form-check-input" id="is_recurring" {{ old('is_recurring') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_recurring">
                                    <strong>مهمة متكررة</strong>
                                    <small class="text-muted d-block">جعل هذه المهمة تتكرر تلقائياً حسب الجدولة المحددة</small>
                                </label>
                            </div>
                        </div>

                        <div id="recurring-options" style="display: none;">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>نمط التكرار</label>
                                    <select name="recurrence_pattern" class="form-control">
                                        <option value="daily" {{ old('recurrence_pattern') === 'daily' ? 'selected' : '' }}>يومياً</option>
                                        <option value="weekly" {{ old('recurrence_pattern') === 'weekly' ? 'selected' : '' }}>أسبوعياً</option>
                                        <option value="monthly" {{ old('recurrence_pattern') === 'monthly' ? 'selected' : '' }}>شهرياً</option>
                                        <option value="yearly" {{ old('recurrence_pattern') === 'yearly' ? 'selected' : '' }}>سنوياً</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>فترة التكرار</label>
                                    <input type="number" name="recurrence_config[interval]" class="form-control" min="1" value="{{ old('recurrence_config.interval', 1) }}">
                                    <small class="text-muted">كل كم من الوحدة المحددة (مثال: كل 2 أسبوع)</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>تاريخ بداية التكرار</label>
                                    <input type="date" name="recurrence_start_date" class="form-control" value="{{ old('recurrence_start_date') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>تاريخ انتهاء التكرار (اختياري)</label>
                                    <input type="date" name="recurrence_end_date" class="form-control" value="{{ old('recurrence_end_date') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>العدد الأقصى للتكرارات (اختياري)</label>
                                    <input type="number" name="recurrence_max_occurrences" class="form-control" min="1" value="{{ old('recurrence_max_occurrences') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" name="recurring_active" class="form-check-input" id="recurring_active" value="1" {{ old('recurring_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recurring_active">تفعيل التكرار فوراً</label>
                                    </div>
                                </div>
                            </div>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle recurring options
    $('#is_recurring').on('change', function() {
        if ($(this).is(':checked')) {
            $('#recurring-options').show();
        } else {
            $('#recurring-options').hide();
        }
    });

    // Initial check
    if ($('#is_recurring').is(':checked')) {
        $('#recurring-options').show();
    }
});
</script>
@endpush

@endsection 