@extends('layouts.app')

@section('title', 'جميع النسخ الاحتياطية')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-copy text-primary me-2"></i>
                        <h4 class="mb-0">جميع النسخ الاحتياطية</h4>
                    </div>
                    <a href="{{ route('documents.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        العودة للمستندات
                    </a>
                </div>
                <div class="card-body">
                    <!-- إحصائيات النسخ الاحتياطية -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $allBackups->count() }}</h5>
                                    <small>إجمالي النسخ</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $allBackups->where('storage_type', 'drive')->count() }}</h5>
                                    <small>في Google Drive</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $allBackups->where('storage_type', 'local')->count() }}</h5>
                                    <small>محلية</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $allBackups->where('backup_type', 'automatic')->count() }}</h5>
                                    <small>تلقائية</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- رسالة إذا لم توجد نسخ احتياطية -->
                    @if($allBackups->count() == 0)
                    <div class="text-center py-5">
                        <i class="fas fa-copy fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد نسخ احتياطية</h5>
                        <p class="text-muted">قم بإنشاء نسخة احتياطية من أحد المستندات</p>
                        <a href="{{ route('documents.index') }}" class="btn btn-primary">
                            <i class="fas fa-file-alt"></i>
                            عرض المستندات
                        </a>
                    </div>
                    @else
                    <!-- معلومات التصحيح -->
                    @if(config('app.debug'))
                    <div class="alert alert-info">
                        <strong>معلومات التصحيح:</strong><br>
                        إجمالي النسخ: {{ $allBackups->count() }}<br>
                        عدد المستندات: {{ $documents->count() }}<br>
                        النسخ في Google Drive: {{ $allBackups->where('storage_type', 'drive')->count() }}<br>
                        النسخ المحلية: {{ $allBackups->where('storage_type', 'local')->count() }}
                    </div>
                    @endif
                    <!-- قائمة النسخ الاحتياطية -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>المستند</th>
                                    <th>نوع النسخة</th>
                                    <th>التخزين</th>
                                    <th>الحجم</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allBackups as $backup)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-{{ getFileIcon($backup->document->file_type) }} text-primary me-2"></i>
                                            <div>
                                                <strong>{{ $backup->document->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $backup->document->user->name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $backup->backup_type === 'automatic' ? 'warning' : 'info' }}">
                                            {{ $backup->backup_type === 'automatic' ? 'تلقائية' : 'يدوية' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $backup->storage_type === 'drive' ? 'success' : 'primary' }}">
                                            {{ $backup->storage_type === 'drive' ? 'Google Drive' : 'محلية' }}
                                        </span>
                                    </td>
                                    <td>{{ $backup->formatted_size ?? 'غير محدد' }}</td>
                                    <td>{{ $backup->backup_date->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($backup->storage_type === 'drive' && $backup->drive_file_id)
                                            <a href="https://drive.google.com/file/d/{{ $backup->drive_file_id }}/view" 
                                               target="_blank" class="btn btn-sm btn-outline-success" title="عرض في Google Drive">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            @endif
                                            @if($backup->storage_type === 'local' && $backup->backup_path)
                                            <a href="{{ route('documents.download', $backup->document) }}?backup={{ $backup->id }}" 
                                               class="btn btn-sm btn-outline-primary" title="تحميل">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @endif
                                            @if($backup->document->user_id === auth()->id())
                                            <form action="{{ route('document-backups.restore', $backup) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                        onclick="return confirm('هل أنت متأكد من استعادة هذه النسخة؟')" title="استعادة">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
function getFileIcon($type) {
    $icons = [
        'pdf' => 'pdf',
        'doc' => 'word',
        'docx' => 'word',
        'xls' => 'excel',
        'xlsx' => 'excel',
        'ppt' => 'powerpoint',
        'pptx' => 'powerpoint',
        'jpg' => 'image',
        'jpeg' => 'image',
        'png' => 'image',
        'gif' => 'image',
        'txt' => 'alt',
    ];
    return $icons[$type] ?? 'alt';
}
@endphp 