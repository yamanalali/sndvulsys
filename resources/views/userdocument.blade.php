@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>مستنداتي</h4>
                </div>
                <div class="card-body">
                    @if(auth()->user()->documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>اسم الملف</th>
                                        <th>تاريخ الرفع</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (auth()->user()->documents as $index => $doc)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $doc->title }}</td>
                                            <td>{{ $doc->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ asset('storage/'.$doc->file_path) }}" 
                                                   class="btn btn-sm btn-primary" 
                                                   target="_blank">
                                                    <i class="fas fa-download"></i> تنزيل
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد مستندات مرفوعة بعد</p>
                            <a href="{{ route('documents.create') }}" class="btn btn-primary">
                                رفع مستند جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
