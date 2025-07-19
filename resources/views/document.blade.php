@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">رفع مستند جديد</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="title">اسم الملف</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="أدخل اسم الملف" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="file">اختر الملف</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".pdf,.docx,.zip" required>
                            <small class="form-text text-muted">الأنواع المسموحة: PDF, DOCX, ZIP (الحد الأقصى: 2MB)</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">رفع الملف</button>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">النسخ الاحتياطية</div>
                <div class="card-body">
                    <a href="{{ route('backup.documents') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> تحميل نسخة احتياطية
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
