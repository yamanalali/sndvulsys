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
        Schema::table('skills', function (Blueprint $table) {
            // إضافة الأعمدة المفقودة
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn(['description', 'category', 'level', 'is_active']);
        });
    }
};
