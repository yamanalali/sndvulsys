@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">إدارة تقييمات المتطوعين</h4>
                        <p class="card-subtitle mb-0">إدارة وتتبع تقييمات طلبات التطوع</p>
                    </div>
                    <div>
                        <a href="{{ route('volunteer-requests.index') }}" class="btn btn-primary me-2">
                            <i class="fas fa-list"></i>
                            جميع طلبات التطوع
                        </a>
                        <a href="{{ route('volunteer-evaluations.statistics') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i>
                            إحصائيات التقييم
                        </a>
                    </div>
                </div>
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

                    <!-- طلبات التطوع التي لم يتم تقييمها -->
                    @if($unevaluatedRequests->count() > 0)
                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="fas fa-clock"></i>
                            طلبات التطوع التي تحتاج تقييم ({{ $unevaluatedRequests->count() }})
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم المتطوع</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>رقم الجوال</th>
                                        <th>تاريخ التقديم</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unevaluatedRequests as $request)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $request->full_name }}</strong>
                                        </td>
                                        <td>{{ $request->email }}</td>
                                        <td>{{ $request->phone }}</td>
                                        <td>
                                            {{ $request->created_at ? $request->created_at->format('Y-m-d') : 'غير محدد' }}
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">في انتظار التقييم</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('volunteer-requests.show', $request->id) }}" 
                                                   class="btn btn-sm btn-info" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('volunteer-evaluations.create', $request->id) }}" 
                                                   class="btn btn-sm btn-success" title="إضافة تقييم">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- التقييمات الموجودة -->
                    @if($evaluations->count() > 0)
                    <div>
                        <h5 class="text-success">
                            <i class="fas fa-check-circle"></i>
                            التقييمات المكتملة ({{ $evaluations->count() }})
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم المتطوع</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>المقيم</th>
                                        <th>تاريخ التقييم</th>
                                        <th>النتيجة الإجمالية</th>
                                        <th>التوصية</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evaluations as $evaluation)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $evaluation->volunteerRequest->full_name }}</strong>
                                        </td>
                                        <td>{{ $evaluation->volunteerRequest->email }}</td>
                                        <td>{{ $evaluation->evaluator->name }}</td>
                                        <td>
                                            {{ $evaluation->evaluation_date ? $evaluation->evaluation_date->format('Y-m-d') : 'غير محدد' }}
                                        </td>
                                        <td>
                                            <strong>{{ $evaluation->overall_score }}/100</strong>
                                            <span class="badge badge-{{ $evaluation->overall_score >= 80 ? 'success' : ($evaluation->overall_score >= 60 ? 'warning' : 'danger') }}">
                                                {{ $evaluation->getScoreLevel() }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $evaluation->recommendation === 'strong_approve' || $evaluation->recommendation === 'approve' ? 'success' : ($evaluation->recommendation === 'reject' || $evaluation->recommendation === 'strong_reject' ? 'danger' : 'warning') }}">
                                                {{ $evaluation->getRecommendationText() }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $evaluation->status === 'approved' ? 'success' : ($evaluation->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ $evaluation->getStatusText() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('volunteer-evaluations.show', $evaluation->id) }}" 
                                                   class="btn btn-sm btn-info" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('volunteer-evaluations.edit', $evaluation->id) }}" 
                                                   class="btn btn-sm btn-primary" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('volunteer-evaluations.destroy', $evaluation->id) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('هل أنت متأكد من حذف هذا التقييم؟')" 
                                                            title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($evaluations->count() == 0 && $unevaluatedRequests->count() == 0)
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد تقييمات أو طلبات متاحة</h5>
                        <p class="text-muted">لم يتم إضافة أي تقييمات أو طلبات تطوع بعد.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 