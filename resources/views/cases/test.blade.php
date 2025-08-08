@extends('layouts.app')

@section('title', 'اختبار صفحة الحالات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>اختبار صفحة الحالات</h4>
                </div>
                <div class="card-body">
                    <h5>معلومات النظام:</h5>
                    <ul>
                        <li>عدد طلبات التطوع: {{ \App\Models\VolunteerRequest::count() }}</li>
                        <li>عدد المستخدمين: {{ \App\Models\User::count() }}</li>
                        <li>عدد سير العمل: {{ \App\Models\Workflow::count() }}</li>
                    </ul>

                    <h5>طلبات التطوع الموجودة:</h5>
                    @php
                        $requests = \App\Models\VolunteerRequest::take(5)->get();
                    @endphp
                    
                    @if($requests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الإنشاء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                    <tr>
                                        <td>{{ $request->id }}</td>
                                        <td>{{ $request->full_name }}</td>
                                        <td>{{ $request->email }}</td>
                                        <td>
                                            <span class="badge badge-{{ $request->getStatusColorAttribute() }}">
                                                {{ $request->getStatusTextAttribute() }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            لا توجد طلبات تطوع في النظام. 
                            <a href="{{ route('volunteer-requests.create') }}" class="btn btn-primary btn-sm">
                                إنشاء طلب جديد
                            </a>
                        </div>
                    @endif

                    <hr>

                    <h5>اختبار الروابط:</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('volunteer-requests.index') }}" class="btn btn-primary">
                            طلبات التطوع
                        </a>
                        <a href="{{ route('workflows.index') }}" class="btn btn-info">
                            سير المراجعة
                        </a>
                        <a href="{{ route('submissions.index') }}" class="btn btn-success">
                            الإرسالات
                        </a>
                        <a href="{{ route('case-management.index') }}" class="btn btn-warning">
                            إدارة الحالات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 