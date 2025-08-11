/**
 * Volunteer Dashboard JavaScript
 * ملف JavaScript مخصص للوحة تحكم المتطوع
 */

class VolunteerDashboard {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeCharts();
        this.setupCalendar();
        this.setupPriorityVisualization();
        this.setupReminderSystem();
        this.setupCompletionInterface();
        this.setupNotifications();
    }

    /**
     * إعداد مستمعي الأحداث
     */
    setupEventListeners() {
        // مستمعي أحداث للبطاقات
        document.querySelectorAll('.volunteer-card').forEach(card => {
            card.addEventListener('mouseenter', this.handleCardHover.bind(this));
            card.addEventListener('mouseleave', this.handleCardLeave.bind(this));
        });

        // مستمعي أحداث للبحث والفلترة
        const searchInput = document.getElementById('searchTasks');
        if (searchInput) {
            searchInput.addEventListener('input', this.handleSearch.bind(this));
        }

        // مستمعي أحداث لتحديث حالة المهام
        document.querySelectorAll('.update-task-status').forEach(button => {
            button.addEventListener('click', this.handleTaskStatusUpdate.bind(this));
        });

        // مستمعي أحداث للتقويم
        document.querySelectorAll('.calendar-day').forEach(day => {
            day.addEventListener('click', this.handleCalendarDayClick.bind(this));
        });

        document.addEventListener('DOMContentLoaded', () => {
            this.setupSearch();
            this.setupTaskStatusUpdates();
            this.setupPriorityFilters();
            this.setupReminderAlerts();
        });
    }

    /**
     * تهيئة الرسوم البيانية
     */
    initializeCharts() {
        // التحقق من وجود عناصر الرسوم البيانية
        const monthlyChart = document.getElementById('monthlyChart');
        const distributionChart = document.getElementById('taskDistributionChart');

        if (monthlyChart && typeof Chart !== 'undefined') {
            this.createMonthlyChart();
        }

        if (distributionChart && typeof Chart !== 'undefined') {
            this.createDistributionChart();
        }
    }

    /**
     * إنشاء الرسم البياني الشهري
     */
    createMonthlyChart() {
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        
        // بيانات تجريبية - يمكن استبدالها ببيانات حقيقية
        const data = {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'المهام المكتملة',
                data: [12, 19, 15, 25, 22, 30],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        };

        new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    /**
     * إنشاء الرسم البياني الدائري
     */
    createDistributionChart() {
        const ctx = document.getElementById('taskDistributionChart').getContext('2d');
        
        const data = {
            labels: ['مكتملة', 'قيد التنفيذ', 'معلقة', 'متأخرة'],
            datasets: [{
                data: [30, 15, 10, 5],
                backgroundColor: [
                    '#28a745',
                    '#17a2b8',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    /**
     * إعداد التقويم
     */
    setupCalendar() {
        // إضافة تأثيرات للتقويم
        document.querySelectorAll('.calendar-day').forEach(day => {
            day.addEventListener('mouseenter', () => {
                day.style.transform = 'scale(1.05)';
            });

            day.addEventListener('mouseleave', () => {
                day.style.transform = 'scale(1)';
            });
        });
    }

    /**
     * إعداد الإشعارات
     */
    setupNotifications() {
        // التحقق من وجود مهام متأخرة
        this.checkOverdueTasks();
        
        // إعداد إشعارات فورية
        this.setupRealTimeNotifications();

        // Create notification container if it doesn't exist
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
            document.body.appendChild(container);
        }
    }

    /**
     * التحقق من المهام المتأخرة
     */
    checkOverdueTasks() {
        const overdueTasks = document.querySelectorAll('.overdue-task');
        if (overdueTasks.length > 0) {
            this.showNotification(`لديك ${overdueTasks.length} مهام متأخرة`, 'warning');
        }
    }

    /**
     * إعداد الإشعارات الفورية
     */
    setupRealTimeNotifications() {
        // يمكن إضافة WebSocket هنا للإشعارات الفورية
        setInterval(() => {
            this.checkNewTasks();
        }, 30000); // فحص كل 30 ثانية
    }

    /**
     * فحص المهام الجديدة
     */
    checkNewTasks() {
        // يمكن إضافة API call هنا للتحقق من المهام الجديدة
        console.log('Checking for new tasks...');
    }

    /**
     * معالجة تأثير البطاقات
     */
    handleCardHover(event) {
        const card = event.currentTarget;
        card.style.transform = 'translateY(-5px)';
        card.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
    }

    handleCardLeave(event) {
        const card = event.currentTarget;
        card.style.transform = 'translateY(0)';
        card.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
    }

    /**
     * معالجة البحث
     */
    handleSearch(event) {
        const searchTerm = event.target.value.toLowerCase();
        const taskRows = document.querySelectorAll('.task-row');

        taskRows.forEach(row => {
            const title = row.querySelector('.task-title').textContent.toLowerCase();
            if (title.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    /**
     * معالجة تحديث حالة المهمة
     */
    handleTaskStatusUpdate(event) {
        const button = event.currentTarget;
        const taskId = button.dataset.taskId;
        const newStatus = button.dataset.status;

        if (confirm('هل أنت متأكد من تحديث حالة المهمة؟')) {
            this.updateTaskStatus(taskId, newStatus);
        }
    }

    /**
     * تحديث حالة المهمة عبر API
     */
    async updateTaskStatus(taskId, status) {
        try {
            const response = await fetch(`/tasks/${taskId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('تم تحديث حالة المهمة بنجاح', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                this.showNotification('حدث خطأ أثناء تحديث حالة المهمة', 'error');
            }
        } catch (error) {
            console.error('Error updating task status:', error);
            this.showNotification('حدث خطأ في الاتصال', 'error');
        }
    }

    /**
     * معالجة النقر على يوم في التقويم
     */
    handleCalendarDayClick(event) {
        const day = event.currentTarget;
        const date = day.dataset.date;
        
        if (date) {
            this.showDayDetails(date);
        }
    }

    /**
     * عرض تفاصيل اليوم
     */
    showDayDetails(date) {
        // يمكن إضافة modal لعرض تفاصيل المهام في هذا اليوم
        console.log(`Showing details for ${date}`);
    }

    /**
     * عرض الإشعارات
     */
    showNotification(message, type = 'info') {
        // استخدام مكتبة الإشعارات المتوفرة أو إنشاء إشعار مخصص
        if (typeof PNotify !== 'undefined') {
            new PNotify({
                title: 'إشعار',
                text: message,
                type: type,
                delay: 3000
            });
        } else {
            // إشعار مخصص
            this.createCustomNotification(message, type);
        }
    }

    /**
     * إنشاء إشعار مخصص
     */
    createCustomNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification-toast`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideInRight 0.3s ease-out;
        `;
        notification.innerHTML = `
            <button type="button" class="close" onclick="this.parentElement.remove()">
                <span>&times;</span>
            </button>
            ${message}
        `;

        document.body.appendChild(notification);

        // إزالة الإشعار تلقائياً بعد 3 ثوان
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 3000);
    }

    /**
     * تصدير الإحصائيات
     */
    exportStatistics() {
        // يمكن إضافة وظيفة تصدير الإحصائيات كـ PDF أو Excel
        console.log('Exporting statistics...');
    }

    /**
     * تحديث البيانات
     */
    refreshData() {
        location.reload();
    }

    /**
     * تبديل الوضع المظلم
     */
    toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
    }

    setupPriorityVisualization() {
        // Add priority indicators to task cards
        const taskCards = document.querySelectorAll('.task-card');
        taskCards.forEach(card => {
            this.addPriorityIndicator(card);
        });

        // Setup priority filter buttons
        const priorityFilters = document.querySelectorAll('.priority-filter');
        priorityFilters.forEach(filter => {
            filter.addEventListener('click', (e) => {
                e.preventDefault();
                this.filterByPriority(filter.dataset.priority);
            });
        });
    }

    addPriorityIndicator(card) {
        const priorityElement = card.querySelector('.task-priority');
        if (!priorityElement) return;

        const priority = priorityElement.dataset.priority;
        const priorityData = this.getPriorityData(priority);
        
        // Create priority badge with icon
        const badge = document.createElement('span');
        badge.className = `priority-badge ${priority}`;
        badge.innerHTML = `
            <i class="${priorityData.icon} priority-icon"></i>
            <span>${priorityData.label}</span>
        `;
        badge.style.backgroundColor = priorityData.color;
        
        // Replace existing priority display
        priorityElement.innerHTML = '';
        priorityElement.appendChild(badge);
    }

    getPriorityData(priority) {
        const data = {
            urgent: {
                icon: 'feather-alert-triangle',
                label: 'عاجلة',
                color: '#dc3545'
            },
            high: {
                icon: 'feather-flag',
                label: 'عالية',
                color: '#fd7e14'
            },
            medium: {
                icon: 'feather-clock',
                label: 'متوسطة',
                color: '#17a2b8'
            },
            low: {
                icon: 'feather-check-circle',
                label: 'منخفضة',
                color: '#28a745'
            }
        };
        
        return data[priority] || data.medium;
    }

    setupReminderSystem() {
        // Add reminder indicators to tasks
        const tasks = document.querySelectorAll('.task-item');
        tasks.forEach(task => {
            this.addReminderIndicator(task);
        });

        // Setup reminder alerts
        this.checkReminders();
        setInterval(() => this.checkReminders(), 60000); // Check every minute
    }

    addReminderIndicator(task) {
        const deadlineElement = task.querySelector('.task-deadline');
        if (!deadlineElement) return;

        const deadline = new Date(deadlineElement.dataset.deadline);
        const reminderLevel = this.getReminderLevel(deadline);
        
        if (reminderLevel !== 'none') {
            const indicator = document.createElement('div');
            indicator.className = `reminder-indicator`;
            indicator.innerHTML = `
                <div class="reminder-badge ${reminderLevel}">
                    <i class="${this.getReminderIcon(reminderLevel)}"></i>
                </div>
            `;
            
            deadlineElement.appendChild(indicator);
        }
    }

    getReminderLevel(deadline) {
        const now = new Date();
        const diffTime = deadline - now;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays < 0) return 'overdue';
        if (diffDays <= 1) return 'critical';
        if (diffDays <= 3) return 'urgent';
        if (diffDays <= 7) return 'warning';
        return 'normal';
    }

    getReminderIcon(level) {
        const icons = {
            overdue: 'feather-alert-octagon',
            critical: 'feather-alert-triangle',
            urgent: 'feather-clock',
            warning: 'feather-bell',
            normal: 'feather-check'
        };
        
        return icons[level] || 'feather-circle';
    }

    checkReminders() {
        const tasks = document.querySelectorAll('.task-item');
        const urgentTasks = [];

        tasks.forEach(task => {
            const deadlineElement = task.querySelector('.task-deadline');
            if (!deadlineElement) return;

            const deadline = new Date(deadlineElement.dataset.deadline);
            const reminderLevel = this.getReminderLevel(deadline);
            
            if (['overdue', 'critical', 'urgent'].includes(reminderLevel)) {
                urgentTasks.push({
                    task: task,
                    level: reminderLevel,
                    deadline: deadline
                });
            }
        });

        if (urgentTasks.length > 0) {
            this.showReminderAlert(urgentTasks);
        }
    }

    showReminderAlert(urgentTasks) {
        const alertContainer = document.getElementById('reminder-alerts');
        if (!alertContainer) return;

        alertContainer.innerHTML = '';
        
        urgentTasks.forEach(({ task, level, deadline }) => {
            const taskTitle = task.querySelector('.task-title')?.textContent || 'مهمة غير معروفة';
            const alert = document.createElement('div');
            alert.className = `alert alert-${level === 'overdue' ? 'danger' : level === 'critical' ? 'danger' : 'warning'} alert-dismissible fade show`;
            alert.innerHTML = `
                <i class="${this.getReminderIcon(level)} me-2"></i>
                <strong>تذكير:</strong> المهمة "${taskTitle}" ${level === 'overdue' ? 'متأخرة' : 'تستحق قريباً'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            alertContainer.appendChild(alert);
        });
    }

    setupCompletionInterface() {
        // Add completion interface to task cards
        const taskCards = document.querySelectorAll('.task-card');
        taskCards.forEach(card => {
            this.addCompletionInterface(card);
        });

        // Setup completion buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('completion-button')) {
                e.preventDefault();
                this.handleTaskCompletion(e.target);
            }
        });
    }

    addCompletionInterface(card) {
        const taskId = card.dataset.taskId;
        const progress = parseInt(card.dataset.progress) || 0;
        const status = card.dataset.status;
        
        if (status === 'completed') return;

        const completionDiv = document.createElement('div');
        completionDiv.className = 'completion-interface';
        completionDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">تقدم المهمة</h6>
                <span class="completion-status ${this.getCompletionStatusClass(progress)}">
                    ${this.getCompletionStatusText(progress)}
                </span>
            </div>
            <div class="completion-progress">
                <div class="completion-progress-bar" style="width: ${progress}%; background-color: ${this.getCompletionColor(progress)}"></div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted">${progress}% مكتمل</span>
                <button class="completion-button" data-task-id="${taskId}" ${progress < 100 ? '' : ''}>
                    <i class="feather-check-circle me-2"></i>
                    ${progress < 100 ? 'تحديث التقدم' : 'إكمال المهمة'}
                </button>
            </div>
        `;
        
        card.appendChild(completionDiv);
    }

    getCompletionStatusClass(progress) {
        if (progress >= 100) return 'completed';
        if (progress >= 90) return 'near_completion';
        if (progress >= 50) return 'half_completed';
        if (progress > 0) return 'started';
        return 'not_started';
    }

    getCompletionStatusText(progress) {
        if (progress >= 100) return 'مكتملة';
        if (progress >= 90) return 'قريبة من الإنجاز';
        if (progress >= 50) return 'نصف مكتملة';
        if (progress > 0) return 'مبدوءة';
        return 'لم تبدأ';
    }

    getCompletionColor(progress) {
        if (progress >= 100) return '#28a745';
        if (progress >= 90) return '#20c997';
        if (progress >= 50) return '#17a2b8';
        if (progress > 0) return '#ffc107';
        return '#6c757d';
    }

    async handleTaskCompletion(button) {
        const taskId = button.dataset.taskId;
        const card = button.closest('.task-card');
        const progressBar = card.querySelector('.completion-progress-bar');
        const progressText = card.querySelector('.text-muted');
        
        try {
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="feather-loader me-2"></i>جاري التحديث...';
            
            // Update progress to 100%
            const newProgress = 100;
            
            // Update UI immediately
            progressBar.style.width = `${newProgress}%`;
            progressBar.style.backgroundColor = this.getCompletionColor(newProgress);
            progressText.textContent = `${newProgress}% مكتمل`;
            
            // Update completion status
            const statusElement = card.querySelector('.completion-status');
            statusElement.className = `completion-status completed`;
            statusElement.textContent = 'مكتملة';
            
            // Update button
            button.innerHTML = '<i class="feather-check-circle me-2"></i>تم الإنجاز';
            button.style.backgroundColor = '#28a745';
            
            // Send completion notification
            this.showNotification('تم إنجاز المهمة بنجاح!', 'success');
            
            // Update task status via AJAX
            await this.updateTaskStatus(taskId, 'completed', newProgress);
            
        } catch (error) {
            console.error('Error completing task:', error);
            this.showNotification('حدث خطأ أثناء إنجاز المهمة', 'error');
            
            // Revert UI changes
            button.disabled = false;
            button.innerHTML = '<i class="feather-check-circle me-2"></i>إكمال المهمة';
        }
    }

    async updateTaskStatus(taskId, status, progress) {
        const response = await fetch(`/tasks/${taskId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                status: status,
                progress: progress
            })
        });

        if (!response.ok) {
            throw new Error('Failed to update task status');
        }

        return response.json();
    }

    showNotification(message, type = 'info', duration = 5000) {
        const container = document.getElementById('notification-container');
        const notification = document.createElement('div');
        notification.className = `notification-toast ${type}`;
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="${this.getNotificationIcon(type)} me-3"></i>
                <div>
                    <strong>${this.getNotificationTitle(type)}</strong>
                    <div class="text-muted">${message}</div>
                </div>
                <button type="button" class="btn-close ms-3" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        container.appendChild(notification);
        
        // Show notification
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Auto remove after duration
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'feather-check-circle',
            error: 'feather-alert-circle',
            warning: 'feather-alert-triangle',
            info: 'feather-info'
        };
        
        return icons[type] || icons.info;
    }

    getNotificationTitle(type) {
        const titles = {
            success: 'نجح',
            error: 'خطأ',
            warning: 'تحذير',
            info: 'معلومات'
        };
        
        return titles[type] || titles.info;
    }

    filterByPriority(priority) {
        const taskContainers = document.querySelectorAll('.task-card-container');
        let visibleCount = 0;
        
        taskContainers.forEach(container => {
            const cardPriority = container.dataset.priority;
            if (priority === 'all' || cardPriority === priority) {
                container.classList.remove('hidden');
                container.style.display = 'block';
                visibleCount++;
            } else {
                container.classList.add('hidden');
                container.style.display = 'none';
            }
        });

        // Update active filter button
        document.querySelectorAll('.priority-filter').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeButton = document.querySelector(`[data-priority="${priority}"]`);
        if (activeButton) {
            activeButton.classList.add('active');
        }

        // Update count in "all" button
        const allButton = document.querySelector('[data-priority="all"]');
        if (allButton && priority === 'all') {
            allButton.innerHTML = `<i class="feather icon-list"></i> جميع المهام (${visibleCount})`;
        }

        // Show notification for filter results
        if (priority !== 'all') {
            this.showNotification(`تم عرض ${visibleCount} مهمة ذات أولوية ${this.getPriorityLabel(priority)}`, 'info', 2000);
        }
    }

    getPriorityLabel(priority) {
        const labels = {
            urgent: 'عاجلة',
            high: 'عالية',
            medium: 'متوسطة',
            low: 'منخفضة'
        };
        return labels[priority] || priority;
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new VolunteerDashboard();
});

// وظائف مساعدة عامة
window.VolunteerDashboardHelpers = {
    /**
     * تنسيق التاريخ
     */
    formatDate(date) {
        return new Date(date).toLocaleDateString('ar-SA');
    },

    /**
     * تنسيق الوقت
     */
    formatTime(date) {
        return new Date(date).toLocaleTimeString('ar-SA');
    },

    /**
     * حساب الفرق في الأيام
     */
    daysDifference(date1, date2) {
        const diffTime = Math.abs(new Date(date2) - new Date(date1));
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    },

    /**
     * تحويل النسبة المئوية إلى درجة
     */
    percentageToDegrees(percentage) {
        return (percentage / 100) * 360;
    }
};

// CSS للرسوم المتحركة
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .notification-toast {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 8px;
    }

    .dark-mode {
        background-color: #1a1a1a;
        color: #ffffff;
    }

    .dark-mode .card {
        background-color: #2d2d2d;
        border-color: #404040;
    }
`;
document.head.appendChild(style); 