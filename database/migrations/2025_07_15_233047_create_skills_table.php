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
        // إنشاء جدول المهارات إذا لم يكن موجوداً
        if (!Schema::hasTable('skills')) {
            Schema::create('skills', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable(); // وصف المهارة
                $table->enum('category', ['technical', 'soft_skills', 'language', 'management', 'creative', 'other'])->default('other'); // فئة المهارة
                $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner'); // مستوى المهارة
                $table->boolean('is_active')->default(true); // حالة المهارة (نشطة/غير نشطة)
                $table->timestamps();
            });
        } else {
            // إضافة الأعمدة المفقودة إذا كان الجدول موجوداً
            Schema::table('skills', function (Blueprint $table) {
                if (!Schema::hasColumn('skills', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('skills', 'category')) {
                    $table->enum('category', ['technical', 'soft_skills', 'language', 'management', 'creative', 'other'])->default('other');
                }
                if (!Schema::hasColumn('skills', 'level')) {
                    $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
                }
                if (!Schema::hasColumn('skills', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
            });
        }

        // جدول pivot للمهارات وطلبات التطوع
        if (!Schema::hasTable('skill_volunteer-request')) {
            Schema::create('skill_volunteer-request', function (Blueprint $table) {
                $table->id();
                $table->foreignId('skill_id')->constrained()->onDelete('cascade');
                $table->foreignId('volunteer-request_id')->constrained()->onDelete('cascade');
                $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
                $table->integer('years_experience')->default(0);
                $table->timestamps();
                
                $table->unique(['skill_id', 'volunteer-request_id']);
            });
        }

        // جدول pivot للمهارات والمستخدمين
        if (!Schema::hasTable('user_skills')) {
            Schema::create('user_skills', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('skill_id')->constrained()->onDelete('cascade');
                $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
                $table->integer('years_experience')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->unique(['user_id', 'skill_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_skills');
        Schema::dropIfExists('skill_volunteer-request');
        Schema::dropIfExists('skills');
    }
};
