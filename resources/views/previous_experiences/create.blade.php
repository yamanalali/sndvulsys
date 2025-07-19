@extends('layouts.master')

@section('content')
<div class="container">
    <h1 class="mb-4">إضافة خبرة سابقة</h1>
    <form method="POST" action="{{ route('previous-experiences.store') }}">
        @csrf
        <div class="form-group mb-3">
            <label for="description">وصف الخبرة</label>
            <textarea name="description" id="description" class="form-control" required></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="volunteer_request_id">رقم طلب التطوع</label>
            <input type="number" name="volunteer_request_id" id="volunteer_request_id" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">إضافة</button>
    </form>
</div>
@endsection 