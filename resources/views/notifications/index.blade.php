@extends('layouts.master')

@section('title', 'الإشعارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 rounded p-3">
                                <i data-feather="bell" class="text-primary" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h1 class="h3 mb-1 text-dark">الإشعارات</h1>
                                <p class="text-muted mb-0">إدارة جميع إشعاراتك في مكان واحد</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('notifications.settings') }}" class="btn btn-outline-secondary">
                                <i data-feather="settings" class="me-2" style="width: 16px; height: 16px;"></i>
                                إعدادات الإشعارات
                            </a>
                            <button onclick="markAllAsRead()" class="btn btn-primary">
                                <i data-feather="check" class="me-2" style="width: 16px; height: 16px;"></i>
                                تحديد الكل كمقروء
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">إجمالي الإشعارات</p>
                                    <p class="h4 mb-0 text-dark">{{ $stats['total'] }}</p>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <i data-feather="bell" class="text-primary" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">غير مقروءة</p>
                                    <p class="h4 mb-0 text-danger">{{ $stats['unread'] }}</p>
                                </div>
                                <div class="bg-danger bg-opacity-10 rounded p-2">
                                    <i data-feather="clock" class="text-danger" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">مقروءة</p>
                                    <p class="h4 mb-0 text-success">{{ $stats['read'] }}</p>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded p-2">
                                    <i data-feather="check-circle" class="text-success" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">اليوم</p>
                                    <p class="h4 mb-0 text-warning">{{ $stats['today'] }}</p>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <i data-feather="calendar" class="text-warning" style="width: 20px; height: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark">قائمة الإشعارات</h5>
                </div>
                
                @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                        <div class="list-group-item border-0 py-3 {{ $notification->read_at ? 'opacity-75' : '' }}">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-{{ $notification->data['color'] ?? 'primary' }} bg-opacity-10 rounded p-2 mt-1">
                                    <i data-feather="{{ $notification->data['icon'] ?? 'bell' }}" 
                                       class="text-{{ $notification->data['color'] ?? 'primary' }}" 
                                       style="width: 18px; height: 18px;"></i>
                                </div>
                                
                                <div class="flex-grow-1">
                                    @if(!$notification->read_at)
                                        <span class="badge bg-danger mb-2">جديد</span>
                                    @endif
                                    
                                    <p class="fw-medium text-dark mb-1">{{ $notification->data['message'] ?? 'إشعار جديد' }}</p>
                                    
                                    @if(isset($notification->data['task_title']))
                                        <p class="text-muted small mb-1">
                                            المهمة: <span class="fw-medium">{{ $notification->data['task_title'] }}</span>
                                        </p>
                                    @endif
                                    
                                    @if(isset($notification->data['deadline']))
                                        <p class="text-muted small mb-1">
                                            الموعد النهائي: <span class="fw-medium">{{ $notification->data['deadline'] }}</span>
                                        </p>
                                    @endif
                                    
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
                                        
                                        <div class="d-flex gap-2">
                                            @if(isset($notification->data['task_id']))
                                                <a href="{{ route('tasks.show', $notification->data['task_id']) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i data-feather="eye" class="me-1" style="width: 14px; height: 14px;"></i>
                                                    عرض المهمة
                                                </a>
                                            @endif
                                            
                                            @if(!$notification->read_at)
                                                <button onclick="markAsRead('{{ $notification->id }}')"
                                                        class="btn btn-sm btn-success">
                                                    <i data-feather="check" class="me-1" style="width: 14px; height: 14px;"></i>
                                                    تحديد كمقروء
                                                </button>
                                            @endif
                                            
                                            <button onclick="deleteNotification('{{ $notification->id }}')"
                                                    class="btn btn-sm btn-outline-danger">
                                                <i data-feather="trash-2" class="me-1" style="width: 14px; height: 14px;"></i>
                                                حذف
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="card-footer bg-white border-top">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <i data-feather="bell-off" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                        <h5 class="text-muted">لا توجد إشعارات</h5>
                        <p class="text-muted">ستظهر هنا جميع إشعاراتك عند وصولها</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAsRead(id) {
    fetch(`/notifications/${id}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function deleteNotification(id) {
    if (confirm('هل أنت متأكد من حذف هذا الإشعار؟')) {
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>
@endpush
@endsection 