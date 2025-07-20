# نظام إدارة المهام المتكامل

## نظرة عامة

تم تطوير نظام إدارة المهام المتكامل باستخدام Laravel 11 مع التركيز على إدارة حالات المهام والأولويات والتبعيات. النظام يوفر بنية قوية وقابلة للتوسع لإدارة المهام بكفاءة.

## المميزات الرئيسية

### 📋 إدارة المهام
- إنشاء وتعديل وحذف المهام
- تصنيف المهام حسب الفئات
- تحديد الأولويات (عاجلة، عالية، متوسطة، منخفضة)
- تتبع التقدم والنسبة المئوية للإنجاز
- إدارة المواعيد النهائية والتواريخ

### 🔄 سير العمل
- حالات المهام: جديدة، قيد التنفيذ، معلقة، مكتملة، ملغاة
- قواعد انتقال الحالات
- التحقق من التبعيات قبل بدء المهام
- إشعارات تلقائية عند تغيير الحالات

### 🎯 نظام الأولويات
- حساب أولوية ديناميكية بناءً على عدة عوامل
- الأولوية الأساسية
- إلحاح الموعد النهائي
- تأثير التبعيات
- حالة التخصيصات
- مستوى التقدم

### 🔗 إدارة التبعيات
- أربعة أنواع من التبعيات:
  - انتهاء إلى بداية (Finish-to-Start)
  - بداية إلى بداية (Start-to-Start)
  - انتهاء إلى انتهاء (Finish-to-Finish)
  - بداية إلى انتهاء (Start-to-Finish)
- منع التبعيات الدائرية
- التحقق من إمكانية بدء المهام

### 👥 التخصيصات
- تخصيص المهام للمستخدمين
- تتبع حالة التخصيصات
- إدارة التقدم الفردي
- حساب الكفاءة والإنتاجية

### 📊 التقارير والإحصائيات
- إحصائيات شاملة للمهام
- توزيع الحالات والأولويات
- المهام المتأخرة والعاجلة
- تحليل الأداء

## البنية التقنية

### النماذج (Models)

#### Task Model
```php
// الحالات المتاحة
const STATUS_NEW = 'new';
const STATUS_IN_PROGRESS = 'in_progress';
const STATUS_PENDING = 'pending';
const STATUS_COMPLETED = 'completed';
const STATUS_CANCELLED = 'cancelled';

// الأولويات المتاحة
const PRIORITY_URGENT = 'urgent';
const PRIORITY_HIGH = 'high';
const PRIORITY_MEDIUM = 'medium';
const PRIORITY_LOW = 'low';
```

#### Assignment Model
```php
// حالات التخصيص
const STATUS_ASSIGNED = 'assigned';
const STATUS_IN_PROGRESS = 'in_progress';
const STATUS_SUBMITTED = 'submitted';
const STATUS_COMPLETED = 'completed';
const STATUS_OVERDUE = 'overdue';
const STATUS_CANCELLED = 'cancelled';
```

#### Category Model
- دعم الفئات الهرمية (أب وابن)
- إدارة الفئات النشطة وغير النشطة
- حساب إحصائيات المهام لكل فئة

#### TaskDependency Model
```php
// أنواع التبعيات
const TYPE_FINISH_TO_START = 'finish_to_start';
const TYPE_START_TO_START = 'start_to_start';
const TYPE_FINISH_TO_FINISH = 'finish_to_finish';
const TYPE_START_TO_FINISH = 'start_to_finish';
```

### الخدمات (Services)

#### TaskWorkflowService
- إدارة انتقالات حالات المهام
- التحقق من صحة الانتقالات
- معالجة التبعيات
- إشعارات التغييرات

#### TaskPriorityService
- حساب أولوية ديناميكية
- ضبط الأولويات تلقائياً
- تحليل المهام العاجلة والمتأخرة
- إحصائيات توزيع الأولويات

### البيانات التجريبية

#### Factories
- `TaskFactory`: إنشاء مهام تجريبية مع حالات مختلفة
- `CategoryFactory`: إنشاء فئات هرمية
- `AssignmentFactory`: إنشاء تخصيصات تجريبية

#### Seeders
- `TaskManagementSeeder`: إنشاء بيانات شاملة للنظام
- دعم اللغة العربية في البيانات التجريبية

### أوامر Artisan

#### TaskManagementCommand
```bash
# عرض المهام
php artisan tasks:manage list

# إحصائيات النظام
php artisan tasks:manage stats

# المهام المتأخرة
php artisan tasks:manage overdue

# المهام العاجلة
php artisan tasks:manage urgent

# التبعيات
php artisan tasks:manage dependencies

# ضبط الأولويات
php artisan tasks:manage priorities

# معلومات سير العمل
php artisan tasks:manage workflow
```

## التثبيت والإعداد

### المتطلبات
- PHP 8.2+
- Laravel 11
- MySQL/PostgreSQL/SQLite

### خطوات التثبيت

1. **تثبيت التبعيات**
```bash
composer install
```

2. **إعداد قاعدة البيانات**
```bash
cp .env.example .env
php artisan key:generate
```

3. **تكوين قاعدة البيانات في ملف .env**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=
```

4. **تشغيل الهجرات**
```bash
php artisan migrate
```

5. **إنشاء البيانات التجريبية**
```bash
php artisan db:seed
```

## الاستخدام

### إنشاء مهمة جديدة
```php
$task = Task::create([
    'title' => 'تطوير واجهة المستخدم',
    'description' => 'تصميم وتطوير واجهة مستخدم حديثة',
    'category_id' => $category->id,
    'priority' => Task::PRIORITY_HIGH,
    'status' => Task::STATUS_NEW,
    'deadline' => now()->addDays(7),
    'created_by' => auth()->id(),
    'assigned_to' => $user->id,
]);
```

### تغيير حالة المهمة
```php
$workflowService = new TaskWorkflowService();
$workflowService->transitionTaskStatus($task, Task::STATUS_IN_PROGRESS);
```

### حساب أولوية المهمة
```php
$priorityService = new TaskPriorityService();
$priorityScore = $priorityService->calculatePriorityScore($task);
```

### إضافة تبعية
```php
TaskDependency::create([
    'task_id' => $dependentTask->id,
    'depends_on_task_id' => $prerequisiteTask->id,
    'dependency_type' => TaskDependency::TYPE_FINISH_TO_START,
    'is_active' => true,
]);
```

## قاعدة البيانات

### الجداول الرئيسية

#### tasks
- `id`: المعرف الفريد
- `title`: عنوان المهمة
- `description`: وصف المهمة
- `status`: حالة المهمة
- `priority`: أولوية المهمة
- `category_id`: معرف الفئة
- `created_by`: منشئ المهمة
- `assigned_to`: المخصص له
- `start_date`: تاريخ البدء
- `deadline`: الموعد النهائي
- `completed_at`: تاريخ الإنجاز
- `progress`: نسبة التقدم
- `is_recurring`: متكررة أم لا

#### assignments
- `id`: المعرف الفريد
- `task_id`: معرف المهمة
- `user_id`: معرف المستخدم
- `assigned_at`: تاريخ التخصيص
- `due_at`: الموعد النهائي
- `completed_at`: تاريخ الإنجاز
- `status`: حالة التخصيص
- `progress`: نسبة التقدم

#### categories
- `id`: المعرف الفريد
- `name`: اسم الفئة
- `slug`: الرابط المختصر
- `description`: وصف الفئة
- `parent_id`: معرف الفئة الأب
- `is_active`: نشطة أم لا

#### task_dependencies
- `id`: المعرف الفريد
- `task_id`: معرف المهمة المعتمدة
- `depends_on_task_id`: معرف المهمة المطلوبة
- `dependency_type`: نوع التبعية
- `is_active`: نشطة أم لا

## الأمان والأداء

### الأمان
- التحقق من صحة البيانات
- منع التبعيات الدائرية
- التحقق من صلاحيات المستخدمين
- حماية من SQL Injection

### الأداء
- فهارس محسنة للبحث السريع
- علاقات محملة مسبقاً (Eager Loading)
- استعلامات محسنة
- تخزين مؤقت للبيانات المتكررة

## التوسع المستقبلي

### الميزات المقترحة
- نظام إشعارات متقدم
- لوحة تحكم تفاعلية
- تقارير متقدمة
- دعم الملفات المرفقة
- نظام التعليقات
- التكامل مع أنظمة خارجية
- تطبيق محمول

### التحسينات التقنية
- استخدام Redis للتخزين المؤقت
- تحسين استعلامات قاعدة البيانات
- إضافة اختبارات شاملة
- تحسين واجهة المستخدم
- دعم متعدد اللغات

## الدعم والمساهمة

### الإبلاغ عن الأخطاء
يرجى الإبلاغ عن أي أخطاء أو مشاكل من خلال:
- إنشاء Issue في GitHub
- وصف تفصيلي للمشكلة
- خطوات إعادة إنتاج المشكلة

### المساهمة
نرحب بالمساهمات من المجتمع:
- Fork المشروع
- إنشاء فرع للميزة الجديدة
- إضافة اختبارات
- تحديث التوثيق
- إنشاء Pull Request

## الترخيص

هذا المشروع مرخص تحت رخصة MIT. راجع ملف LICENSE للتفاصيل.

## الاتصال

للاستفسارات والدعم:
- البريد الإلكتروني: support@example.com
- الموقع الإلكتروني: https://example.com
- GitHub: https://github.com/example/task-management 