@extends('layouts.master')

@section('content')
<div class="flex justify-center bg-gradient-to-br from-slate-50 to-blue-50 dark:from-navy-900 dark:to-navy-800 min-h-screen" dir="rtl">
    <div class="max-w-4xl w-full mx-auto px-4 py-8 my-auto relative z-10 flex flex-col justify-center items-center">
        <div class="bg-white dark:bg-navy-750 rounded-2xl shadow-soft dark:shadow-soft-dark overflow-hidden">
            <!-- Header -->
            <div class="px-8 py-6 border-b border-slate-100 dark:border-navy-600 bg-gradient-to-r from-slate-50 to-blue-50 dark:from-navy-700 dark:to-navy-600">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800 dark:text-navy-100">تعديل المشروع</h1>
                            <p class="text-slate-600 dark:text-navy-300 mt-1">تحديث تفاصيل المشروع "{{ $project->name }}"</p>
                        </div>
                    </div>
                    <a href="{{ route('projects.show', $project) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-600 text-white rounded-xl hover:bg-slate-700 transition-all duration-300 shadow-soft">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        العودة للمشروع
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('projects.update', $project) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Project Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">
                            اسم المشروع <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $project->name) }}"
                                   class="w-full pl-4 pr-12 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100 placeholder-slate-400 dark:placeholder-navy-300"
                                   placeholder="أدخل اسم المشروع"
                                   required>
                        </div>
                        @error('name')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">
                            وصف المشروع
                        </label>
                        <div class="relative">
                            <textarea id="description" 
                                      name="description" 
                                      rows="4"
                                      class="w-full px-4 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100 placeholder-slate-400 dark:placeholder-navy-300 resize-none"
                                      placeholder="أدخل وصفاً مفصلاً للمشروع وأهدافه">{{ old('description', $project->description) }}</textarea>
                        </div>
                        @error('description')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">
                            حالة المشروع <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <select id="status" 
                                    name="status" 
                                    class="w-full pl-4 pr-12 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100 appearance-none"
                                    required>
                                <option value="">اختر حالة المشروع</option>
                                <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>معلق</option>
                                <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        @error('status')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Manager -->
                    <div>
                        <label for="manager_id" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">
                            مدير المشروع
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <select id="manager_id" 
                                    name="manager_id" 
                                    class="w-full pl-4 pr-12 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100 appearance-none">
                                <option value="">اختر مدير المشروع</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('manager_id', $project->manager_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        @error('manager_id')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">
                            تاريخ البداية
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input type="date" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}"
                                   class="w-full pl-4 pr-12 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100">
                        </div>
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">
                            تاريخ الانتهاء
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input type="date" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}"
                                   class="w-full pl-4 pr-12 py-3.5 border border-slate-300 dark:border-navy-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 bg-white dark:bg-navy-700 text-slate-800 dark:text-navy-100">
                        </div>
                        @error('end_date')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-4 mt-10 pt-8 border-t border-slate-100 dark:border-navy-600">
                    <a href="{{ route('projects.show', $project) }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 border border-slate-300 dark:border-navy-600 text-slate-700 dark:text-navy-300 rounded-xl hover:bg-slate-50 dark:hover:bg-navy-700 transition-all duration-300 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-soft font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        تحديث المشروع
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 