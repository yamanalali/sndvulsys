@extends('layouts.master')

@section('content')
<main class="flex-1 flex items-center justify-center pt-10 pb-8 px-4">
    <div class="max-w-4xl w-full mx-auto relative z-10 flex flex-col justify-center items-center">
        <div class="bg-white dark:bg-navy-750 rounded-2xl shadow-soft dark:shadow-soft-dark overflow-hidden w-full">
            <!-- Header -->
            <div class="px-8 py-6 border-b border-slate-100 dark:border-navy-600 bg-gradient-to-r from-slate-50 to-blue-50 dark:from-navy-700 dark:to-navy-600">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-primary to-purple-600 rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800 dark:text-navy-100">إنشاء مشروع جديد</h1>
                            <p class="text-slate-600 dark:text-navy-300 mt-1">أضف مشروعاً جديداً مع تفاصيله الكاملة</p>
                        </div>
                    </div>
                    <a href="{{ route('projects.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-600 text-white rounded-xl hover:bg-slate-700 transition-all duration-300 shadow-soft">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        العودة للمشاريع
                    </a>
                </div>
            </div>

            <!-- Tabs -->
            <div class="px-8 pt-6">
                <ul class="flex border-b border-slate-200 dark:border-navy-600 mb-6" id="projectTabs" role="tablist">
                    <li class="mr-2">
                        <button class="inline-block py-2 px-4 text-primary border-b-2 border-primary font-semibold focus:outline-none" id="tab_project_tab" data-tab="tab_project" type="button" role="tab" aria-controls="tab_project" aria-selected="true">المشروع</button>
                    </li>
                    <li>
                        <button class="inline-block py-2 px-4 text-slate-500 dark:text-navy-200 border-b-2 border-transparent hover:text-primary hover:border-primary focus:outline-none" id="tab_settings_tab" data-tab="tab_settings" type="button" role="tab" aria-controls="tab_settings" aria-selected="false">إعدادات المشروع</button>
                    </li>
                </ul>
            </div>

            <form action="{{ route('projects.store') }}" method="POST" class="p-8 pt-0">
                @csrf
                <div id="tab_project" class="tab-pane">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Project Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">
                                اسم المشروع <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full px-4 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100 placeholder-slate-400 dark:placeholder-navy-300" placeholder="أدخل اسم المشروع" required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-2 flex items-center gap-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">وصف المشروع</label>
                            <textarea id="description" name="description" rows="4" class="w-full px-4 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100 placeholder-slate-400 dark:placeholder-navy-300 resize-none" placeholder="أدخل وصفاً مفصلاً للمشروع وأهدافه">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-2 flex items-center gap-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">حالة المشروع <span class="text-red-500">*</span></label>
                            <select id="status" name="status" class="w-full px-4 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100 appearance-none" required>
                                <option value="">اختر حالة المشروع</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>معلق</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-2 flex items-center gap-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Manager -->
                        <div>
                            <label for="manager_id" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">مدير المشروع</label>
                            <select id="manager_id" name="manager_id" class="w-full px-4 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100 appearance-none">
                                <option value="">اختر مدير المشروع</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('manager_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('manager_id')
                                <p class="text-red-500 text-sm mt-2 flex items-center gap-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">تاريخ البداية</label>
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" class="w-full px-4 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100">
                            @error('start_date')
                                <p class="text-red-500 text-sm mt-2 flex items-center gap-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">تاريخ الانتهاء</label>
                            <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" class="w-full px-4 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100">
                            @error('end_date')
                                <p class="text-red-500 text-sm mt-2 flex items-center gap-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div id="tab_settings" class="tab-pane hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Project Members -->
                        <div class="md:col-span-2">
                            <label for="project_members" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">أعضاء الفريق</label>
                            <select id="project_members" name="project_members[]" class="w-full px-4 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100" multiple>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ (collect(old('project_members'))->contains($user->id)) ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Project Settings (checkboxes) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">إعدادات المشروع</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="settings[view_tasks]" class="form-checkbox text-primary" checked>
                                        <span class="ml-2">السماح للعميل بعرض المهام</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="settings[create_tasks]" class="form-checkbox text-primary" checked>
                                        <span class="ml-2">السماح للعميل بإنشاء مهام</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="settings[edit_tasks]" class="form-checkbox text-primary" checked>
                                        <span class="ml-2">السماح للعميل بتعديل المهام</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="settings[comment_on_tasks]" class="form-checkbox text-primary" checked>
                                        <span class="ml-2">السماح للعميل بالتعليق على المهام</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="settings[view_task_attachments]" class="form-checkbox text-primary" checked>
                                        <span class="ml-2">السماح للعميل بعرض مرفقات المهام</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="settings[upload_files]" class="form-checkbox text-primary" checked>
                                        <span class="ml-2">السماح للعميل برفع ملفات</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="settings[view_gantt]" class="form-checkbox text-primary" checked>
                                        <span class="ml-2">السماح للعميل بعرض مخطط جانت</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="settings[view_team_members]" class="form-checkbox text-primary" checked>
                                        <span class="ml-2">السماح للعميل بعرض أعضاء الفريق</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-4 mt-10 pt-8 border-t border-slate-100 dark:border-navy-600">
                    <a href="{{ route('projects.index') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 border border-slate-300 dark:border-navy-600 text-slate-700 dark:text-navy-300 rounded-xl hover:bg-slate-50 dark:hover:bg-navy-700 transition-all duration-300 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-r from-primary to-primary/90 text-white rounded-xl hover:from-primary/90 hover:to-primary transition-all duration-300 shadow-soft font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        إنشاء المشروع
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
<script>
    // تبويبات بسيطة بدون مكتبة خارجية
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('[data-tab]');
        const panes = document.querySelectorAll('.tab-pane');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('text-primary', 'border-primary', 'font-semibold'));
                this.classList.add('text-primary', 'border-primary', 'font-semibold');
                panes.forEach(pane => pane.classList.add('hidden'));
                document.getElementById(this.getAttribute('data-tab')).classList.remove('hidden');
            });
        });
    });
</script>
@endsection 