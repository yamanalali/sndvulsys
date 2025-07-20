@extends('layouts.app') 

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white rounded-3xl shadow-2xl border border-slate-100 p-8 flex flex-col gap-8" dir="rtl">
    <div class="flex items-center gap-3 mb-2">
        <div class="bg-primary/10 rounded-full p-3">
            <svg class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v3a2 2 0 002 2m12 0v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7" /></svg>
        </div>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800">تفاصيل المهمة</h2>
            <div class="text-slate-500 text-sm">#{{ $task->id }}</div>
        </div>
        <a href="{{ route('tasks.index') }}" class="ml-auto bg-slate-100 text-slate-600 px-4 py-2 rounded-lg font-bold hover:bg-slate-200 transition flex items-center gap-1">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            رجوع
        </a>
    </div>
    <div class="bg-slate-50 rounded-xl border border-slate-200 p-6">
        <ul class="divide-y divide-slate-200 text-base">
            <li class="py-2 flex justify-between"><span class="font-bold text-slate-700">العنوان:</span> <span class="text-slate-800">{{ $task->title }}</span></li>
            <li class="py-2 flex justify-between"><span class="font-bold text-slate-700">الوصف:</span> <span class="text-slate-600">{{ $task->description ?: '—' }}</span></li>
            <li class="py-2 flex justify-between"><span class="font-bold text-slate-700">الحالة:</span>
                @php
                    $statusColors = [
                        'completed' => 'bg-green-100 text-green-700',
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'in_progress' => 'bg-yellow-100 text-yellow-700',
                        'cancelled' => 'bg-gray-200 text-gray-500',
                        'new' => 'bg-slate-100 text-slate-700',
                    ];
                    $statusLabel = [
                        'completed' => 'منجزة',
                        'pending' => 'معلقة',
                        'in_progress' => 'قيد التنفيذ',
                        'cancelled' => 'ملغاة',
                        'new' => 'جديدة',
                    ][$task->status] ?? $task->status;
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$task->status] ?? 'bg-slate-100 text-slate-700' }}">{{ $statusLabel }}</span>
            </li>
            <li class="py-2 flex justify-between"><span class="font-bold text-slate-700">تاريخ البدء:</span> <span class="text-slate-600">{{ $task->start_date ? $task->start_date->format('Y-m-d') : '—' }}</span></li>
            <li class="py-2 flex justify-between"><span class="font-bold text-slate-700">تاريخ الانتهاء:</span> <span class="text-slate-600">{{ $task->deadline ? $task->deadline->format('Y-m-d') : '—' }}</span></li>
            <li class="py-2 flex justify-between"><span class="font-bold text-slate-700">الأولوية:</span> <span class="text-slate-600">{{ $task->priority ?? '—' }}</span></li>
            <li class="py-2 flex justify-between"><span class="font-bold text-slate-700">الفئة:</span> <span class="text-slate-600">{{ optional($task->category)->name ?? '—' }}</span></li>
            <li class="py-2 flex justify-between"><span class="font-bold text-slate-700">نسبة الإنجاز:</span> <span class="text-slate-600">{{ $task->progress }}%</span></li>
            <li class="py-2 flex justify-between"><span class="font-bold text-slate-700">ملاحظات:</span> <span class="text-slate-600">{{ $task->notes ?: '—' }}</span></li>
        </ul>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-lg font-bold text-slate-700 mb-2">تحديث حالة المهمة</h3>
        <form method="POST" action="{{ route('tasks.updateStatus', $task->id) }}" class="flex flex-col md:flex-row gap-4 items-end">
            @csrf
            <select name="status" class="form-input rounded-lg border border-slate-300 px-3 py-2 focus:border-primary text-lg" required>
                <option value="in_progress">قيد التنفيذ</option>
                <option value="pending_review">بانتظار المراجعة</option>
                <option value="awaiting_approval">بانتظار الموافقة</option>
                <option value="approved">موافق عليها</option>
                <option value="rejected">مرفوضة</option>
                <option value="on_hold">معلقة مؤقتاً</option>
                <option value="completed">منجزة</option>
                <option value="cancelled">ملغاة</option>
                <option value="archived">مؤرشفة</option>
            </select>
            <button type="submit" class="bg-primary text-white px-5 py-2 rounded-lg font-bold shadow hover:bg-primary/90 transition">تحديث الحالة</button>
        </form>
    </div>
    {{-- قسم تبعيات المهمة --}}
    <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
        <h3 class="text-xl font-bold text-blue-800 mb-4 flex items-center gap-2">
            <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h2M15 3h-6a2 2 0 00-2 2v3a2 2 0 002 2h6a2 2 0 002-2V5a2 2 0 00-2-2z" /></svg>
            تبعيات المهمة
        </h3>
        @if($task->dependenciesRaw && $task->dependenciesRaw->count())
            <table class="min-w-full text-right mb-4">
                <thead>
                    <tr class="text-blue-700 bg-blue-100">
                        <th class="px-3 py-2">المهمة المعتمَد عليها</th>
                        <th class="px-3 py-2">نوع التبعية</th>
                        <th class="px-3 py-2">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($task->dependenciesRaw as $dep)
                        <tr class="bg-white border-b">
                            <td class="px-3 py-2">{{ optional($dep->prerequisiteTask)->title ?? 'غير محدد' }}</td>
                            <td class="px-3 py-2">
                                @php
                                    $types = [
                                        'finish_to_start' => 'إنهاء-لبدء',
                                        'start_to_start' => 'بدء-لبدء',
                                        'finish_to_finish' => 'إنهاء-لإنهاء',
                                        'start_to_finish' => 'بدء-لإنهاء',
                                    ];
                                @endphp
                                <span class="px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs font-bold">{{ $types[$dep->dependency_type] ?? $dep->dependency_type }}</span>
                            </td>
                            <td class="px-3 py-2">
                                <form action="{{ route('tasks.dependencies.destroy', [$task->id, $dep->id]) }}" method="POST" onsubmit="return confirm('هل تريد حذف التبعية؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline font-bold">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-slate-500 mb-4">لا توجد تبعيات حالياً لهذه المهمة.</div>
        @endif
        {{-- نموذج إضافة تبعية جديدة --}}
        <form action="{{ route('tasks.dependencies.store', $task->id) }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
            @csrf
            <div class="flex-1">
                <label class="block font-semibold mb-1">اختر مهمة تعتمد عليها</label>
                <select name="depends_on_id" class="form-input w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-primary text-lg" required>
                    <option value="">-- اختر مهمة --</option>
                    @foreach($allTasks as $t)
                        @if($t->id != $task->id && !$task->dependencies->pluck('depends_on_task_id')->contains($t->id))
                            <option value="{{ $t->id }}">{{ $t->title }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">نوع التبعية</label>
                <select name="dependency_type" class="form-input rounded-lg border border-slate-300 px-3 py-2 focus:border-primary text-lg">
                    <option value="finish_to_start">إنهاء-لبدء</option>
                    <option value="start_to_start">بدء-لبدء</option>
                    <option value="finish_to_finish">إنهاء-لإنهاء</option>
                    <option value="start_to_finish">بدء-لإنهاء</option>
                </select>
            </div>
            <button type="submit" class="bg-primary text-white px-5 py-2 rounded-lg font-bold shadow hover:bg-primary/90 transition">إضافة تبعية</button>
        </form>
    </div>
</div>
@endsection
