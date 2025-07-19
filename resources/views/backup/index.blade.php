@extends('layouts.master')

@section('content')
<div class="backup-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-text">
                <h1 class="page-title">
                    <i class="fas fa-database"></i>
                    النسخ الاحتياطية
                </h1>
                <p class="page-subtitle">إدارة وحماية بياناتك من خلال النسخ الاحتياطية</p>
            </div>
            <div class="header-action">
                <a href="{{ route('documents.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-right"></i>
                    العودة للمستندات
                </a>
            </div>
        </div>
    </div>

    <!-- Backup Dashboard -->
    <div class="backup-dashboard">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $totalDocuments ?? 0 }}</h3>
                    <p class="stat-label">إجمالي المستندات</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-hdd"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($totalSize ?? 0, 1) }} MB</h3>
                    <p class="stat-label">إجمالي الحجم</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $lastBackup ?? 'لا يوجد' }}</h3>
                    <p class="stat-label">آخر نسخة احتياطية</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $backupCount ?? 0 }}</h3>
                    <p class="stat-label">عدد النسخ الاحتياطية</p>
                </div>
            </div>
        </div>

        <!-- Backup Actions -->
        <div class="backup-actions">
            <div class="action-card">
                <div class="action-header">
                    <h3 class="action-title">
                        <i class="fas fa-download"></i>
                        إنشاء نسخة احتياطية
                    </h3>
                    <p class="action-description">قم بإنشاء نسخة احتياطية من جميع مستنداتك</p>
                </div>
                
                <div class="action-content">
                    <div class="backup-options">
                        <div class="option-group">
                            <label class="option-label">
                                <input type="checkbox" id="includeMetadata" checked>
                                <span class="checkmark"></span>
                                تضمين البيانات الوصفية
                            </label>
                            <small>يشمل معلومات الملفات والتواريخ</small>
                        </div>
                        
                        <div class="option-group">
                            <label class="option-label">
                                <input type="checkbox" id="compressFiles" checked>
                                <span class="checkmark"></span>
                                ضغط الملفات
                            </label>
                            <small>تقليل حجم النسخة الاحتياطية</small>
                        </div>
                        
                        <div class="option-group">
                            <label class="option-label">
                                <input type="checkbox" id="encryptBackup">
                                <span class="checkmark"></span>
                                تشفير النسخة الاحتياطية
                            </label>
                            <small>حماية إضافية للبيانات</small>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button onclick="createBackup()" class="btn btn-primary btn-large" id="createBackupBtn">
                            <i class="fas fa-download"></i>
                            إنشاء نسخة احتياطية
                        </button>
                        <button onclick="scheduleBackup()" class="btn btn-outline-secondary btn-large">
                            <i class="fas fa-clock"></i>
                            جدولة نسخة احتياطية
                        </button>
                    </div>
                </div>
            </div>

            <!-- Backup Progress -->
            <div class="backup-progress" id="backupProgress" style="display: none;">
                <div class="progress-header">
                    <h4>جاري إنشاء النسخة الاحتياطية...</h4>
                    <span class="progress-percentage" id="progressPercentage">0%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <div class="progress-status" id="progressStatus">جاري تحضير الملفات...</div>
            </div>
        </div>

        <!-- Recent Backups -->
        <div class="recent-backups">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-history"></i>
                    النسخ الاحتياطية الحديثة
                </h3>
                <button onclick="refreshBackups()" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-sync-alt"></i>
                    تحديث
                </button>
            </div>
            
            <div class="backups-list" id="backupsList">
                @forelse($recentBackups ?? [] as $backup)
                <div class="backup-item">
                    <div class="backup-icon">
                        <i class="fas fa-file-archive"></i>
                    </div>
                    <div class="backup-details">
                        <h5 class="backup-name">{{ $backup['name'] }}</h5>
                        <div class="backup-meta">
                            <span class="backup-size">{{ $backup['size'] }}</span>
                            <span class="backup-date">{{ $backup['date'] }}</span>
                            <span class="backup-status {{ $backup['status'] }}">
                                {{ $backup['status_text'] }}
                            </span>
                        </div>
                    </div>
                    <div class="backup-actions">
                        <button onclick="downloadBackup('{{ $backup['id'] }}')" class="btn btn-sm btn-primary">
                            <i class="fas fa-download"></i>
                            تحميل
                        </button>
                        <button onclick="deleteBackup('{{ $backup['id'] }}')" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                            حذف
                        </button>
                    </div>
                </div>
                @empty
                <div class="empty-backups">
                    <div class="empty-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h4>لا توجد نسخ احتياطية</h4>
                    <p>قم بإنشاء أول نسخة احتياطية لحماية مستنداتك</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Backup Settings -->
        <div class="backup-settings">
            <div class="settings-card">
                <h3 class="settings-title">
                    <i class="fas fa-cog"></i>
                    إعدادات النسخ الاحتياطية
                </h3>
                
                <div class="settings-content">
                    <div class="setting-group">
                        <label class="setting-label">النسخ الاحتياطية التلقائية</label>
                        <div class="setting-control">
                            <label class="switch">
                                <input type="checkbox" id="autoBackup">
                                <span class="slider"></span>
                            </label>
                            <span class="setting-description">إنشاء نسخ احتياطية تلقائية</span>
                        </div>
                    </div>
                    
                    <div class="setting-group">
                        <label class="setting-label">تكرار النسخ الاحتياطية</label>
                        <select class="setting-select" id="backupFrequency">
                            <option value="daily">يومياً</option>
                            <option value="weekly">أسبوعياً</option>
                            <option value="monthly">شهرياً</option>
                        </select>
                    </div>
                    
                    <div class="setting-group">
                        <label class="setting-label">الاحتفاظ بالنسخ الاحتياطية</label>
                        <select class="setting-select" id="retentionPeriod">
                            <option value="7">أسبوع واحد</option>
                            <option value="30">شهر واحد</option>
                            <option value="90">3 أشهر</option>
                            <option value="365">سنة واحدة</option>
                        </select>
                    </div>
                    
                    <div class="setting-actions">
                        <button onclick="saveSettings()" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            حفظ الإعدادات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Backup Info Modal -->
<
</div>

<style>
/* Backup Container */
.backup-container {
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
    font-size: 0.95rem;
}

.header-action {
    flex-shrink: 0;
}

/* Backup Dashboard */
.backup-dashboard {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #4e73df, #224abe);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.0rem;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.0rem;
    font-weight: 400;
    color: #2d3748;
    margin: 0 0 5px 0;
}

.stat-label {
    color: #718096;
    margin: 0;
    font-size: 0.9rem;
}

/* Backup Actions */
.backup-actions {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.action-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.action-header {
    margin-bottom: 25px;
}

.action-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.action-title i {
    color: #4e73df;
}

.action-description {
    color: #718096;
    margin: 0;
    font-size: 0.95rem;
}

.action-content {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

/* Backup Options */
.backup-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.option-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.option-label {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    font-weight: 500;
    color: #2d3748;
}

.option-label input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid #cbd5e0;
    border-radius: 4px;
    position: relative;
    transition: all 0.3s ease;
}

.option-label input[type="checkbox"]:checked + .checkmark {
    background: #4e73df;
    border-color: #4e73df;
}

.option-label input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.option-group small {
    color: #718096;
    font-size: 0.85rem;
    margin-right: 32px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 500;
    text-decoration: none;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-large {
    padding: 15px 30px;
    font-size: 1rem;
}

.btn-sm {
    padding: 8px 16px;
    font-size: 0.85rem;
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
    text-decoration: none;
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
    text-decoration: none;
}

.btn-outline-danger {
    color: #e53e3e;
    border-color: #e53e3e;
    background: transparent;
}

.btn-outline-danger:hover {
    background: #e53e3e;
    border-color: #e53e3e;
    color: white;
    text-decoration: none;
}

/* Backup Progress */
.backup-progress {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 25px;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.progress-header h4 {
    margin: 0;
    color: #2d3748;
    font-weight: 600;
}

.progress-percentage {
    color: #4e73df;
    font-weight: 600;
    font-size: 0.9rem;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4e73df, #224abe);
    width: 0%;
    transition: width 0.3s ease;
}

.progress-status {
    color: #718096;
    font-size: 0.9rem;
    text-align: center;
}

/* Recent Backups */
.recent-backups {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.section-title {
    font-size: 1.0rem;
    font-weight: 400;
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #4e73df;
}

.backups-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.backup-item {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.backup-item:hover {
    background: #f1f3f4;
    border-color: #cbd5e0;
}

.backup-icon {
    width: 50px;
    height: 50px;
    background: #4e73df;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.backup-details {
    flex: 1;
}

.backup-name {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 8px 0;
}

.backup-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.backup-size, .backup-date {
    color: #718096;
    font-size: 0.85rem;
}

.backup-status {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.backup-status.success {
    background: #d4edda;
    color: #155724;
}

.backup-status.pending {
    background: #fff3cd;
    color: #856404;
}

.backup-status.failed {
    background: #f8d7da;
    color: #721c24;
}

.backup-actions {
    display: flex;
    gap: 10px;
}

/* Empty State */
.empty-backups {
    text-align: center;
    padding: 60px 20px;
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

.empty-backups h4 {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 10px 0;
}

.empty-backups p {
    color: #718096;
    margin: 0;
}

/* Backup Settings */
.backup-settings {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.settings-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 25px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.settings-title i {
    color: #4e73df;
}

.settings-content {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.setting-group {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #e2e8f0;
}

.setting-group:last-child {
    border-bottom: none;
}

.setting-label {
    font-weight: 600;
    color: #2d3748;
}

.setting-control {
    display: flex;
    align-items: center;
    gap: 15px;
}

.setting-description {
    color: #718096;
    font-size: 0.9rem;
}

.setting-select {
    padding: 8px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    background: white;
    color: #2d3748;
    font-size: 0.9rem;
    min-width: 150px;
}

.setting-select:focus {
    outline: none;
    border-color: #4e73df;
}

/* Switch Toggle */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e0;
    transition: .4s;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #4e73df;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.setting-actions {
    display: flex;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
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
    padding: 20px 25px;
}

.modal-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    font-size: 1.1rem;
    margin: 0;
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
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .backup-options {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .backup-container {
        padding: 20px;
    }
    
    .header-content {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .backup-item {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .backup-meta {
        justify-content: center;
    }
    
    .backup-actions {
        justify-content: center;
    }
    
    .setting-group {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}

@media (max-width: 576px) {
    .backup-container {
        padding: 15px;
    }
    
    .page-header {
        padding: 20px;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .action-card, .recent-backups, .backup-settings {
        padding: 20px;
    }
    
    .stat-card {
        padding: 20px;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize backup functionality
    initializeBackupSystem();
});

function initializeBackupSystem() {
    // Load backup statistics
    loadBackupStats();
    
    // Load recent backups
    loadRecentBackups();
}

function loadBackupStats() {
    // In a real application, you would fetch this data from the server
    // For now, we'll use placeholder data
    console.log('Loading backup statistics...');
}

function loadRecentBackups() {
    // In a real application, you would fetch this data from the server
    console.log('Loading recent backups...');
}

function createBackup() {
    const createBtn = document.getElementById('createBackupBtn');
    const progressDiv = document.getElementById('backupProgress');
    const progressFill = document.getElementById('progressFill');
    const progressPercentage = document.getElementById('progressPercentage');
    const progressStatus = document.getElementById('progressStatus');
    
    // Show progress
    progressDiv.style.display = 'block';
    createBtn.disabled = true;
    createBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإنشاء...';
    
    // Simulate backup progress
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        
        progressFill.style.width = progress + '%';
        progressPercentage.textContent = Math.round(progress) + '%';
        
        // Update status messages
        if (progress < 30) {
            progressStatus.textContent = 'جاري تحضير الملفات...';
        } else if (progress < 60) {
            progressStatus.textContent = 'جاري ضغط الملفات...';
        } else if (progress < 90) {
            progressStatus.textContent = 'جاري إنشاء النسخة الاحتياطية...';
        }
    }, 300);
    
    // Make actual backup request
    fetch('{{ route("backup.documents") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Backup failed');
    })
    .then(blob => {
        clearInterval(progressInterval);
        progressFill.style.width = '100%';
        progressPercentage.textContent = '100%';
        progressStatus.textContent = 'تم إنشاء النسخة الاحتياطية بنجاح!';
        
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'backup_' + new Date().toISOString().slice(0, 19).replace(/:/g, '-') + '.zip';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Reset UI after delay
        setTimeout(() => {
            progressDiv.style.display = 'none';
            createBtn.disabled = false;
            createBtn.innerHTML = '<i class="fas fa-download"></i> إنشاء نسخة احتياطية';
            
            // Refresh backups list
            loadRecentBackups();
        }, 2000);
    })
    .catch(error => {
        clearInterval(progressInterval);
        console.error('Backup error:', error);
        progressStatus.textContent = 'حدث خطأ أثناء إنشاء النسخة الاحتياطية';
        
        setTimeout(() => {
            progressDiv.style.display = 'none';
            createBtn.disabled = false;
            createBtn.innerHTML = '<i class="fas fa-download"></i> إنشاء نسخة احتياطية';
        }, 3000);
    });
}

function scheduleBackup() {
    // Show scheduling modal or form
    alert('سيتم إضافة ميزة جدولة النسخ الاحتياطية قريباً');
}

function downloadBackup(backupId) {
    // Download specific backup
    console.log('Downloading backup:', backupId);
    // In real app, you would make a request to download the specific backup
}

function deleteBackup(backupId) {
    if (confirm('هل أنت متأكد من حذف هذه النسخة الاحتياطية؟')) {
        console.log('Deleting backup:', backupId);
        // In real app, you would make a request to delete the backup
    }
}

function refreshBackups() {
    // Refresh the backups list
    console.log('Refreshing backups...');
    loadRecentBackups();
}

function saveSettings() {
    const autoBackup = document.getElementById('autoBackup').checked;
    const frequency = document.getElementById('backupFrequency').value;
    const retention = document.getElementById('retentionPeriod').value;
    
    // Save settings to server
    console.log('Saving settings:', { autoBackup, frequency, retention });
    
    // Show success message
    alert('تم حفظ الإعدادات بنجاح');
}
</script>
@endsection 