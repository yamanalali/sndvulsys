<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('advanced_search', function (Blueprint $table) {
            $table->id();
            
            // معلومات البحث الأساسية
            $table->string('search_term')->nullable(); // مصطلح البحث
            $table->string('search_type')->default('volunteer_requests'); // نوع البحث
            $table->json('filters')->nullable(); // المرشحات المطبقة
            $table->json('sort_options')->nullable(); // خيارات الترتيب
            
            // معلومات المستخدم والجلسة
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // معرف الجلسة
            $table->string('ip_address')->nullable(); // عنوان IP
            
            // نتائج البحث
            $table->integer('total_results')->default(0); // إجمالي النتائج
            $table->json('search_results')->nullable(); // نتائج البحث المخزنة
            $table->timestamp('executed_at')->nullable(); // وقت تنفيذ البحث
            
            // إحصائيات البحث
            $table->integer('execution_time_ms')->nullable(); // وقت التنفيذ بالميلي ثانية
            $table->boolean('is_saved')->default(false); // هل البحث محفوظ
            $table->string('saved_name')->nullable(); // اسم البحث المحفوظ
            
            // معلومات إضافية
            $table->text('notes')->nullable(); // ملاحظات على البحث
            $table->boolean('is_public')->default(false); // هل البحث عام
            $table->json('sharing_options')->nullable(); // خيارات المشاركة
            
            $table->timestamps();
            
            // فهارس للبحث السريع
            $table->index(['user_id', 'search_type']);
            $table->index(['search_term']);
            $table->index(['executed_at']);
            $table->index(['is_saved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advanced_search');
    }
}; 