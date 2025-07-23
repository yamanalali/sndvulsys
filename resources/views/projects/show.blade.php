@extends('layouts.master')

@section('content')
<div class="flex justify-center bg-gradient-to-br from-slate-50 to-blue-50 dark:from-navy-900 dark:to-navy-800 min-h-screen" dir="rtl">
    <div class="max-w-7xl w-full mx-auto px-4 py-8 my-auto relative z-10 flex flex-col justify-center items-center">
        <!-- Project Header -->
        <div class="bg-white dark:bg-navy-750 rounded-2xl shadow-soft dark:shadow-soft-dark overflow-hidden mb-8">
            <div class="p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 mb-8">
                    <div class="flex-1">
                        <div class="flex items-center gap-4 mb-4 mt-16">
                            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-primary to-purple-600 rounded-xl">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-100">{{ $project->name }}</h1>
                                <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold {{ $project->status_color }} shadow-soft mt-2">
                                    {{ $project->status_label }}
                                </span>
                            </div>
                        </div>
                        @if($project->description)
                            <p class="text-slate-600 dark:text-navy-300 text-lg leading-relaxed">{{ $project->description }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <a href="{{ route('projects.edit', $project) }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-soft font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            تعديل المشروع
                        </a>
                        <a href="{{ route('projects.index') }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-600 text-white rounded-xl hover:bg-slate-700 transition-all duration-300 shadow-soft font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            العودة للمشاريع
                        </a>
                    </div>
                </div>

                <!-- Project Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-navy-700 dark:to-navy-600 rounded-2xl p-6 text-center shadow-soft">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-navy-100">{{ $project->tasks->count() }}</div>
                        <div class="text-sm text-slate-600 dark:text-navy-300">إجمالي المهام</div>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-navy-700 dark:to-navy-600 rounded-2xl p-6 text-center shadow-soft">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $project->progress }}%</div>
                        <div class="text-sm text-slate-600 dark:text-navy-300">التقدم العام</div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-navy-700 dark:to-navy-600 rounded-2xl p-6 text-center shadow-soft">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $project->tasks->where('status', 'completed')->count() }}</div>
                        <div class="text-sm text-slate-600 dark:text-navy-300">المهام المكتملة</div>
                    </div>
                    <div class="bg-gradient-to-r from-orange-50 to-orange-100 dark:from-navy-700 dark:to-navy-600 rounded-2xl p-6 text-center shadow-soft">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $project->tasks->where('status', 'in_progress')->count() }}</div>
                        <div class="text-sm text-slate-600 dark:text-navy-300">قيد التنفيذ</div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-bold text-slate-700 dark:text-navy-100">التقدم العام</span>
                        <span class="text-sm text-slate-600 dark:text-navy-300">{{ $project->progress }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-navy-600 rounded-full h-3">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500" 
                             style="width: {{ $project->progress }}%"></div>
                    </div>
                </div>

                <!-- Project Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-navy-100 mb-4">تفاصيل المشروع</h3>
                        @if($project->manager)
                            <div class="flex items-center p-4 bg-slate-50 dark:bg-navy-700 rounded-xl">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-700 dark:text-navy-100">مدير المشروع</div>
                                    <div class="text-sm text-slate-600 dark:text-navy-300">{{ $project->manager->name }}</div>
                                </div>
                            </div>
                        @endif

                        @if($project->start_date)
                            <div class="flex items-center p-4 bg-slate-50 dark:bg-navy-700 rounded-xl">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-700 dark:text-navy-100">تاريخ البداية</div>
                                    <div class="text-sm text-slate-600 dark:text-navy-300">{{ $project->start_date->format('Y-m-d') }}</div>
                                </div>
                            </div>
                        @endif

                        @if($project->end_date)
                            <div class="flex items-center p-4 bg-slate-50 dark:bg-navy-700 rounded-xl">
                                <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-700 dark:text-navy-100">تاريخ الانتهاء</div>
                                    <div class="text-sm text-slate-600 dark:text-navy-300">{{ $project->end_date->format('Y-m-d') }}</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-navy-100 mb-4">معلومات إضافية</h3>
                        @if($project->teamMembers->count() > 0)
                            <div class="p-4 bg-slate-50 dark:bg-navy-700 rounded-xl">
                                <div class="text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">أعضاء الفريق</div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($project->teamMembers as $member)
                                        <span class="px-3 py-1.5 bg-gradient-to-r from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 text-blue-700 dark:text-blue-300 text-sm rounded-lg font-medium">
                                            {{ $member->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="p-4 bg-slate-50 dark:bg-navy-700 rounded-xl">
                            <div class="text-sm font-bold text-slate-700 dark:text-navy-100 mb-3">حالة المهام</div>
                            <div class="space-y-3">
                                @php
                                    $statusCounts = $project->tasks->groupBy('status')->map->count();
                                @endphp
                                @foreach(['new', 'in_progress', 'completed', 'cancelled'] as $status)
                                    @if(isset($statusCounts[$status]))
                                        <div class="flex items-center justify-between p-2 bg-white dark:bg-navy-600 rounded-lg">
                                            <span class="text-sm text-slate-600 dark:text-navy-300">
                                                {{ match($status) {
                                                    'new' => 'جديدة',
                                                    'in_progress' => 'قيد التنفيذ',
                                                    'completed' => 'مكتملة',
                                                    'cancelled' => 'ملغاة',
                                                    default => $status
                                                } }}
                                            </span>
                                            <span class="text-sm font-bold text-slate-800 dark:text-navy-100 bg-slate-100 dark:bg-navy-500 px-2 py-1 rounded-lg">{{ $statusCounts[$status] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Tasks -->
        <div class="bg-white dark:bg-navy-750 rounded-2xl shadow-soft dark:shadow-soft-dark overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 dark:border-navy-600 bg-gradient-to-r from-slate-50 to-blue-50 dark:from-navy-700 dark:to-navy-600">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-primary to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-navy-100">مهام المشروع</h2>
                    </div>
                    <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-primary/90 text-white rounded-xl hover:from-primary/90 hover:to-primary transition-all duration-300 shadow-soft font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        إضافة مهمة جديدة
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-navy-600 text-right">
                    <thead class="bg-slate-50 dark:bg-navy-700">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-600 dark:text-navy-300">المهمة</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-600 dark:text-navy-300">الحالة</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-600 dark:text-navy-300">المكلفون</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-600 dark:text-navy-300">تاريخ الانتهاء</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-600 dark:text-navy-300">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-navy-750 divide-y divide-slate-100 dark:divide-navy-600">
                        @forelse($project->tasks as $task)
                            @php
                                $statusColor = match($task->status) {
                                    'completed' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                    'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                    'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                    'cancelled' => 'bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                                    default => ($task->deadline && $task->deadline < now() && $task->status != 'completed' ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'),
                                };
                                $statusLabel = match($task->status) {
                                    'completed' => 'مكتملة',
                                    'pending' => 'معلقة',
                                    'in_progress' => 'قيد التنفيذ',
                                    'cancelled' => 'ملغاة',
                                    default => ($task->deadline && $task->deadline < now() && $task->status != 'completed' ? 'متأخرة' : 'جديدة'),
                                };
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-navy-700 transition-all duration-200">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-bold text-slate-800 dark:text-navy-100">{{ $task->title }}</div>
                                        @if($task->description)
                                            <div class="text-sm text-slate-500 dark:text-navy-400 line-clamp-1 mt-1">{{ $task->description }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $statusColor }} shadow-soft">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($task->assignments as $assignment)
                                            <span class="px-2 py-1 bg-gradient-to-r from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 text-blue-700 dark:text-blue-300 text-xs rounded-lg font-medium">
                                                {{ $assignment->user->name }}
                                                @if($assignment->assigned_at)
                                                    <span class="block text-[10px] text-slate-400 dark:text-navy-400 font-normal mt-0.5">({{ $assignment->assigned_at->format('Y-m-d H:i') }})</span>
                                                @endif
                                            </span>
                                        @empty
                                            <span class="text-slate-400 dark:text-navy-500 text-xs">غير مخصص</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($task->deadline)
                                        <span class="{{ $task->deadline < now() && $task->status != 'completed' ? 'text-red-600 dark:text-red-400' : 'text-slate-600 dark:text-navy-300' }} font-medium">
                                            {{ $task->deadline->format('Y-m-d') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 dark:text-navy-500">غير محدد</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('tasks.show', $task->id) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-primary to-primary/90 text-white rounded-lg shadow-soft font-bold text-xs transition-all duration-300 hover:from-primary/90 hover:to-primary">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 5-9 9-9 9s-9-4-9-9a9 9 0 0118 0z" />
                                            </svg>
                                            <span>تفاصيل</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-center">
                                        <div class="w-20 h-20 bg-gradient-to-r from-slate-200 to-slate-300 dark:from-navy-600 dark:to-navy-700 rounded-full flex items-center justify-center mx-auto mb-6">
                                            <svg class="w-10 h-10 text-slate-400 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-800 dark:text-navy-100 mb-2">لا توجد مهام في هذا المشروع</h3>
                                        <p class="text-slate-600 dark:text-navy-300 mb-6 max-w-md mx-auto">ابدأ بإضافة مهمة جديدة للمشروع لبدء العمل عليه</p>
                                        <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" 
                                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary to-primary/90 text-white rounded-xl hover:from-primary/90 hover:to-primary transition-all duration-300 shadow-soft font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            إضافة مهمة جديدة
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 