@extends('layouts.master')
@section('content')
@php
    $showMine = request('mine') == '1';
    $userId = auth()->id();
    $filteredTasks = $showMine
        ? $tasks->filter(function($task) use ($userId) {
            return $task->assignments->pluck('user_id')->contains($userId);
        })
        : $tasks;
@endphp
<div class="row justify-content-center mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">قائمة المهام</h5>
                <div>
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary"><i class="feather icon-plus"></i> إضافة مهمة</a>
                </div>
            </div>
            <div class="card-block">
                <form class="form-inline mb-3" method="GET" action="">
                    <div class="form-group mr-2">
                        <select id="project-filter" name="project" class="form-control">
                            <option value="">جميع المشاريع</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <a href="?mine=1" class="btn btn-outline-info {{ request('mine') == '1' ? 'active' : '' }}">مهامي فقط</a>
                        @if(request('mine') == '1')
                            <a href="?" class="btn btn-outline-secondary ml-2">عرض الكل</a>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-info">تصفية</button>
                </form>
                <table class="table table-hover table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>العنوان</th>
                            <th>الحالة</th>
                            <th>تاريخ الانتهاء</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($filteredTasks as $task)
                            @php
                                $statusColor = match($task->status) {
                                    'completed' => 'badge-success',
                                    'pending' => 'badge-warning',
                                    'in_progress' => 'badge-info',
                                    'cancelled' => 'badge-secondary',
                                    default => ($task->deadline && $task->deadline < now() && $task->status != 'completed' ? 'badge-danger' : 'badge-light'),
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
                                <td>{{ $task->title }}</td>
                                <td><span class="badge {{ $statusColor }}">{{ $statusLabel }}</span></td>
                                <td>{{ $task->deadline ? $task->deadline->format('Y-m-d') : '-' }}</td>
                                <td>
                                    <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-outline-info" title="تفاصيل"><i class="feather icon-eye"></i></a>
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل"><i class="feather icon-edit"></i></a>
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')" title="حذف"><i class="feather icon-trash-2"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">لا توجد مهام حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

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