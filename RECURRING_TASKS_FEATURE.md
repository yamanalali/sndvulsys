# نظام المهام المتكررة (Recurring Tasks System)

## نظرة عامة
تم تطوير نظام شامل للمهام المتكررة يدعم الجدولة التلقائية للمهام بأنماط مختلفة (يومية، أسبوعية، شهرية، سنوية) مع إدارة الاستثناءات والتحكم المرن في التكرار.

## المكونات المضافة

### 1. قاعدة البيانات

#### جدول المهام المحسن (Enhanced Tasks Table)
- `recurrence_config` (JSON): إعدادات التكرار المتقدمة
- `recurrence_start_date` (Date): تاريخ بداية التكرار
- `recurrence_end_date` (Date): تاريخ انتهاء التكرار (اختياري)
- `recurrence_max_occurrences` (Integer): العدد الأقصى للتكرارات
- `recurrence_current_count` (Integer): عدد التكرارات المنشأة حالياً
- `parent_task_id` (BigInteger): معرف المهمة الأب للمهام المنشأة
- `is_recurring_instance` (Boolean): هل هذه مهمة منشأة من تكرار
- `next_occurrence_date` (DateTime): موعد التكرار التالي
- `recurring_active` (Boolean): حالة تفعيل التكرار

#### جدول الاستثناءات (recurring_task_exceptions)
- `parent_task_id`: معرف المهمة المتكررة الأصلية
- `exception_date`: تاريخ الاستثناء
- `exception_type`: نوع الاستثناء (تخطي، إعادة جدولة، تعديل)
- `new_date`: التاريخ الجديد في حالة إعادة الجدولة
- `modified_data`: البيانات المعدلة للمهمة
- `reason`: سبب الاستثناء
- `created_by`: منشئ الاستثناء

### 2. النماذج (Models)

#### Task Model المحسن
**أنماط التكرار:**
- `RECURRENCE_DAILY`: يومياً
- `RECURRENCE_WEEKLY`: أسبوعياً  
- `RECURRENCE_MONTHLY`: شهرياً
- `RECURRENCE_YEARLY`: سنوياً
- `RECURRENCE_CUSTOM`: مخصص

**العلاقات الجديدة:**
- `parentTask()`: المهمة الأب للمهام المنشأة
- `recurringInstances()`: المهام المنشأة من التكرار
- `recurringExceptions()`: استثناءات التكرار

**الوظائف الرئيسية:**
- `isRecurringMaster()`: التحقق من كون المهمة متكررة أصلية
- `isRecurringInstance()`: التحقق من كون المهمة منشأة من تكرار
- `calculateNextOccurrence()`: حساب موعد التكرار التالي
- `createRecurringInstance()`: إنشاء مهمة جديدة من التكرار
- `shouldContinueRecurring()`: التحقق من استمرار التكرار

#### RecurringTaskException Model
- إدارة استثناءات التكرار
- أنواع الاستثناءات: تخطي، إعادة جدولة، تعديل
- ربط بالمهمة الأصلية والمستخدم المنشئ

### 3. الخدمات (Services)

#### RecurringTaskService
**الوظائف الرئيسية:**
- `generateUpcomingTasks()`: إنشاء المهام القادمة
- `generateTaskInstances()`: إنشاء مهام لتكرار محدد
- `createException()`: إنشاء استثناء
- `skipOccurrence()`: تخطي تكرار محدد
- `rescheduleOccurrence()`: إعادة جدولة تكرار
- `modifyOccurrence()`: تعديل تكرار محدد
- `updateRecurrenceConfig()`: تحديث إعدادات التكرار
- `getRecurringTaskStats()`: إحصائيات المهام المتكررة
- `validateRecurrenceConfig()`: التحقق من صحة الإعدادات
- `previewOccurrences()`: معاينة التكرارات القادمة

### 4. أوامر التحكم (Console Commands)

#### GenerateRecurringTasks
```bash
php artisan tasks:generate-recurring --days=30 --cleanup
```
- إنشاء المهام المتكررة للأيام القادمة
- تنظيف المهام القديمة المكتملة
- عرض الإحصائيات

**الخيارات:**
- `--days`: عدد الأيام المراد إنشاء مهام لها
- `--force`: إجبار الإنشاء حتى لو كانت المهام موجودة
- `--cleanup`: تنظيف المهام القديمة المكتملة

### 5. وحدات التحكم (Controllers)

#### RecurringTaskController
**الصفحات المتاحة:**
- `index`: عرض قائمة المهام المتكررة
- `show`: تفاصيل المهمة المتكررة
- `edit`: تعديل إعدادات التكرار
- `exceptions`: إدارة استثناءات التكرار
- `statistics`: إحصائيات شاملة

**API Endpoints:**
- `POST /recurring-tasks/{task}/generate`: إنشاء مهام يدوياً
- `POST /recurring-tasks/{task}/toggle-active`: تفعيل/إيقاف التكرار
- `POST /recurring-tasks/preview`: معاينة التكرارات
- `POST /recurring-tasks/{task}/exceptions`: إنشاء استثناء
- `DELETE /recurring-tasks/{task}/exceptions/{exception}`: حذف استثناء

### 6. واجهات المستخدم (Views)

#### الصفحات الرئيسية
1. **recurring-tasks/index.blade.php**
   - عرض المهام المتكررة مع فلترة
   - إحصائيات سريعة
   - المهام القادمة في الشريط الجانبي

2. **recurring-tasks/show.blade.php**
   - تفاصيل المهمة المتكررة
   - قائمة المهام المنشأة
   - التكرارات القادمة
   - إجراءات سريعة

3. **recurring-tasks/edit.blade.php**
   - تعديل إعدادات التكرار
   - معاينة التكرارات القادمة
   - خيارات متقدمة للتكرار

4. **recurring-tasks/exceptions.blade.php**
   - إدارة استثناءات التكرار
   - إضافة استثناءات جديدة
   - عرض الاستثناءات الموجودة

#### تحسينات نموذج إنشاء المهام
- إضافة خيارات التكرار
- واجهة ديناميكية تظهر/تخفي الخيارات
- معاينة التكرارات قبل الحفظ

### 7. التوجيهات (Routes)

```php
Route::prefix('recurring-tasks')->name('recurring-tasks.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/statistics', 'statistics')->name('statistics');
    Route::post('/preview', 'preview')->name('preview');
    Route::get('/{task}', 'show')->name('show');
    Route::get('/{task}/edit', 'edit')->name('edit');
    Route::put('/{task}', 'update')->name('update');
    Route::post('/{task}/generate', 'generate')->name('generate');
    Route::post('/{task}/toggle-active', 'toggleActive')->name('toggle-active');
    Route::get('/{task}/exceptions', 'exceptions')->name('exceptions');
    Route::post('/{task}/exceptions', 'createException')->name('exceptions.create');
    Route::delete('/{task}/exceptions/{exception}', 'deleteException')->name('exceptions.delete');
});
```

## أنماط التكرار المدعومة

### 1. يومياً (Daily)
- تكرار كل عدد معين من الأيام
- مثال: كل يوم، كل 3 أيام، كل أسبوع

### 2. أسبوعياً (Weekly)
- تكرار في أيام محددة من الأسبوع
- إمكانية اختيار أيام متعددة
- مثال: كل يوم اثنين وأربعاء

### 3. شهرياً (Monthly)
- تكرار في يوم محدد من الشهر
- تعامل تلقائي مع الشهور القصيرة
- مثال: يوم 15 من كل شهر

### 4. سنوياً (Yearly)
- تكرار في تاريخ محدد كل سنة
- تحديد الشهر واليوم
- مثال: 1 يناير من كل سنة

## إدارة الاستثناءات

### أنواع الاستثناءات

1. **تخطي (Skip)**
   - تخطي تكرار محدد بالكامل
   - لا يتم إنشاء مهمة لهذا التاريخ

2. **إعادة جدولة (Reschedule)**
   - تغيير تاريخ التكرار لتاريخ آخر
   - المهمة تنشأ في التاريخ الجديد

3. **تعديل (Modify)**
   - تعديل خصائص المهمة لهذا التكرار
   - يمكن تغيير العنوان، الوصف، الأولوية

## المميزات المتقدمة

### 1. التحكم في دورة الحياة
- تفعيل/إيقاف التكرار
- تحديد تاريخ بداية ونهاية
- عدد أقصى للتكرارات

### 2. الإحصائيات والتقارير
- عدد المهام المتكررة النشطة
- إجمالي المهام المنشأة
- المهام القادمة والمتأخرة

### 3. التكامل مع النظام الحالي
- توافق كامل مع نظام المهام الموجود
- دعم التبعيات والتخصيصات
- تسجيل تاريخ العمليات

### 4. الأمان والصلاحيات
- ربط الاستثناءات بالمستخدم المنشئ
- فحص الصلاحيات للوصول والتعديل
- تسجيل جميع العمليات

## كيفية الاستخدام

### 1. إنشاء مهمة متكررة
1. في صفحة إنشاء مهمة جديدة
2. تفعيل "مهمة متكررة"
3. اختيار نمط وإعدادات التكرار
4. حفظ المهمة

### 2. إدارة المهام المتكررة
1. الانتقال لصفحة "المهام المتكررة"
2. عرض وإدارة المهام الموجودة
3. تعديل الإعدادات أو إيقاف التكرار

### 3. إدارة الاستثناءات
1. في تفاصيل المهمة المتكررة
2. الانتقال لصفحة "إدارة الاستثناءات"
3. إضافة استثناءات حسب الحاجة

### 4. مراقبة النظام
- تشغيل الأمر اليومي لإنشاء المهام
- مراجعة الإحصائيات بانتظام
- تنظيف المهام القديمة

## الجدولة التلقائية

### إعداد Cron Job
```bash
# في crontab
0 1 * * * cd /path/to/project && php artisan tasks:generate-recurring --days=7 --cleanup
```

### إعداد Laravel Scheduler
```php
// في app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('tasks:generate-recurring --days=7')
             ->daily()
             ->at('01:00');
             
    $schedule->command('tasks:generate-recurring --cleanup')
             ->weekly()
             ->sundays()
             ->at('02:00');
}
```

## أمثلة الاستخدام

### مهمة تقرير يومي
- النمط: يومياً
- الفترة: كل يوم
- التوقيت: 9:00 صباحاً

### اجتماع أسبوعي
- النمط: أسبوعياً
- الأيام: الاثنين والأربعاء
- الفترة: كل أسبوع

### تقرير شهري
- النمط: شهرياً
- اليوم: آخر يوم في الشهر
- الفترة: كل شهر

### مراجعة سنوية
- النمط: سنوياً
- التاريخ: 1 يناير
- الفترة: كل سنة

## النصائح والتوجيهات

### الأداء
- تشغيل الأوامر في أوقات قليلة الحمولة
- تنظيف المهام القديمة بانتظام
- مراقبة استهلاك قاعدة البيانات

### الصيانة
- فحص سجلات الأخطاء بانتظام
- تحديث إعدادات التكرار حسب الحاجة
- اختبار النظام قبل تطبيق تغييرات كبيرة

### التخصيص
- يمكن إضافة أنماط تكرار جديدة
- تخصيص واجهات المستخدم
- إضافة إشعارات للمهام المتكررة

تم تطوير هذا النظام ليكون مرناً وقابلاً للتوسع، مع دعم جميع احتياجات إدارة المهام المتكررة في بيئة العمل.