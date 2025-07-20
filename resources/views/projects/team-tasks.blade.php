@extends('layouts.master')

@section('content')
<div class="max-w-7xl mx-auto mt-10 px-4" dir="rtl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">مهام فريقي</h1>
            <p class="text-slate-600 mt-2">المهام في المشاريع التي أشارك فيها مع الفريق</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.index') }}" 
               class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition">
                جميع المشاريع
            </a>
            <a href="{{ route('projects.my-projects') }}" 
               class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                مشاريعي
            </a>
        </div>
    </div>

    <!-- Tasks Summary -->
    @php
        $statusLabels = [
            'new' => ['label' => 'جديدة', 'color' => '#64748b', 'text' => 'text-slate-700'],
            'in_progress' => ['label' => 'قيد التنفيذ', 'color' => '#3b82f6', 'text' => 'text-blue-600'],
            'testing' => ['label' => 'اختبار', 'color' => '#0284c7', 'text' => 'text-cyan-700'],
            'awaiting_feedback' => ['label' => 'في انتظار التغذية الراجعة', 'color' => '#84cc16', 'text' => 'text-lime-700'],
            'completed' => ['label' => 'مكتملة', 'color' => '#22c55e', 'text' => 'text-green-700'],
        ];
        $summary = [];
        foreach ($statusLabels as $status => $info) {
            $summary[$status] = [
                'count' => $teamTasks->where('status', $status)->count(),
                'label' => $info['label'],
                'color' => $info['color'],
                'text' => $info['text'],
            ];
        }
    @endphp

    <div class="w-full flex flex-row gap-2 mb-6 overflow-x-auto py-2 px-1" style="direction: rtl;">
        @foreach($summary as $item)
            <div class="flex flex-col items-center justify-center min-w-[120px] bg-white border border-slate-200 rounded-xl shadow-sm px-4 py-2 text-center">
                <span class="font-extrabold text-xl {{ $item['text'] }} mb-1">{{ $item['count'] }}</span>
                <span class="text-xs font-bold" style="color:{{ $item['color'] }}">{{ $item['label'] }}</span>
            </div>
        @endforeach
    </div>

    <!-- Tasks List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-800">قائمة المهام</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-slate-600">المشروع</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-600">المهمة</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-600">الحالة</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-600">المكلفون</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-600">تاريخ الانتهاء</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-600">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($teamTasks as $task)
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
                                @if($task->project)
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-primary rounded-full ml-2"></div>
                                        <span class="font-medium text-slate-800">{{ $task->project->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400">بدون مشروع</span>
                                @endif
                            </td>
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
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-center">
                                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-slate-600 mb-2">لا توجد مهام لفريقك</h3>
                                    <p class="text-slate-500">لم يتم تعيينك في أي مشروع بعد</p>
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