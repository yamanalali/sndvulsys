@extends('layouts.master')

@section('title', 'مهاراتي')

@section('content')
<div class="skills-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-text">
                <h1 class="page-title">
                    <i class="fas fa-user-graduate"></i>
                    مهاراتي
                </h1>
                <p class="page-subtitle">عرض وإدارة مهاراتك الشخصية</p>
            </div>
            <div class="header-action">
                <a href="{{ route('skills.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    إضافة مهارة جديدة
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-section">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $skills->count() }}</h3>
                    <p>إجمالي المهارات</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon verified">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $skills->where('pivot.is_verified', true)->count() }}</h3>
                    <p>مهارات موثقة</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon available">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $skills->where('available_for_volunteering', true)->count() }}</h3>
                    <p>متاحة للتطوع</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Skills Grid -->
    <div class="skills-grid" id="skillsContainer">
        @forelse($skills as $skill)
        <div class="skill-card skill-item" data-name="{{ strtolower($skill->name) }}" data-category="{{ $skill->category }}">
            <div class="skill-card-header">
                <div class="skill-icon">
                    @switch($skill->category)
                        @case('تقنية')
                            <i class="fas fa-laptop-code"></i>
                            @break
                        @case('تعليمية')
                            <i class="fas fa-chalkboard-teacher"></i>
                            @break
                        @case('طبية')
                            <i class="fas fa-heartbeat"></i>
                            @break
                        @case('اجتماعية')
                            <i class="fas fa-hands-helping"></i>
                            @break
                        @default
                            <i class="fas fa-star"></i>
                    @endswitch
                </div>
                <div class="skill-badge">{{ $skill->category }}</div>
                @if($skill->pivot->is_verified)
                <div class="verification-badge">
                    <i class="fas fa-check-circle"></i>
                    موثقة
                </div>
                @endif
            </div>
            
            <div class="skill-card-body">
                <h3 class="skill-title">{{ $skill->name }}</h3>
                <p class="skill-description">
                    {{ $skill->description ? Str::limit($skill->description, 80) : 'لا يوجد وصف متاح' }}
                </p>
                
                <div class="skill-details">
                    @if($skill->pivot->skill_level)
                    <div class="detail-item">
                        <span class="detail-label">مستواك:</span>
                        <span class="detail-value">{{ $skill->pivot->skill_level }}</span>
                    </div>
                    @endif
                    
                    @if($skill->pivot->experience_years)
                    <div class="detail-item">
                        <span class="detail-label">خبرتك:</span>
                        <span class="detail-value">{{ $skill->pivot->experience_years }}</span>
                    </div>
                    @endif
                    
                    @if($skill->pivot->notes)
                    <div class="detail-item">
                        <span class="detail-label">ملاحظات:</span>
                        <span class="detail-value">{{ Str::limit($skill->pivot->notes, 50) }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="skill-card-actions">
                <button type="button" class="btn btn-sm btn-outline-primary" 
                        onclick="showSkillDetails('{{ $skill->id }}', '{{ $skill->name }}')">
                    <i class="fas fa-eye"></i>
                    عرض
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning" 
                        onclick="editUserSkill('{{ $skill->id }}', '{{ $skill->name }}')">
                    <i class="fas fa-edit"></i>
                    تعديل
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" 
                        onclick="removeSkill('{{ $skill->id }}', '{{ $skill->name }}')">
                    <i class="fas fa-trash"></i>
                    إزالة
                </button>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-content">
                <div class="empty-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>لا توجد مهارات حالياً</h3>
                <p>لم تقم بإضافة أي مهارات بعد. ابدأ بإضافة مهاراتك الأولى!</p>
                <a href="{{ route('skills.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    إضافة أول مهارة
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Skill Details Modal -->
<div class="modal fade" id="skillModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i>
                    تفاصيل المهارة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="skillModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
/* Skills Container */
.skills-container {
    padding: 70px;
    background: #f8f9fa;
    min-height: calc(100vh - 80px);
}

/* Page Header */
.page-header {
    background: white;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-text {
    flex: 1;
}

.page-title {
    font-size: 1.0rem;
    font-weight: 400;
    color: #2d3748;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-title i {
    color: #4e73df;
}

.page-subtitle {
    color: #718096;
    margin: 0;
    font-size: 0.90rem;
}



/* Stats Section */
.stats-section {
    margin-bottom: 25px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    background: #4e73df;
}

.stat-icon.verified {
    background: #10b981;
}

.stat-icon.available {
    background: #f59e0b;
}

.stat-content h3 {
    font-size: 1.5rem;
    font-weight: 500;
    color: #2d3748;
    margin: 0 0 5px 0;
}

.stat-content p {
    color: #718096;
    margin: 0;
    font-size: 0.95rem;
}

/* Skills Grid */
.skills-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.skill-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0;
    overflow: hidden;
}

.skill-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.skill-card-header {
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e3e6f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.skill-icon {
    width: 50px;
    height: 50px;
    background: #4e73df;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
}

.skill-badge {
    background: #e3e6f0;
    color: #5a5c69;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.verification-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #10b981;
    color: white;
    padding: 4px 8px;
    border-radius: 10px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

.skill-card-body {
    padding: 20px;
}

.skill-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 15px 0;
    line-height: 1.3;
}

.skill-description {
    color: #718096;
    font-size: 0.9rem;
    line-height: 1.5;
    margin: 0 0 20px 0;
}

.skill-details {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: #f7fafc;
    border-radius: 8px;
    border-right: 3px solid #4e73df;
}

.detail-label {
    font-weight: 600;
    color: #4a5568;
    font-size: 0.85rem;
}

.detail-value {
    color: #2d3748;
    font-size: 0.9rem;
}

.skill-card-actions {
    padding: 20px;
    border-top: 1px solid #e3e6f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

/* Buttons */
.btn {
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    cursor: pointer;
}

.btn-sm {
    padding: 8px 12px;
    font-size: 0.8rem;
}

.btn-primary {
    background: #4e73df;
    border-color: #4e73df;
    color: white;
}

.btn-primary:hover {
    background: #224abe;
    border-color: #224abe;
    color: white;
    text-decoration: none;
}

.btn-outline-primary {
    color: #4e73df;
    border-color: #4e73df;
    background: transparent;
}

.btn-outline-primary:hover {
    background: #4e73df;
    border-color: #4e73df;
    color: white;
}

.btn-outline-warning {
    color: #f6c23e;
    border-color: #f6c23e;
    background: transparent;
}

.btn-outline-warning:hover {
    background: #f6c23e;
    border-color: #f6c23e;
    color: white;
}

.btn-outline-danger {
    color: #e74a3b;
    border-color: #e74a3b;
    background: transparent;
}

.btn-outline-danger:hover {
    background: #e74a3b;
    border-color: #e74a3b;
    color: white;
}

/* Empty State */
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 10px;
}

.empty-content {
    max-width: 300px;
    margin: 0 auto;
}

.empty-icon {
    width: 60px;
    height: 60px;
    background: #4e73df;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
    font-size: 2.0rem;
    color: white;
}

.empty-content h3 {
    font-size: 1.0rem;
    font-weight: 300;
    color: #2d3748;
    margin-bottom: 10px;
}

.empty-content p {
    color: #718096;
    margin-bottom: 15px;
    font-size: 1.0rem;
    line-height: 1.0;
}

/* Modal */
.modal-content {
    border-radius: 12px;
    border: none;
}

.modal-header {
    background: #4e73df;
    color: white;
    border-radius: 12px 12px 0 0;
    border: none;
}

.modal-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.btn-close {
    filter: invert(1);
    opacity: 0.8;
}

.modal-body {
    padding: 25px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .skills-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    .skills-container {
        padding: 20px;
    }
    
    .header-content {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .page-title {
        font-size: 0.8rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .skills-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .skill-card-header {
        padding: 15px;
    }
    
    .skill-card-body {
        padding: 15px;
    }
    
    .skill-card-actions {
        padding: 15px;
        flex-direction: column;
        gap: 10px;
    }
    
    .btn {
        justify-content: center;
        width: 100%;
    }
}

@media (max-width: 576px) {
    .skills-container {
        padding: 15px;
    }
    
    .page-header {
        padding: 30px;
    }
    
    .stat-card {
        padding: 20px;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
    
    .stat-content h3 {
        font-size: 1.5rem;
    }
    
    .empty-icon {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }
    
    .empty-content h3 {
        font-size: 1.0rem;
    }
}
</style>

<script>
function showSkillDetails(skillId, skillName) {
    const modalBody = document.getElementById('skillModalBody');
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
            <p class="text-muted">جاري تحميل تفاصيل المهارة...</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('skillModal'));
    modal.show();
    
    setTimeout(() => {
        modalBody.innerHTML = `
            <div class="skill-details-modal">
                <div class="detail-section">
                    <h6 class="detail-title">معلومات المهارة</h6>
                    <div class="detail-row">
                        <span class="detail-label">اسم المهارة:</span>
                        <span class="detail-value">${skillName}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">الفئة:</span>
                        <span class="detail-value">تقنية</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">مستواك:</span>
                        <span class="detail-value">متوسط</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">خبرتك:</span>
                        <span class="detail-value">3-5 سنوات</span>
                    </div>
                </div>
            </div>
        `;
    }, 1000);
}

function editUserSkill(skillId, skillName) {
    // يمكن إضافة منطق تعديل مهارة المستخدم هنا
    alert(`تعديل مهارة: ${skillName}`);
}

function removeSkill(skillId, skillName) {
    if (confirm(`هل أنت متأكد من إزالة المهارة "${skillName}" من ملفك الشخصي؟`)) {
        // إرسال طلب حذف
        fetch(`/skills/${skillId}/remove-from-user`, {
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
            } else {
                alert('حدث خطأ أثناء إزالة المهارة');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إزالة المهارة');
        });
    }
}

// Add CSS for modal details
const modalStyle = document.createElement('style');
modalStyle.textContent = `
    .skill-details-modal {
        padding: 0;
    }
    
    .detail-section {
        background: #f7fafc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .detail-title {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: 600;
        color: #4a5568;
    }
    
    .detail-value {
        color: #2d3748;
    }
`;
document.head.appendChild(modalStyle);
</script>
@endsection 