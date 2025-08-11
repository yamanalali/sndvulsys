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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // إشعارات التخصيص
            $table->boolean('assignment_notifications')->default(true);
            $table->boolean('assignment_email')->default(true);
            $table->boolean('assignment_database')->default(true);
            
            // إشعارات تحديث الحالة
            $table->boolean('status_update_notifications')->default(true);
            $table->boolean('status_update_email')->default(true);
            $table->boolean('status_update_database')->default(true);
            
            // إشعارات التذكيرات
            $table->boolean('deadline_reminder_notifications')->default(true);
            $table->boolean('deadline_reminder_email')->default(true);
            $table->boolean('deadline_reminder_database')->default(true);
            $table->integer('deadline_reminder_days')->default(1); // عدد الأيام قبل الموعد النهائي
            
            // إشعارات التبعيات
            $table->boolean('dependency_notifications')->default(true);
            $table->boolean('dependency_email')->default(true);
            $table->boolean('dependency_database')->default(true);
            
            // إعدادات عامة
            $table->boolean('email_notifications')->default(true);
            $table->boolean('database_notifications')->default(true);
            $table->boolean('browser_notifications')->default(false);
            
            $table->timestamps();
            
            // فهرس فريد لكل مستخدم
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
