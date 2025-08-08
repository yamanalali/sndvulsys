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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_id')->unique(); // معرف فريد للمستند
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ربط بالمستخدم
            $table->string('title'); // عنوان المستند
            $table->text('description')->nullable(); // وصف المستند
            $table->string('file_name'); // اسم الملف الأصلي
            $table->string('file_path'); // مسار الملف المخزن
            $table->string('file_type'); // نوع الملف (pdf, doc, etc.)
            $table->bigInteger('file_size'); // حجم الملف بالبايت
            $table->string('mime_type'); // نوع MIME
            $table->string('hash')->unique(); // هاش الملف للأمان
            $table->enum('status', ['active', 'archived', 'deleted'])->default('active');
            $table->enum('privacy_level', ['public', 'private', 'restricted'])->default('private');
            $table->json('metadata')->nullable(); // بيانات إضافية
            $table->timestamp('expires_at')->nullable(); // تاريخ انتهاء الصلاحية
            $table->timestamps();
            
            // فهارس للأداء
            $table->index(['user_id', 'status']);
            $table->index(['file_type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
