@extends('layouts.app')

@section('title', 'تاريخ المهام')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">
                    <i class="fas fa-history text-primary"></i>
                    تاريخ المهام والأنشطة
                </h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('volunteer.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active">تاريخ المهام</li>
                </ul>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('task-history.export', ['format' => 'json']) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download"></i> تصدير JSON
                    </a>
                    <a href="{{ route('task-history.export', ['format' => 'csv']) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('task-history.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">البحث في المهام</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ $search }}" placeholder="ابحث في عناوين المهام...">
                        </div>
                        <div class="col-md-3">
                            <label for="filter" class="form-label">نوع الإجراء</label>
                            <select class="form-select" id="filter" name="filter">
                                <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>جميع الإجراءات</option>
                                @foreach($actionTypes as $actionType)
                                    <option value="{{ $actionType }}" {{ $filter === $actionType ? 'selected' : '' }}>
                                        {{ $actionType }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <a href="{{ route('task-history.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh"></i> إعادة تعيين
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- History List -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i>
                        سجل الأنشطة ({{ $history->total() }} إجراء)
                    </h5>
                </div>
                <div class="card-body">
                    @if($history->count() > 0)
                        <div class="timeline-container">
                            @foreach($history as $record)
                                <div class="timeline-item" data-action-type="{{ $record->action_type }}">
                                    <div class="timeline-marker timeline-marker-{{ $record->action_color }}">
                                        <i class="{{ $record->action_icon }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="timeline-title">
                                                    {{ $record->task->title }}
                                                </h6>
                                                <span class="timeline-time">{{ $record->time_ago }}</span>
                                            </div>
                                            <div class="timeline-subtitle">
                                                <span class="badge bg-{{ $record->action_color }}">{{ $record->action_description }}</span>
                                                @if($record->task->project)
                                                    <span class="text-muted ms-2">
                                                        <i class="fas fa-project-diagram"></i>
                                                        {{ $record->task->project->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="timeline-body">
                                            <p class="timeline-description">{{ $record->description }}</p>
                                            
                                            @if($record->field_name && ($record->old_value || $record->new_value))
                                                <div class="timeline-changes">
                                                    <div class="change-item">
                                                        <span class="change-label">{{ $record->field_name }}:</span>
                                                        <span class="change-old">{{ $record->formatted_old_value }}</span>
                                                        <i class="fas fa-arrow-right text-muted mx-2"></i>
                                                        <span class="change-new">{{ $record->formatted_new_value }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($record->metadata)
                                                <div class="timeline-metadata">
                                                    @foreach($record->metadata as $key => $value)
                                                        <span class="badge bg-light text-dark me-1">
                                                            {{ $key }}: {{ $value }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="timeline-footer">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="user-info">
                                                    <i class="fas fa-user text-muted"></i>
                                                    <span class="text-muted">{{ $record->user ? $record->user->name : 'النظام' }}</span>
                                                </div>
                                                <div class="timeline-actions">
                                                    <a href="{{ route('task-history.timeline', $record->task) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-clock"></i> الجدول الزمني
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $history->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h5>لا توجد أنشطة مسجلة</h5>
                                <p class="text-muted">لم يتم العثور على أي أنشطة أو تغييرات في المهام.</p>
                                <a href="{{ route('volunteer.dashboard') }}" class="btn btn-primary">
                                    <i class="fas fa-home"></i> العودة للوحة التحكم
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Modal -->
<div class="modal fade" id="activityStatsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-chart-bar"></i>
                    ملخص الأنشطة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="activityStatsContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline-container {
    position: relative;
    padding-left: 30px;
}

.timeline-container::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #e9ecef 0%, #007bff 50%, #e9ecef 100%);
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 2;
}

.timeline-marker-success { background: linear-gradient(45deg, #28a745, #20c997); }
.timeline-marker-info { background: linear-gradient(45deg, #17a2b8, #6f42c1); }
.timeline-marker-warning { background: linear-gradient(45deg, #ffc107, #fd7e14); }
.timeline-marker-primary { background: linear-gradient(45deg, #007bff, #0056b3); }
.timeline-marker-danger { background: linear-gradient(45deg, #dc3545, #c82333); }
.timeline-marker-secondary { background: linear-gradient(45deg, #6c757d, #495057); }

.timeline-content {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.timeline-content:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.timeline-header {
    margin-bottom: 15px;
}

.timeline-title {
    margin: 0;
    color: #2c3e50;
    font-weight: 600;
}

.timeline-time {
    font-size: 12px;
    color: #6c757d;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 12px;
}

.timeline-subtitle {
    margin-top: 8px;
}

.timeline-description {
    margin: 0 0 15px 0;
    color: #495057;
    line-height: 1.6;
}

.timeline-changes {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 15px;
}

.change-item {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.change-label {
    font-weight: 600;
    color: #495057;
    min-width: 80px;
}

.change-old {
    background: #fff3cd;
    color: #856404;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.change-new {
    background: #d1ecf1;
    color: #0c5460;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.timeline-metadata {
    margin-bottom: 15px;
}

.timeline-footer {
    border-top: 1px solid #e9ecef;
    padding-top: 15px;
    margin-top: 15px;
}

.user-info {
    font-size: 12px;
}

.timeline-actions {
    display: flex;
    gap: 8px;
}

.empty-state {
    color: #6c757d;
}

.empty-state i {
    opacity: 0.5;
}

/* Responsive Design */
@media (max-width: 768px) {
    .timeline-container {
        padding-left: 20px;
    }
    
    .timeline-container::before {
        left: 10px;
    }
    
    .timeline-marker {
        left: -17px;
        width: 24px;
        height: 24px;
        font-size: 10px;
    }
    
    .timeline-content {
        padding: 15px;
    }
    
    .change-item {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize timeline animations
    const timelineItems = document.querySelectorAll('.timeline-item');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateX(0)';
            }
        });
    }, { threshold: 0.1 });
    
    timelineItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        item.style.transition = 'all 0.5s ease';
        observer.observe(item);
    });
    
    // Filter functionality
    const filterSelect = document.getElementById('filter');
    const searchInput = document.getElementById('search');
    
    function applyFilters() {
        const filterValue = filterSelect.value;
        const searchValue = searchInput.value.toLowerCase();
        
        timelineItems.forEach(item => {
            const actionType = item.dataset.actionType;
            const title = item.querySelector('.timeline-title').textContent.toLowerCase();
            
            const matchesFilter = filterValue === 'all' || actionType === filterValue;
            const matchesSearch = !searchValue || title.includes(searchValue);
            
            if (matchesFilter && matchesSearch) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    filterSelect.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);
});
</script>
@endpush 