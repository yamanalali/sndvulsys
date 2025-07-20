@extends('layouts.master')
@section('content')
<main class="main-content w-full px-[var(--margin-x)] pb-8 bg-gradient-to-br from-blue-50 via-white to-slate-100 min-h-[80vh]" dir="rtl">
    <div class="max-w-3xl mx-auto card p-10 bg-white shadow-2xl rounded-3xl border border-slate-200 mt-12 relative">
        <div class="flex flex-col items-center mb-8">
            <div class="flex items-center gap-2 mb-2">
                <svg class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <h2 class="text-3xl font-extrabold text-slate-800">إضافة مهمة جديدة</h2>
            </div>
            <p class="text-slate-500 text-base">املأ البيانات التالية لإنشاء مهمة جديدة في النظام</p>
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
        <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-1">المشروع</label>
                    <select name="project_id" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50">
                        <option value="">اختر المشروع</option>
                        @foreach(\App\Models\Project::all() as $project)
                            <option value="{{ $project->id }}" 
                                    {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">العنوان <span class="text-red-500">*</span></label>
                    <input name="title" type="text" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary/30 text-lg bg-blue-50 placeholder:text-slate-400" required value="{{ old('title') }}" placeholder="مثال: تطوير صفحة الهبوط">
                </div>
                <div>
                    <label class="block font-semibold mb-1">الموضوع (Subject)</label>
                    <input name="subject" type="text" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50 placeholder:text-slate-400" value="{{ old('subject') }}" placeholder="مثال: مشروع الموقع الجديد">
                </div>
                <div>
                    <label class="block font-semibold mb-1">تاريخ البدء</label>
                    <input name="startdate" type="date" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50" value="{{ old('startdate') }}">
                </div>
                <div>
                    <label class="block font-semibold mb-1">تاريخ الانتهاء <span class="text-red-500">*</span></label>
                    <input name="deadline" type="date" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50" required value="{{ old('deadline') }}">
                </div>
                <div>
                    <label class="block font-semibold mb-1">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50" required>
                        <option value="new" {{ old('status') == 'new' ? 'selected' : '' }}>جديدة</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>منجزة</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">الأولوية</label>
                    <select name="priority" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50">
                        <option value="low">منخفضة</option>
                        <option value="medium" selected>متوسطة</option>
                        <option value="high">عالية</option>
                        <option value="urgent">عاجلة</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">تكرار المهمة</label>
                    <select name="repeat_every" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50">
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
            </div>
            <div>
                <label class="block font-semibold mb-1">الوصف</label>
                <textarea name="description" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 min-h-[90px] focus:border-primary text-lg bg-blue-50 placeholder:text-slate-400" placeholder="اكتب تفاصيل المهمة هنا...">{{ old('description') }}</textarea>
            </div>
            <div class="flex flex-wrap gap-8 mb-4">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="is_public" class="form-checkbox rounded border-slate-400 text-primary focus:ring-primary">
                    <span>مهمة عامة (Public)</span>
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="billable" class="form-checkbox rounded border-slate-400 text-primary focus:ring-primary" checked>
                    <span>قابلة للفوترة (Billable)</span>
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="visible_to_client" class="form-checkbox rounded border-slate-400 text-primary focus:ring-primary">
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
                    @foreach(\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-slate-400">يمكنك اختيار أكثر من مكلف</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-1">الوسوم (Tags)</label>
                    <input name="tags" type="text" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 focus:border-primary text-lg bg-blue-50 placeholder:text-slate-400" placeholder="أدخل الوسوم مفصولة بفواصل">
                </div>
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Checklist</label>
                <textarea name="checklist" class="form-input w-full rounded-xl border-2 border-primary/30 px-4 py-2 min-h-[48px] focus:border-primary text-lg bg-blue-50 placeholder:text-slate-400" placeholder="عنصر 1\nعنصر 2\nعنصر 3"></textarea>
            </div>
            <div class="flex justify-between gap-4 mt-10">
                <a href="{{ route('tasks.index') }}" class="w-1/2 py-3 rounded-xl bg-slate-200 text-slate-700 text-lg font-semibold text-center hover:bg-slate-300 transition flex items-center justify-center gap-2">
                    <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    إلغاء
                </a>
                <button type="submit" class="w-1/2 py-3 rounded-xl bg-primary text-white text-lg font-bold flex items-center justify-center gap-2 shadow-lg hover:bg-primary/90 transition">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    حفظ المهمة
                </button>
            </div>
        </form>
    </div>
</main>
@endsection 