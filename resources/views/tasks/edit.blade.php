@extends('layouts.master')
@section('content')
<main class="main-content w-full px-[var(--margin-x)] pb-8 bg-gradient-to-br from-blue-50 via-white to-slate-100 min-h-[80vh]" dir="rtl">
    <div class="max-w-2xl mx-auto card p-10 bg-white shadow-2xl rounded-3xl border border-slate-200 mt-12 flex flex-col gap-8">
        <div class="flex items-center gap-2 mb-4">
            <div class="bg-primary/10 rounded-full p-3">
                <svg class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13h3l8-8a2.828 2.828 0 00-4-4l-8 8v3zm0 0v3a2 2 0 002 2h3" /></svg>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-800">تعديل المهمة</h2>
            <a href="{{ route('tasks.index') }}" class="ml-auto bg-slate-100 text-slate-600 px-4 py-2 rounded-lg font-bold hover:bg-slate-200 transition flex items-center gap-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                رجوع
            </a>
        </div>
        @if(session('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-800 text-center text-base font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-800 text-sm">
                <ul class="list-disc pr-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('tasks.update', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-1">العنوان <span class="text-red-500">*</span></label>
                    <input name="title" type="text" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary/30 text-lg bg-blue-50 placeholder:text-slate-400" required value="{{ old('title', $task->title) }}" placeholder="مثال: تطوير صفحة الهبوط">
                </div>
                <div>
                    <label class="block font-semibold mb-1">الموضوع (Subject)</label>
                    <input name="subject" type="text" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50 placeholder:text-slate-400" value="{{ old('subject', $task->subject ?? '') }}" placeholder="مثال: مشروع الموقع الجديد">
                </div>
                <div>
                    <label class="block font-semibold mb-1">تاريخ البدء</label>
                    <input name="startdate" type="date" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50" value="{{ old('startdate', $task->startdate ?? '') }}">
                </div>
                <div>
                    <label class="block font-semibold mb-1">تاريخ الانتهاء <span class="text-red-500">*</span></label>
                    <input name="deadline" type="date" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50" required value="{{ old('deadline', $task->deadline ? $task->deadline->format('Y-m-d') : '') }}">
                </div>
                <div>
                    <label class="block font-semibold mb-1">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50" required>
                        <option value="new" {{ old('status', $task->status) == 'new' ? 'selected' : '' }}>جديدة</option>
                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>منجزة</option>
                        <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">الأولوية</label>
                    <select name="priority" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50">
                        <option value="low" {{ old('priority', $task->priority ?? '') == 'low' ? 'selected' : '' }}>منخفضة</option>
                        <option value="medium" {{ old('priority', $task->priority ?? 'medium') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="high" {{ old('priority', $task->priority ?? '') == 'high' ? 'selected' : '' }}>عالية</option>
                        <option value="urgent" {{ old('priority', $task->priority ?? '') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">تكرار المهمة</label>
                    <select name="repeat_every" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50">
                        <option value="" {{ old('repeat_every', $task->repeat_every ?? '') == '' ? 'selected' : '' }}>بدون تكرار</option>
                        <option value="1-week" {{ old('repeat_every', $task->repeat_every ?? '') == '1-week' ? 'selected' : '' }}>أسبوع</option>
                        <option value="2-week" {{ old('repeat_every', $task->repeat_every ?? '') == '2-week' ? 'selected' : '' }}>أسبوعين</option>
                        <option value="1-month" {{ old('repeat_every', $task->repeat_every ?? '') == '1-month' ? 'selected' : '' }}>شهر</option>
                        <option value="2-month" {{ old('repeat_every', $task->repeat_every ?? '') == '2-month' ? 'selected' : '' }}>شهرين</option>
                        <option value="3-month" {{ old('repeat_every', $task->repeat_every ?? '') == '3-month' ? 'selected' : '' }}>3 أشهر</option>
                        <option value="6-month" {{ old('repeat_every', $task->repeat_every ?? '') == '6-month' ? 'selected' : '' }}>6 أشهر</option>
                        <option value="1-year" {{ old('repeat_every', $task->repeat_every ?? '') == '1-year' ? 'selected' : '' }}>سنة</option>
                        <option value="custom" {{ old('repeat_every', $task->repeat_every ?? '') == 'custom' ? 'selected' : '' }}>مخصص</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-semibold mb-1">الوصف</label>
                <textarea name="description" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 min-h-[90px] focus:border-primary text-lg bg-blue-50 placeholder:text-slate-400" placeholder="اكتب تفاصيل المهمة هنا...">{{ old('description', $task->description) }}</textarea>
            </div>
            <div class="flex flex-wrap gap-8 mb-4">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="is_public" class="form-checkbox rounded border-slate-400 text-primary focus:ring-primary" {{ old('is_public', $task->is_public ?? false) ? 'checked' : '' }}>
                    <span>مهمة عامة (Public)</span>
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="billable" class="form-checkbox rounded border-slate-400 text-primary focus:ring-primary" {{ old('billable', $task->billable ?? true) ? 'checked' : '' }}>
                    <span>قابلة للفوترة (Billable)</span>
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="visible_to_client" class="form-checkbox rounded border-slate-400 text-primary focus:ring-primary" {{ old('visible_to_client', $task->visible_to_client ?? false) ? 'checked' : '' }}>
                    <span>مرئية للعميل</span>
                </label>
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">المرفقات</label>
                <input type="file" name="attachments[]" multiple class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">المكلفون (Assignees)</label>
                <select name="user_ids[]" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50" multiple required>
                    @php
                        $assignedIds = $task->assignments->pluck('user_id')->toArray();
                    @endphp
                    @foreach(\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}" {{ in_array($user->id, $assignedIds) ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-slate-400">يمكنك اختيار أكثر من مكلف</span>
            </div>
            <div>
                <label class="block font-semibold mb-1">الوسوم (Tags)</label>
                <input name="tags" type="text" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50 placeholder:text-slate-400" placeholder="أدخل الوسوم مفصولة بفواصل" value="{{ old('tags', $task->tags ?? '') }}">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Checklist</label>
                <textarea name="checklist" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 min-h-[48px] focus:border-primary text-lg bg-blue-50 placeholder:text-slate-400" placeholder="عنصر 1\nعنصر 2\nعنصر 3">{{ old('checklist', $task->checklist ?? '') }}</textarea>
            </div>
            <div class="flex justify-between gap-4 mt-10">
                <a href="{{ route('tasks.index') }}" class="w-1/2 py-3 rounded-xl bg-slate-200 text-slate-700 text-lg font-semibold text-center hover:bg-slate-300 transition flex items-center justify-center gap-2">
                    <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    إلغاء
                </a>
                <button type="submit" class="w-1/2 py-3 rounded-xl bg-primary text-white text-lg font-bold flex items-center justify-center gap-2 shadow-lg hover:bg-primary/90 transition">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</main>
@endsection 