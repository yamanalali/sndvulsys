@extends('layouts.master')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">قائمة الخبرات السابقة</h1>
    <a href="{{ route('previous-experiences.create') }}" class="btn btn-primary mb-3">إضافة خبرة جديدة</a>
    @if(count($experiences))
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الوصف</th>
                    <th>رقم طلب التطوع</th>
                    <th>تاريخ الإضافة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($experiences as $exp)
                    <tr>
                        <td>{{ $exp->id }}</td>
                        <td>{{ $exp->description }}</td>
                        <td>{{ $exp->volunteer_request_id }}</td>
                        <td>{{ $exp->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>لا توجد خبرات سابقة مسجلة حالياً.</p>
    @endif
</div>
@endsection 