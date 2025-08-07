@extends('layouts.master')

@section('content')
<div class="page-wrapper" dir="rtl">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">
                                    <i class="feather-list me-2"></i>
                                    كل حالات الطلب (Workflow)
                                </h4>
                                <a href="{{ route('workflows.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>
                                    إضافة حالة جديدة
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="feather-check-circle me-2"></i>
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>الاسم</th>
                                            <th>الوصف</th>
                                            <th>إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($workflows as $workflow)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $workflow->name }}</div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $workflow->description ?: 'لا يوجد وصف' }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('workflows.edit', $workflow->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="feather-edit me-1"></i>
                                                        تعديل
                                                    </a>
                                                    <form action="{{ route('workflows.destroy', $workflow->id) }}" 
                                                          method="POST" style="display:inline;">
                                                        @csrf 
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                            <i class="feather-trash-2 me-1"></i>
                                                            حذف
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <div class="text-center">
                                                    <div class="mb-4">
                                                        <i class="feather-list text-muted" style="font-size: 64px;"></i>
                                                    </div>
                                                    <h4 class="text-muted mb-3">لا توجد حالات مضافة</h4>
                                                    <p class="text-muted mb-4">ابدأ بإضافة حالة جديدة لإدارة سير العمل</p>
                                                    <a href="{{ route('workflows.create') }}" class="btn btn-primary">
                                                        <i class="feather-plus me-2"></i>
                                                        إضافة حالة جديدة
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection