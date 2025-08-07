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
        Schema::create('user_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // المستخدم الرئيسي
            $table->foreignId('related_user_id')->constrained('users')->onDelete('cascade'); // المستخدم المرتبط
            $table->enum('relationship_type', [
                'supervisor', // مشرف
                'subordinate', // مرؤوس
                'colleague', // زميل
                'mentor', // مرشد
                'mentee', // متدرب
                'project_leader', // قائد مشروع
                'project_member', // عضو مشروع
                'team_leader', // قائد فريق
                'team_member', // عضو فريق
                'client', // عميل
                'partner' // شريك
            ]);
            $table->enum('status', ['active', 'inactive', 'pending', 'blocked'])->default('active');
            $table->date('start_date')->nullable(); // تاريخ بداية العلاقة
            $table->date('end_date')->nullable(); // تاريخ انتهاء العلاقة
            $table->text('notes')->nullable(); // ملاحظات
            $table->json('permissions')->nullable(); // صلاحيات خاصة
            $table->timestamps();
            
            // فهارس للأداء
            $table->unique(['user_id', 'related_user_id', 'relationship_type'], 'user_rel_unique');
            $table->index(['user_id', 'status']);
            $table->index(['related_user_id', 'status']);
            $table->index('relationship_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_relationships');
    }
};
