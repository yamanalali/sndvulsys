@extends('layouts.master')

@section('title', 'المهارات')

@section('content')
<div class="skills-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-text">
                <h1 class="page-title">
                    <i class="fas fa-tools"></i>
                    المهارات
                </h1>
                <p class="page-subtitle">إدارة المهارات المتاحة للمتطوعين</p>
            </div>
            @if(auth()->user()->user_type === 'admin')
            <div class="header-action">
                <a href="{{ route('skills.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    إضافة مهارة
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="search-section">
        <div class="search-card">
            <div class="search-content">
                <div class="search-row">
                    <div class="search-group">
                        <div class="search-input-wrapper">
                          
                            <input type="text" id="searchInput" class="search-input" placeholder="البحث في المهارات...">
                        </div>
                    </div>
                    <div class="search-group">
                        <select id="categoryFilter" class="filter-select">
                            <option value="">جميع الفئات</option>
                            <option value="تقنية">تقنية</option>
                            <option value="تعليمية">تعليمية</option>
                            <option value="طبية">طبية</option>
                            <option value="اجتماعية">اجتماعية</option>
                        </select>
                    </div>
                    <div class="search-group">
                        <button id="resetFilters" class="btn btn-secondary">
                            <i class="fas fa-undo"></i>
                            إعادة تعيين
                        </button>
                    </div>
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
            </div>
            
            <div class="skill-card-body">
                <h3 class="skill-title">{{ $skill->name }}</h3>
                <p class="skill-description">
                    {{ $skill->description ? Str::limit($skill->description, 80) : 'لا يوجد وصف متاح' }}
                </p>
                
                <div class="skill-stats">
                    <span class="stat-item">
                        <i class="fas fa-users"></i>
                        {{ $skill->users_count ?? 0 }} متطوع
                    </span>
                </div>
            </div>
            
            <div class="skill-card-actions">
                <button type="button" class="btn btn-sm btn-outline-primary" 
                        onclick="showSkillDetails('{{ $skill->id }}', '{{ $skill->name }}')">
                    <i class="fas fa-eye"></i>
                    عرض
                </button>
                @if(auth()->user()->hasSkill($skill->id))
                    <button type="button" class="btn btn-sm btn-outline-success" disabled>
                        <i class="fas fa-check"></i>
                        مضافة
                    </button>
                @else
                    <button type="button" class="btn btn-sm btn-outline-info" 
                            onclick="addSkillToUser('{{ $skill->id }}', '{{ $skill->name }}')">
                        <i class="fas fa-plus"></i>
                        إضافة لي
                    </button>
                @endif
                @if(auth()->user()->user_type === 'admin')
                <div class="admin-actions">
                    <a href="{{ route('skills.edit', $skill->id) }}" class="btn btn-sm btn-outline-warning" title="تعديل">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="deleteSkill('{{ $skill->id }}', '{{ $skill->name }}')" title="حذف">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-content">
                <div class="empty-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <h3>لا توجد مهارات حالياً</h3>
                <p>لم يتم إضافة أي مهارات بعد</p>
                @if(auth()->user()->user_type === 'admin')
                <a href="{{ route('skills.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    إضافة أول مهارة
                </a>
                @endif
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
/* Skills Container - Adjusted for sidebar layout */
.skills-container {
    padding: 70px;
    background: #f8f9fa;
    min-height: calc(100vh - 80px);
}

/* Page Header */
.page-header {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    font-size: 1.8rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 5px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.page-title i {
    color: #4e73df;
}

.page-subtitle {
    color: #6c757d;
    margin: 0;
    font-size: 0.95rem;
}

.header-action {
    flex-shrink: 0;
}

/* Search Section */
.search-section {
    margin-bottom: 20px;
}

.search-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.search-content {
    width: 100%;
}

.search-row {
    display: flex;
    gap: 15px;
    align-items: center;
}

.search-group {
    flex: 1;
}

.search-input-wrapper {
    position: relative;
}

.search-input-wrapper i {
    position: absolute;
    left: 30px;
    top: 70%;
    transform: translateY(50%);
    color: #6c757d;
    font-size: 0.9rem;
}

.search-input {
    width: 100%;
    padding: 20px 20px 10px 35px;
    border: 1px solid #d1d3e2;
    border-radius: 6px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.filter-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d3e2;
    border-radius: 6px;
    font-size: 0.9rem;
    background: white;
    cursor: pointer;
}

.filter-select:focus {
    outline: none;
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

/* Skills Grid - Compact and responsive */
.skills-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.skill-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0;
    overflow: hidden;
}

.skill-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.skill-card-header {
    padding: 15px;
    background: #f8f9fa;
    border-bottom: 1px solid #e3e6f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.skill-icon {
    width: 40px;
    height: 40px;
    background: #4e73df;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
}

.skill-badge {
    background: #e3e6f0;
    color: #5a5c69;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.skill-card-body {
    padding: 15px;
}

.skill-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 10px 0;
    line-height: 1.3;
}

.skill-description {
    color: #6c757d;
    font-size: 0.85rem;
    line-height: 1.4;
    margin: 0 0 15px 0;
    min-height: 40px;
}

.skill-stats {
    margin-bottom: 15px;
}

.stat-item {
    color: #6c757d;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.stat-item i {
    color: #4e73df;
}

.skill-card-actions {
    padding: 15px;
    border-top: 1px solid #e3e6f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.admin-actions {
    display: flex;
    gap: 8px;
}

/* Buttons */
.btn {
    border-radius: 6px;
    font-size: 0.85rem;
    padding: 8px 12px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s ease;
    border: 1px solid transparent;
    cursor: pointer;
}

.btn-sm {
    padding: 6px 10px;
    font-size: 0.8rem;
}

.btn-primary {
    background: #4e73df;
    border-color: #4e73df;
    color: white;
}

.btn-primary:hover {
    background: #2e59d9;
    border-color: #2e59d9;
    color: white;
    text-decoration: none;
}

.btn-secondary {
    background: #858796;
    border-color: #858796;
    color: white;
}

.btn-secondary:hover {
    background: #717384;
    border-color: #717384;
    color: white;
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
    padding: 60px 20px;
}

.empty-content {
    max-width: 400px;
    margin: 0 auto;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: #4e73df;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 2rem;
    color: white;
}

.empty-content h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.empty-content p {
    color: #6c757d;
    margin-bottom: 20px;
}

/* Modal */
.modal-content {
    border-radius: 8px;
    border: none;
}

.modal-header {
    background: #4e73df;
    color: white;
    border-radius: 8px 8px 0 0;
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
    padding: 20px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .skills-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}

@media (max-width: 768px) {
    .skills-container {
        padding: 15px;
    }
    
    .header-content {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .search-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .skills-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .skill-card-header {
        padding: 12px;
    }
    
    .skill-card-body {
        padding: 12px;
    }
    
    .skill-card-actions {
        padding: 12px;
        flex-direction: column;
        gap: 10px;
    }
    
    .admin-actions {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .skills-container {
        padding: 10px;
    }
    
    .page-header {
        padding: 15px;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .search-card {
        padding: 15px;
    }
    
    .skill-icon {
        width: 35px;
        height: 35px;
        font-size: 1rem;
    }
    
    .empty-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .empty-content h3 {
        font-size: 1.3rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const resetFilters = document.getElementById('resetFilters');
    const skillItems = document.querySelectorAll('.skill-item');

    // Search functionality
    searchInput.addEventListener('input', filterSkills);
    categoryFilter.addEventListener('change', filterSkills);
    resetFilters.addEventListener('click', resetAllFilters);

    function filterSkills() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;

        skillItems.forEach(item => {
            const skillName = item.dataset.name;
            const skillCategory = item.dataset.category;
            
            const matchesSearch = skillName.includes(searchTerm);
            const matchesCategory = !selectedCategory || skillCategory === selectedCategory;
            
            if (matchesSearch && matchesCategory) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function resetAllFilters() {
        searchInput.value = '';
        categoryFilter.value = '';
        skillItems.forEach(item => {
            item.style.display = 'block';
        });
    }
});

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
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary">اسم المهارة:</h6>
                    <p class="mb-3">${skillName}</p>
                    
                    <h6 class="fw-bold text-primary">الفئة:</h6>
                    <p class="mb-3">تقنية</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary">الوصف:</h6>
                    <p class="mb-3">وصف تفصيلي للمهارة وأهميتها في العمل التطوعي</p>
                    
                    <h6 class="fw-bold text-primary">عدد المتطوعين:</h6>
                    <p class="mb-3">15 متطوع</p>
                </div>
            </div>
        `;
    }, 1000);
}

function deleteSkill(skillId, skillName) {
    if (confirm(`هل أنت متأكد من حذف المهارة "${skillName}"؟`)) {
        console.log('Deleting skill:', skillId);
        alert('تم حذف المهارة بنجاح');
    }
}

function addSkillToUser(skillId, skillName) {
    if (confirm(`هل تريد إضافة المهارة "${skillName}" إلى ملفك الشخصي؟`)) {
        // إرسال طلب إضافة المهارة للمستخدم
        fetch(`/skills/${skillId}/add-to-user`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تمت إضافة المهارة إلى ملفك الشخصي بنجاح');
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ أثناء إضافة المهارة');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إضافة المهارة');
        });
    }
}
</script>
@endsection 