@extends('layouts.master')

@section('title', 'إعدادات الإشعارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 rounded p-3">
                            <i data-feather="settings" class="text-primary" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h1 class="h3 mb-1 text-dark">إعدادات الإشعارات</h1>
                            <p class="text-muted mb-0">تخصيص إعدادات الإشعارات حسب احتياجاتك</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Settings Form -->
            <form action="{{ route('notifications.update-settings') }}" method="POST">
                @csrf
                
                <!-- إشعارات التخصيص -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                            <i data-feather="user-plus" class="text-primary" style="width: 20px; height: 20px;"></i>
                            إشعارات التخصيص
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="assignment_notifications" id="assignment_notifications" 
                                           class="form-check-input" 
                                           {{ $settings->assignment_notifications ? 'checked' : '' }}>
                                    <label for="assignment_notifications" class="form-check-label">
                                        تفعيل إشعارات التخصيص
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="assignment_email" id="assignment_email" 
                                           class="form-check-input" 
                                           {{ $settings->assignment_email ? 'checked' : '' }}>
                                    <label for="assignment_email" class="form-check-label">
                                        إشعارات البريد الإلكتروني
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="assignment_database" id="assignment_database" 
                                           class="form-check-input" 
                                           {{ $settings->assignment_database ? 'checked' : '' }}>
                                    <label for="assignment_database" class="form-check-label">
                                        إشعارات قاعدة البيانات
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إشعارات تحديث الحالة -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                            <i data-feather="refresh-cw" class="text-primary" style="width: 20px; height: 20px;"></i>
                            إشعارات تحديث الحالة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="status_update_notifications" id="status_update_notifications" 
                                           class="form-check-input" 
                                           {{ $settings->status_update_notifications ? 'checked' : '' }}>
                                    <label for="status_update_notifications" class="form-check-label">
                                        تفعيل إشعارات تحديث الحالة
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="status_update_email" id="status_update_email" 
                                           class="form-check-input" 
                                           {{ $settings->status_update_email ? 'checked' : '' }}>
                                    <label for="status_update_email" class="form-check-label">
                                        إشعارات البريد الإلكتروني
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="status_update_database" id="status_update_database" 
                                           class="form-check-input" 
                                           {{ $settings->status_update_database ? 'checked' : '' }}>
                                    <label for="status_update_database" class="form-check-label">
                                        إشعارات قاعدة البيانات
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إشعارات التذكيرات -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                            <i data-feather="clock" class="text-primary" style="width: 20px; height: 20px;"></i>
                            إشعارات التذكيرات
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="deadline_reminder_notifications" id="deadline_reminder_notifications" 
                                           class="form-check-input" 
                                           {{ $settings->deadline_reminder_notifications ? 'checked' : '' }}>
                                    <label for="deadline_reminder_notifications" class="form-check-label">
                                        تفعيل إشعارات التذكيرات
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="deadline_reminder_email" id="deadline_reminder_email" 
                                           class="form-check-input" 
                                           {{ $settings->deadline_reminder_email ? 'checked' : '' }}>
                                    <label for="deadline_reminder_email" class="form-check-label">
                                        إشعارات البريد الإلكتروني
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="deadline_reminder_database" id="deadline_reminder_database" 
                                           class="form-check-input" 
                                           {{ $settings->deadline_reminder_database ? 'checked' : '' }}>
                                    <label for="deadline_reminder_database" class="form-check-label">
                                        إشعارات قاعدة البيانات
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="deadline_reminder_days" class="form-label">عدد الأيام قبل الموعد النهائي</label>
                                <input type="number" name="deadline_reminder_days" id="deadline_reminder_days" 
                                       class="form-control" min="0" max="30" 
                                       value="{{ $settings->deadline_reminder_days }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إشعارات التبعيات -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                            <i data-feather="link" class="text-primary" style="width: 20px; height: 20px;"></i>
                            إشعارات التبعيات
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="dependency_notifications" id="dependency_notifications" 
                                           class="form-check-input" 
                                           {{ $settings->dependency_notifications ? 'checked' : '' }}>
                                    <label for="dependency_notifications" class="form-check-label">
                                        تفعيل إشعارات التبعيات
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="dependency_email" id="dependency_email" 
                                           class="form-check-input" 
                                           {{ $settings->dependency_email ? 'checked' : '' }}>
                                    <label for="dependency_email" class="form-check-label">
                                        إشعارات البريد الإلكتروني
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="dependency_database" id="dependency_database" 
                                           class="form-check-input" 
                                           {{ $settings->dependency_database ? 'checked' : '' }}>
                                    <label for="dependency_database" class="form-check-label">
                                        إشعارات قاعدة البيانات
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إعدادات عامة -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                            <i data-feather="settings" class="text-primary" style="width: 20px; height: 20px;"></i>
                            إعدادات عامة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="email_notifications" id="email_notifications" 
                                           class="form-check-input" 
                                           {{ $settings->email_notifications ? 'checked' : '' }}>
                                    <label for="email_notifications" class="form-check-label">
                                        تفعيل إشعارات البريد الإلكتروني
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="database_notifications" id="database_notifications" 
                                           class="form-check-input" 
                                           {{ $settings->database_notifications ? 'checked' : '' }}>
                                    <label for="database_notifications" class="form-check-label">
                                        تفعيل إشعارات قاعدة البيانات
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="browser_notifications" id="browser_notifications" 
                                           class="form-check-input" 
                                           {{ $settings->browser_notifications ? 'checked' : '' }}>
                                    <label for="browser_notifications" class="form-check-label">
                                        تفعيل إشعارات المتصفح
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-2" style="width: 16px; height: 16px;"></i>
                                حفظ الإعدادات
                            </button>
                            
                            <button type="button" onclick="resetSettings()" class="btn btn-outline-secondary">
                                <i data-feather="refresh-cw" class="me-2" style="width: 16px; height: 16px;"></i>
                                إعادة تعيين
                            </button>
                            
                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary">
                                <i data-feather="arrow-left" class="me-2" style="width: 16px; height: 16px;"></i>
                                العودة للإشعارات
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function resetSettings() {
    if (confirm('هل أنت متأكد من إعادة تعيين جميع إعدادات الإشعارات إلى الافتراضية؟')) {
        fetch('{{ route("notifications.reset-settings") }}', {
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
}
</script>
@endpush
@endsection 