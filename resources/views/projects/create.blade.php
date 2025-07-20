@extends('layouts.master')

@section('content')
<div class="max-w-4xl mx-auto mt-10 px-4" dir="rtl">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">إنشاء مشروع جديد</h1>
                    <p class="text-slate-600 mt-1">أضف مشروعاً جديداً مع تفاصيله الكاملة</p>
                </div>
                <a href="{{ route('projects.index') }}" 
                   class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition">
                    العودة للمشاريع
                </a>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('projects.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Project Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        اسم المشروع <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                           placeholder="أدخل اسم المشروع"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                        وصف المشروع
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                              placeholder="أدخل وصفاً مفصلاً للمشروع">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-2">
                        حالة المشروع <span class="text-red-500">*</span>
                    </label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                            required>
                        <option value="">اختر حالة المشروع</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>معلق</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Manager -->
                <div>
                    <label for="manager_id" class="block text-sm font-medium text-slate-700 mb-2">
                        مدير المشروع
                    </label>
                    <select id="manager_id" 
                            name="manager_id" 
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                        <option value="">اختر مدير المشروع</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('manager_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('manager_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-slate-700 mb-2">
                        تاريخ البداية
                    </label>
                    <input type="date" 
                           id="start_date" 
                           name="start_date" 
                           value="{{ old('start_date') }}"
                           class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-slate-700 mb-2">
                        تاريخ الانتهاء
                    </label>
                    <input type="date" 
                           id="end_date" 
                           name="end_date" 
                           value="{{ old('end_date') }}"
                           class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-slate-100">
                <a href="{{ route('projects.index') }}" 
                   class="px-6 py-3 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition font-medium">
                    إنشاء المشروع
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 