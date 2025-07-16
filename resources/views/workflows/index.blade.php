@extends('layouts.app')
@section('content')
<div class="container">
    <h2>كل حالات الطلب (Workflow)</h2>
    <a href="{{ route('workflows.create') }}" class="btn btn-success mb-3">إضافة حالة جديدة</a>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    <table class="table">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>الوصف</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workflows as $workflow)
            <tr>
                <td>{{ $workflow->name }}</td>
                <td>{{ $workflow->description }}</td>
                <td>
                    <a href="{{ route('workflows.edit', $workflow->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    <form action="{{ route('workflows.destroy', $workflow->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection