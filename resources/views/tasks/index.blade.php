@extends('layouts.master')
@section('content')
<div class="max-w-5xl mx-auto mt-10 card p-6" dir="rtl">
    @php
        $showMine = request('mine') == '1';
        $userId = auth()->id();
        $filteredTasks = $showMine
            ? $tasks->filter(function($task) use ($userId) {
                return $task->assignments->pluck('user_id')->contains($userId);
            })
            : $tasks;
    @endphp
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-slate-700">قائمة المهام</h2>
        <div class="flex items-center gap-2">
            <!-- Project Filter -->
            <select id="project-filter" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                <option value="">جميع المشاريع</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
            
            <a href="?mine=1" class="px-4 py-2 rounded-lg font-bold text-white bg-primary hover:bg-primary/90 transition {{ $showMine ? 'ring-2 ring-primary' : '' }}">مهامي فقط</a>
            @if($showMine)
                <a href="?" class="px-4 py-2 rounded-lg font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">عرض الكل</a>
            @endif
            <a href="{{ route('tasks.create') }}" class="btn bg-primary text-white rounded px-4 py-2">إضافة مهمة</a>
        </div>
    </div>
    @php
        $statusLabels = [
            'new' => ['label' => 'Not Started', 'color' => '#64748b', 'text' => 'text-slate-700'],
            'in_progress' => ['label' => 'In Progress', 'color' => '#3b82f6', 'text' => 'text-blue-600'],
            'testing' => ['label' => 'Testing', 'color' => '#0284c7', 'text' => 'text-cyan-700'],
            'awaiting_feedback' => ['label' => 'Awaiting Feedback', 'color' => '#84cc16', 'text' => 'text-lime-700'],
            'completed' => ['label' => 'Complete', 'color' => '#22c55e', 'text' => 'text-green-700'],
        ];
        $summary = [];
        foreach ($statusLabels as $status => $info) {
            $summary[$status] = [
                'count' => $tasks->where('status', $status)->count(),
                'assigned' => $tasks->where('status', $status)->where('assigned_to', auth()->id())->count(),
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
                <span class="text-[11px] text-neutral-500 mt-1">Tasks: {{ $item['assigned'] }}</span>
            </div>
        @endforeach
    </div>
    <table class="min-w-full divide-y divide-slate-200 text-right">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-2 text-xs font-bold text-slate-600">العنوان</th>
                <th class="px-4 py-2 text-xs font-bold text-slate-600">الحالة</th>
                <th class="px-4 py-2 text-xs font-bold text-slate-600">تاريخ الانتهاء</th>
                <th class="px-4 py-2 text-xs font-bold text-slate-600">إجراءات</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-100">
            @forelse($filteredTasks as $task)
                @php
                    $statusColor = match($task->status) {
                        'completed' => 'bg-green-100 text-green-700',
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'in_progress' => 'bg-yellow-100 text-yellow-700',
                        'cancelled' => 'bg-gray-200 text-gray-500',
                        default => ($task->deadline && $task->deadline < now() && $task->status != 'completed' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700'),
                    };
                    $statusLabel = match($task->status) {
                        'completed' => 'منجزة',
                        'pending' => 'معلقة',
                        'in_progress' => 'قيد التنفيذ',
                        'cancelled' => 'ملغاة',
                        default => ($task->deadline && $task->deadline < now() && $task->status != 'completed' ? 'متأخرة' : 'جديدة'),
                    };
                @endphp
                <tr>
                    <td class="px-4 py-2 font-medium">{{ $task->title }}</td>
                    <td class="px-4 py-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ $task->deadline ? $task->deadline->format('Y-m-d') : '-' }}</td>
                    <td class="px-4 py-2 space-x-2 space-x-reverse flex items-center justify-end gap-2">
                        <a href="{{ route('tasks.show', $task->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-1 text-white rounded shadow font-bold mx-1 focus:outline-none focus:ring-2 focus:ring-purple-300 text-xs"
                           style="background-color: #7c3aed !important; border: none; box-shadow: 0 2px 8px 0 rgba(124,58,237,0.10); transition: background 0.2s;"
                           onmouseover="this.style.backgroundColor='#5b21b6'" onmouseout="this.style.backgroundColor='#7c3aed'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 5-9 9-9 9s-9-4-9-9a9 9 0 0118 0z" /></svg>
                            <span>تفاصيل</span>
                        </a>
                        <a href="{{ route('tasks.edit', $task->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-1 text-white rounded shadow font-bold mx-1 focus:outline-none focus:ring-2 focus:ring-blue-300 text-xs"
                           style="background-color: #2563eb !important; border: none; box-shadow: 0 2px 8px 0 rgba(37,99,235,0.10); transition: background 0.2s;"
                           onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13h3l8-8a2.828 2.828 0 00-4-4l-8 8v3zm0 0v3a2 2 0 002 2h3" /></svg>
                            <span>تعديل</span>
                        </a>
                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center gap-1 px-3 py-1 text-white rounded shadow font-bold mx-1 focus:outline-none focus:ring-2 focus:ring-red-300 text-xs"
                                style="background-color: #dc2626 !important; border: none;"
                                onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V4a1 1 0 011-1h6a1 1 0 011 1v3" /></svg>
                                <span>حذف</span>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-slate-400">لا توجد مهام حالياً.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
document.getElementById('project-filter').addEventListener('change', function() {
    const projectId = this.value;
    const currentUrl = new URL(window.location);
    
    if (projectId) {
        currentUrl.searchParams.set('project', projectId);
    } else {
        currentUrl.searchParams.delete('project');
    }
    
    window.location.href = currentUrl.toString();
});
</script>
@endsection 