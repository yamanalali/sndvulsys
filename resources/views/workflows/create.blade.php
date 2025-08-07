@extends('layouts.master')

@section('content')
<div class="page-wrapper" dir="rtl">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="feather-plus-circle me-2"></i>
                                إضافة حالة جديدة
                            </h4>
                        </div>
                        <div class="card-body">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('workflows.store') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">اسم الحالة</label>
                                    <input type="text" name="name" id="name" class="form-control" required 
                                           value="{{ old('name') }}" placeholder="أدخل اسم الحالة">
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">الوصف</label>
                                    <textarea name="description" id="description" class="form-control" rows="3" 
                                              placeholder="أدخل وصف الحالة">{{ old('description') }}</textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i>
                                        إضافة
                                    </button>
                                    <a href="{{ route('workflows.index') }}" class="btn btn-secondary">
                                        <i class="feather-arrow-right me-2"></i>
                                        العودة
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection