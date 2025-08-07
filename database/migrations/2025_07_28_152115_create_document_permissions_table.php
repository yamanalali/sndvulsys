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
        Schema::create('document_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('permission_type', [
                'view', // عرض
                'download', // تحميل
                'edit', // تعديل
                'delete', // حذف
                'share', // مشاركة
                'admin' // إدارة كاملة
            ]);
            $table->enum('grant_type', ['direct', 'inherited', 'temporary'])->default('direct');
            $table->timestamp('expires_at')->nullable(); // تاريخ انتهاء الصلاحية
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();
            
            // فهارس للأداء
            $table->unique(['document_id', 'user_id', 'permission_type']);
            $table->index(['user_id', 'permission_type']);
            $table->index(['document_id', 'permission_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_permissions');
    }
};
