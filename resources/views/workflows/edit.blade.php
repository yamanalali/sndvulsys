@extends('layouts.app')
@section('content')
<div class="container">
    <h2>تعديل الحالة</h2>
    <form method="POST" action="{{ route('workflows.update', $workflow->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">اسم الحالة</label>
            <input type="text" name="name" class="form-control" value="{{ $workflow->name }}" required>
        </div>
        <div class="form-group">
            <label for="description">الوصف</label>
            <input type="text" name="description" class="form-control" value="{{ $workflow->description }}">
        </div>
        <button type="submit" class="btn btn-primary">تحديث</button>
    </form>
</div>
@endsection