@extends('layouts.master')

@section('content')
<div class="documents-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-text">
                <h1 class="page-title">
                    <i class="fas fa-file-alt"></i>
                    المستندات والملفات
                </h1>
                <p class="page-subtitle">إدارة وتنظيم المستندات والملفات المرفوعة</p>
            </div>
            <div class="header-action">
                <a href="{{ route('documents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    رفع مستند جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="search-section">
        <div class="search-card">
            <div class="search-content">
                <div class="search-row">
                    <div class="search-group">
                        <div class="search-input-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" class="search-input" placeholder="البحث في المستندات...">
                        </div>
                    </div>
                    <div class="search-group">
                        <select id="typeFilter" class="filter-select">
                            <option value="">جميع الأنواع</option>
                            <option value="pdf">PDF</option>
                            <option value="docx">DOCX</option>
                            <option value="zip">ZIP</option>
                        </select>
                    </div>
                    <div class="search-group">
                        <button id="resetFilters" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i>
                            إعادة تعيين
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Grid -->
    <div class="documents-grid">
        @forelse($documents as $document)
        <div class="document-card document-item" 
             data-name="{{ strtolower($document->title) }}" 
             data-type="{{ pathinfo($document->file_path, PATHINFO_EXTENSION) }}">
            
            <!-- Document Header -->
            <div class="document-card-header">
                <div class="document-icon">
                    @php
                        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                        $iconClass = match($extension) {
                            'pdf' => 'fas fa-file-pdf',
                            'docx' => 'fas fa-file-word',
                            'zip' => 'fas fa-file-archive',
                            default => 'fas fa-file'
                        };
                        $iconColor = match($extension) {
                            'pdf' => '#dc3545',
                            'docx' => '#0d6efd',
                            'zip' => '#fd7e14',
                            default => '#6c757d'
                        };
                    @endphp
                    <i class="{{ $iconClass }}" style="color: {{ $iconColor }};"></i>
                </div>
                <div class="document-menu">
                    <button class="document-menu-btn" onclick="toggleDocumentMenu({{ $document->id }})">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="document-menu-dropdown" id="menu-{{ $document->id }}">
                        <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="menu-item">
                            <i class="fas fa-eye"></i>
                            عرض
                        </a>
                        <a href="{{ asset('storage/'.$document->file_path) }}" download class="menu-item">
                            <i class="fas fa-download"></i>
                            تحميل
                        </a>
                        <button onclick="deleteDocument({{ $document->id }}, '{{ $document->title }}')" class="menu-item text-danger">
                            <i class="fas fa-trash"></i>
                            حذف
                        </button>
                    </div>
                </div>
            </div>

            <!-- Document Body -->
            <div class="document-card-body">
                <h5 class="document-title">{{ $document->title }}</h5>
                <div class="document-meta">
                    <span class="document-type">{{ strtoupper($extension) }}</span>
                    <span class="document-size">{{ number_format(filesize(storage_path('app/public/' . $document->file_path)) / 1024, 1) }} KB</span>
                </div>
                <div class="document-info">
                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $document->created_at->format('Y/m/d') }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <span>{{ $document->created_at->format('H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Document Actions -->
            <div class="document-card-actions">
                <button onclick="showDocumentDetails({{ $document->id }}, '{{ $document->title }}')" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-info-circle"></i>
                    تفاصيل
                </button>
                <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="btn btn-primary btn-sm">
                    <i class="fas fa-external-link-alt"></i>
                    فتح
                </a>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-content">
                <div class="empty-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3>لا توجد مستندات</h3>
                <p>لم يتم رفع أي مستندات بعد. ابدأ برفع أول مستند لك.</p>
                <a href="{{ route('documents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    رفع مستند جديد
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Document Details Modal -->

<style>
/* Documents Container */
.documents-container {
    padding: 70px;
    background: #f8f9fa;
    min-height: calc(100vh - 80px);
}

/* Page Header */
.page-header {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
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
    font-size: 1.8rem;
    font-weight: 600;
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
    font-size: 0.95rem;
}

.header-action {
    flex-shrink: 0;
}

/* Search Section */
.search-section {
    margin-bottom: 25px;
}

.search-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.search-content {
    width: 100%;
}

.search-row {
    display: flex;
    gap: 20px;
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
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #718096;
    font-size: 0.9rem;
}

.search-input {
    width: 100%;
    padding: 12px 15px 12px 40px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #4e73df;
    box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
}

.filter-select {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.9rem;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-select:focus {
    outline: none;
    border-color: #4e73df;
    box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
}

/* Documents Grid */
.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
}

.document-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.document-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.document-card-header {
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.document-icon {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.document-menu {
    position: relative;
}

.document-menu-btn {
    background: none;
    border: none;
    color: #718096;
    font-size: 1rem;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.document-menu-btn:hover {
    background: #e2e8f0;
    color: #4a5568;
}

.document-menu-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    min-width: 150px;
    z-index: 1000;
    display: none;
    border: 1px solid #e2e8f0;
}

.document-menu-dropdown.show {
    display: block;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    color: #4a5568;
    text-decoration: none;
    border: none;
    background: none;
    width: 100%;
    text-align: right;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.menu-item:hover {
    background: #f7fafc;
    color: #2d3748;
}

.menu-item.text-danger:hover {
    background: #fed7d7;
    color: #c53030;
}

.document-card-body {
    padding: 20px;
}

.document-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 12px 0;
    line-height: 1.4;
}

.document-meta {
    display: flex;
    gap: 12px;
    margin-bottom: 15px;
}

.document-type {
    background: #e2e8f0;
    color: #4a5568;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.document-size {
    background: #f0fff4;
    color: #38a169;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.document-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #718096;
    font-size: 0.85rem;
}

.info-item i {
    width: 16px;
    color: #a0aec0;
}

.document-card-actions {
    padding: 20px;
    background: #f8f9fa;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 10px;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
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
    background: #2e59d9;
    border-color: #2e59d9;
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

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
    background: transparent;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    border-color: #6c757d;
    color: white;
}

/* Empty State */
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
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
    margin: 0 auto 25px;
    font-size: 2rem;
    color: white;
}

.empty-content h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 12px;
}

.empty-content p {
    color: #718096;
    margin-bottom: 25px;
    line-height: 1.6;
}

/* Modal */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.modal-header {
    background: #4e73df;
    color: white;
    border-radius: 12px 12px 0 0;
    border: none;
    padding: 40px;
}

.modal-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 400;
    font-size: 1.0rem;
    margin: 40px;
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
    .documents-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    .documents-container {
        padding: 20px;
    }
    
    .header-content {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .search-row {
        flex-direction: column;
        gap: 15px;
    }
    
    .documents-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .document-card-header {
        padding: 15px;
    }
    
    .document-card-body {
        padding: 15px;
    }
    
    .document-card-actions {
        padding: 15px;
        flex-direction: column;
        gap: 10px;
    }
    
    .document-meta {
        flex-direction: column;
        gap: 8px;
    }
}

@media (max-width: 576px) {
    .documents-container {
        padding: 15px;
    }
    
    .page-header {
        padding: 20px;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .search-card {
        padding: 20px;
    }
    
    .document-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
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
    const typeFilter = document.getElementById('typeFilter');
    const resetFilters = document.getElementById('resetFilters');
    const documentItems = document.querySelectorAll('.document-item');

    // Search functionality
    searchInput.addEventListener('input', filterDocuments);
    typeFilter.addEventListener('change', filterDocuments);
    resetFilters.addEventListener('click', resetAllFilters);

    function filterDocuments() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedType = typeFilter.value;

        documentItems.forEach(item => {
            const documentName = item.dataset.name;
            const documentType = item.dataset.type;
            
            const matchesSearch = documentName.includes(searchTerm);
            const matchesType = !selectedType || documentType === selectedType;
            
            if (matchesSearch && matchesType) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function resetAllFilters() {
        searchInput.value = '';
        typeFilter.value = '';
        documentItems.forEach(item => {
            item.style.display = 'block';
        });
    }
});

function toggleDocumentMenu(documentId) {
    const menu = document.getElementById(`menu-${documentId}`);
    const allMenus = document.querySelectorAll('.document-menu-dropdown');
    
    // Close all other menus
    allMenus.forEach(m => {
        if (m.id !== `menu-${documentId}`) {
            m.classList.remove('show');
        }
    });
    
    // Toggle current menu
    menu.classList.toggle('show');
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.document-menu')) {
        document.querySelectorAll('.document-menu-dropdown').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

function showDocumentDetails(documentId, documentName) {
    const modalBody = document.getElementById('documentModalBody');
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
            <p class="text-muted">جاري تحميل تفاصيل المستند...</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('documentModal'));
    modal.show();
    
    setTimeout(() => {
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary">اسم الملف:</h6>
                    <p class="mb-3">${documentName}</p>
                    
                    <h6 class="fw-bold text-primary">نوع الملف:</h6>
                    <p class="mb-3">PDF</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary">حجم الملف:</h6>
                    <p class="mb-3">245.6 KB</p>
                    
                    <h6 class="fw-bold text-primary">تاريخ الرفع:</h6>
                    <p class="mb-3">2024/01/15 - 14:30</p>
                </div>
            </div>
        `;
    }, 1000);
}

function deleteDocument(documentId, documentName) {
    if (confirm(`هل أنت متأكد من حذف المستند "${documentName}"؟`)) {
        // إرسال طلب حذف المستند
        fetch(`/documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم حذف المستند بنجاح');
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ أثناء حذف المستند');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حذف المستند');
        });
    }
}
</script>
@endsection 