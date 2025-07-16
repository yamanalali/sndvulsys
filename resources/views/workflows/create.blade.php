@extends('layouts.app')
@section('content')
<div class="container">
    <h2>إضافة حالة جديدة</h2>
    <form method="POST" action="{{ route('workflows.store') }}">
        @csrf
        <div class="form-group">
            <label for="name">اسم الحالة</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">الوصف</label>
            <input type="text" name="description" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">إضافة</button>
    </form>
</div>
@endsection