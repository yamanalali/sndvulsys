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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            // بيانات المستلم
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('المستخدم المستلم');
            
            // بيانات الإشعار
            $table->string('title')->comment('عنوان الإشعار');
            $table->text('message')->comment('محتوى الإشعار');
            $table->enum('type', [
                'approval_decision',
                'rejection_decision', 
                'request_review',
                'status_update',
                'reminder',
                'system_alert'
            ])->comment('نوع الإشعار');
            
            // ربط الإشعار بالعنصر المرتبط
            $table->string('notifiable_type')->nullable()->comment('نوع العنصر المرتبط');
            $table->unsignedBigInteger('notifiable_id')->nullable()->comment('معرف العنصر المرتبط');
            
            // بيانات الإشعار
            $table->json('data')->nullable()->comment('بيانات إضافية للإشعار');
            $table->boolean('is_read')->default(false)->comment('هل تم قراءة الإشعار');
            $table->timestamp('read_at')->nullable()->comment('تاريخ قراءة الإشعار');
            
            // بيانات الإرسال
            $table->enum('delivery_method', ['email', 'sms', 'in_app', 'all'])->default('in_app')->comment('طريقة الإرسال');
            $table->boolean('email_sent')->default(false)->comment('هل تم إرسال البريد الإلكتروني');
            $table->boolean('sms_sent')->default(false)->comment('هل تم إرسال الرسالة النصية');
            $table->timestamp('sent_at')->nullable()->comment('تاريخ الإرسال');
            
            $table->timestamps();
            
            // فهارس للبحث السريع
            $table->index(['user_id', 'is_read']);
            $table->index(['type', 'created_at']);
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
}; 