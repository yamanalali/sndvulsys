# Task History and Timeline System

## نظرة عامة

تم تطوير نظام شامل لتتبع تاريخ المهام والجدول الزمني يتيح للمتطوعين مراقبة جميع التغييرات والأنشطة المتعلقة بالمهام بشكل تفصيلي. يوفر النظام رؤية شاملة لجميع الإجراءات والتحديثات مع إمكانية الأرشفة والاستعادة.

## الميزات الرئيسية

### 1. تتبع تاريخ المهام (Task History Tracking)
- **تسجيل شامل**: يتم تسجيل جميع التغييرات في المهام تلقائياً
- **أنواع الإجراءات**: إنشاء، تحديث، تغيير الحالة، تغيير الأولوية، التعيين، الإنجاز
- **تفاصيل التغييرات**: القيم القديمة والجديدة لكل حقل تم تعديله
- **المستخدم المسؤول**: تتبع المستخدم الذي قام بالتغيير
- **الطوابع الزمنية**: تسجيل دقيق للوقت والتاريخ

### 2. الجدول الزمني التفاعلي (Interactive Timeline)
- **عرض بصري**: جدول زمني تفاعلي مع أيقونات ملونة
- **تصفية متقدمة**: تصفية حسب نوع الإجراء والتاريخ
- **عروض متعددة**: جدول زمني، قائمة، عرض مضغوط
- **تفاصيل شاملة**: عرض كامل للتغييرات والبيانات الوصفية

### 3. أرشيف المهام (Task Archive)
- **أرشفة آمنة**: حفظ المهام المكتملة في الأرشيف
- **استعادة سهلة**: إمكانية استعادة المهام من الأرشيف
- **بحث متقدم**: تصفية حسب الأولوية والمشروع والكلمات المفتاحية
- **إدارة فعالة**: تنظيم المهام القديمة مع الحفاظ على التاريخ

### 4. تصدير البيانات (Data Export)
- **صيغ متعددة**: JSON و CSV
- **تصفية مرنة**: تصدير تاريخ مهمة محددة أو جميع الأنشطة
- **دعم اللغة العربية**: ترميز UTF-8 للعرض الصحيح

## البنية التقنية

### النماذج (Models)

#### TaskHistory Model
```php
class TaskHistory extends Model
{
    // أنواع الإجراءات
    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_STATUS_CHANGED = 'status_changed';
    const ACTION_PRIORITY_CHANGED = 'priority_changed';
    const ACTION_ASSIGNED = 'assigned';
    const ACTION_COMPLETED = 'completed';
    const ACTION_PROGRESS_UPDATED = 'progress_updated';
    const ACTION_ARCHIVED = 'archived';
    const ACTION_RESTORED = 'restored';
    
    // العلاقات
    public function task(): BelongsTo
    public function user(): BelongsTo
    
    // Accessors للعرض
    public function getActionDescriptionAttribute(): string
    public function getFormattedOldValueAttribute(): string
    public function getFormattedNewValueAttribute(): string
    public function getTimeAgoAttribute(): string
    public function getActionIconAttribute(): string
    public function getActionColorAttribute(): string
}
```

#### Task Model Extensions
```php
// إضافة العلاقات والطرق الجديدة
public function history(): HasMany
public function recentHistory(): HasMany
public function recordHistory(): void
public function isArchived(): bool
public function archive(): void
public function restore(): void
```

### الخدمات (Services)

#### TaskHistoryService
```php
class TaskHistoryService
{
    // تسجيل الإجراءات
    public function recordTaskCreated(Task $task): void
    public function recordStatusChange(Task $task, string $oldStatus, string $newStatus): void
    public function recordProgressUpdate(Task $task, int $oldProgress, int $newProgress): void
    public function recordTaskCompleted(Task $task): void
    
    // استخراج البيانات
    public function getTaskTimeline(Task $task): array
    public function getUserTaskHistory(User $user, int $days = 30): array
    
    // تصدير البيانات
    public function exportToCsv($data)
}
```

### المتحكمات (Controllers)

#### TaskHistoryController
```php
class TaskHistoryController extends Controller
{
    // عرض التاريخ
    public function index(Request $request)
    public function timeline(Task $task)
    public function archive(Request $request)
    
    // إدارة الأرشيف
    public function restore(Task $task)
    public function archiveTask(Task $task)
    
    // API endpoints
    public function getTaskHistory(Task $task)
    public function activitySummary()
    public function export(Request $request)
}
```

## قاعدة البيانات

### جدول task_history
```sql
CREATE TABLE task_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    action_type VARCHAR(255) NOT NULL,
    field_name VARCHAR(255) NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    description TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_task_created (task_id, created_at),
    INDEX idx_action_created (action_type, created_at)
);
```

## الواجهات (Views)

### 1. صفحة تاريخ المهام (task-history.blade.php)
- **عرض الجدول الزمني**: عرض بصري للأنشطة مع أيقونات ملونة
- **تصفية متقدمة**: تصفية حسب نوع الإجراء والبحث
- **تصدير البيانات**: أزرار لتصدير JSON و CSV
- **تصفح الصفحات**: دعم التصفح للمجموعات الكبيرة

### 2. الجدول الزمني للمهمة (task-timeline.blade.php)
- **معلومات المهمة**: عرض تفاصيل المهمة في الأعلى
- **الجدول الزمني التفاعلي**: عرض جميع التغييرات مرتبة زمنياً
- **تفاصيل التغييرات**: عرض القيم القديمة والجديدة
- **تصفية حسب النوع**: تصفية الإجراءات حسب النوع

### 3. أرشيف المهام (task-archive.blade.php)
- **قائمة المهام المؤرشفة**: عرض منظم للمهام في الأرشيف
- **بحث متقدم**: تصفية حسب الأولوية والمشروع
- **إجراءات سريعة**: عرض التفاصيل، الاستعادة، التاريخ
- **معلومات الأرشفة**: تاريخ ووقت الأرشفة

## التكامل مع النظام الحالي

### تحديث TaskController
```php
// تسجيل إنشاء المهمة
app(\App\Services\TaskHistoryService::class)->recordTaskCreated($task);

// تسجيل تغيير الحالة
app(\App\Services\TaskHistoryService::class)->recordStatusChange($task, $oldStatus, $newStatus);

// تسجيل تحديث التقدم
app(\App\Services\TaskHistoryService::class)->recordProgressUpdate($task, $oldProgress, $newProgress);

// تسجيل إنجاز المهمة
app(\App\Services\TaskHistoryService::class)->recordTaskCompleted($task);
```

### تحديث VolunteerDashboardController
```php
// إضافة عدد الأنشطة الحديثة
$recentActivities = \App\Models\TaskHistory::where('user_id', auth()->id())
    ->where('created_at', '>=', now()->subDays(7))
    ->count();
```

## المسارات (Routes)

```php
// Task History and Timeline routes
Route::prefix('task-history')->name('task-history.')->middleware('auth')->group(function () {
    Route::get('/', [TaskHistoryController::class, 'index'])->name('index');
    Route::get('/timeline/{task}', [TaskHistoryController::class, 'timeline'])->name('timeline');
    Route::get('/archive', [TaskHistoryController::class, 'archive'])->name('archive');
    Route::post('/restore/{task}', [TaskHistoryController::class, 'restore'])->name('restore');
    Route::post('/archive/{task}', [TaskHistoryController::class, 'archiveTask'])->name('archive-task');
    Route::get('/history/{task}', [TaskHistoryController::class, 'getTaskHistory'])->name('get-history');
    Route::get('/activity-summary', [TaskHistoryController::class, 'activitySummary'])->name('activity-summary');
    Route::get('/export', [TaskHistoryController::class, 'export'])->name('export');
});
```

## الاستخدام

### 1. عرض تاريخ المهام
```php
// الوصول لصفحة التاريخ الرئيسية
Route::get('/task-history', [TaskHistoryController::class, 'index']);

// عرض الجدول الزمني لمهمة محددة
Route::get('/task-history/timeline/{task}', [TaskHistoryController::class, 'timeline']);
```

### 2. إدارة الأرشيف
```php
// عرض الأرشيف
Route::get('/task-history/archive', [TaskHistoryController::class, 'archive']);

// أرشفة مهمة
$task->archive();

// استعادة مهمة من الأرشيف
$task->restore();
```

### 3. تصدير البيانات
```php
// تصدير JSON
Route::get('/task-history/export?format=json');

// تصدير CSV
Route::get('/task-history/export?format=csv');

// تصدير تاريخ مهمة محددة
Route::get('/task-history/export?task_id=123&format=csv');
```

## الأمان والصلاحيات

### التحقق من الصلاحيات
```php
private function userHasAccessToTask(Task $task): bool
{
    $user = Auth::user();
    
    // التحقق من تعيين المهمة للمستخدم
    $isAssigned = $task->assignments()->where('user_id', $user->id)->exists();
    
    // التحقق من إنشاء المهمة بواسطة المستخدم
    $isCreator = $task->created_by === $user->id;
    
    // التحقق من صلاحيات المدير
    $isAdmin = $user->hasRole('admin') || $user->is_admin;
    
    return $isAssigned || $isCreator || $isAdmin;
}
```

## الأداء والتحسين

### الفهرسة
- فهرس على `task_id` و `created_at` للاستعلامات السريعة
- فهرس على `action_type` و `created_at` للتصفية
- فهرس على `user_id` لتتبع أنشطة المستخدم

### التخزين المؤقت
- تخزين مؤقت للاستعلامات المتكررة
- تحسين استعلامات العلاقات مع `with()`
- استخدام `paginate()` للمجموعات الكبيرة

## الصيانة والتطوير

### التنظيف الدوري
```php
// حذف السجلات القديمة (أكثر من سنة)
TaskHistory::where('created_at', '<', now()->subYear())->delete();
```

### النسخ الاحتياطي
- نسخ احتياطي منتظم لجدول `task_history`
- تصدير البيانات المهمة قبل التنظيف

### المراقبة
- مراقبة حجم جدول `task_history`
- تنبيهات عند تجاوز الحدود المحددة
- تقارير دورية عن النشاط

## الاستنتاج

يوفر نظام تتبع تاريخ المهام والجدول الزمني حلاً شاملاً لمراقبة وإدارة جميع التغييرات في المهام. مع الميزات المتقدمة مثل الأرشفة والتصدير، يصبح من السهل تتبع التقدم والحفاظ على سجل دقيق لجميع الأنشطة.

النظام مصمم ليكون:
- **سهل الاستخدام**: واجهات بديهية وواضحة
- **قابل للتوسع**: بنية مرنة تدعم النمو المستقبلي
- **آمن**: تحقق من الصلاحيات وحماية البيانات
- **فعال**: تحسينات الأداء والفهرسة المناسبة 