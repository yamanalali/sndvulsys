@extends('layouts.master')

@section('content')
<div class="max-w-7xl mx-auto mt-10 px-4" dir="rtl">
    <!-- Project Header -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-3xl font-bold text-slate-800">{{ $project->name }}</h1>
                        <span class="px-3 py-1 rounded-full text-sm font-bold {{ $project->status_color }}">
                            {{ $project->status_label }}
                        </span>
                    </div>
                    @if($project->description)
                        <p class="text-slate-600 text-lg">{{ $project->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('projects.edit', $project) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        تعديل المشروع
                    </a>
                    <a href="{{ route('projects.index') }}" 
                       class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition">
                        العودة للمشاريع
                    </a>
                </div>
            </div>

            <!-- Project Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-slate-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-slate-800">{{ $project->tasks->count() }}</div>
                    <div class="text-sm text-slate-600">إجمالي المهام</div>
                </div>
                <div class="bg-slate-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $project->progress }}%</div>
                    <div class="text-sm text-slate-600">التقدم العام</div>
                </div>
                <div class="bg-slate-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $project->tasks->where('status', 'completed')->count() }}</div>
                    <div class="text-sm text-slate-600">المهام المكتملة</div>
                </div>
                <div class="bg-slate-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $project->tasks->where('status', 'in_progress')->count() }}</div>
                    <div class="text-sm text-slate-600">قيد التنفيذ</div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-700">التقدم العام</span>
                    <span class="text-sm text-slate-600">{{ $project->progress }}%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-3">
                    <div class="bg-green-500 h-3 rounded-full transition-all duration-300" 
                         style="width: {{ $project->progress }}%"></div>
                </div>
            </div>

            <!-- Project Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    @if($project->manager)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-slate-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-slate-700">مدير المشروع</div>
                                <div class="text-sm text-slate-600">{{ $project->manager->name }}</div>
                            </div>
                        </div>
                    @endif

                    @if($project->start_date)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-slate-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-slate-700">تاريخ البداية</div>
                                <div class="text-sm text-slate-600">{{ $project->start_date->format('Y-m-d') }}</div>
                            </div>
                        </div>
                    @endif

                    @if($project->end_date)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-slate-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-slate-700">تاريخ الانتهاء</div>
                                <div class="text-sm text-slate-600">{{ $project->end_date->format('Y-m-d') }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-4">
                    @if($project->teamMembers->count() > 0)
                        <div>
                            <div class="text-sm font-medium text-slate-700 mb-2">أعضاء الفريق</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($project->teamMembers as $member)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full">
                                        {{ $member->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <div class="text-sm font-medium text-slate-700 mb-2">حالة المهام</div>
                        <div class="space-y-2">
                            @php
                                $statusCounts = $project->tasks->groupBy('status')->map->count();
                            @endphp
                            @foreach(['new', 'in_progress', 'completed', 'cancelled'] as $status)
                                @if(isset($statusCounts[$status]))
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-600">
                                            {{ match($status) {
                                                'new' => 'جديدة',
                                                'in_progress' => 'قيد التنفيذ',
                                                'completed' => 'مكتملة',
                                                'cancelled' => 'ملغاة',
                                                default => $status
                                            } }}
                                        </span>
                                        <span class="text-sm font-medium text-slate-800">{{ $statusCounts[$status] }}</span>
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
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-800">مهام المشروع</h2>
                <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" 
                   class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                    إضافة مهمة جديدة
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-slate-600">المهمة</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-600">الحالة</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-600">المكلفون</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-600">تاريخ الانتهاء</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-600">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($project->tasks as $task)
                        @php
                            $statusColor = match($task->status) {
                                'completed' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'in_progress' => 'bg-blue-100 text-blue-700',
                                'cancelled' => 'bg-gray-200 text-gray-500',
                                default => ($task->deadline && $task->deadline < now() && $task->status != 'completed' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700'),
                            };
                            $statusLabel = match($task->status) {
                                'completed' => 'مكتملة',
                                'pending' => 'معلقة',
                                'in_progress' => 'قيد التنفيذ',
                                'cancelled' => 'ملغاة',
                                default => ($task->deadline && $task->deadline < now() && $task->status != 'completed' ? 'متأخرة' : 'جديدة'),
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="font-medium text-slate-800">{{ $task->title }}</div>
                                    @if($task->description)
                                        <div class="text-sm text-slate-500 line-clamp-1">{{ $task->description }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($task->assignments as $assignment)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                                            {{ $assignment->user->name }}
                                        </span>
                                    @empty
                                        <span class="text-slate-400 text-xs">غير مخصص</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($task->deadline)
                                    <span class="{{ $task->deadline < now() && $task->status != 'completed' ? 'text-red-600' : 'text-slate-600' }}">
                                        {{ $task->deadline->format('Y-m-d') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">غير محدد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tasks.show', $task->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1 text-white rounded shadow font-bold focus:outline-none focus:ring-2 focus:ring-purple-300 text-xs"
                                       style="background-color: #7c3aed !important; border: none; box-shadow: 0 2px 8px 0 rgba(124,58,237,0.10); transition: background 0.2s;"
                                       onmouseover="this.style.backgroundColor='#5b21b6'" onmouseout="this.style.backgroundColor='#7c3aed'">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-slate-600 mb-2">لا توجد مهام في هذا المشروع</h3>
                                    <p class="text-slate-500 mb-6">ابدأ بإضافة مهمة جديدة للمشروع</p>
                                    <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" 
                                       class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
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
@endsection 