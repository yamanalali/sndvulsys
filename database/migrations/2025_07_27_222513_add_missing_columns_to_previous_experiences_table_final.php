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
        Schema::table('previous_experiences', function (Blueprint $table) {
            // إضافة الأعمدة المفقودة إذا لم تكن موجودة
            if (!Schema::hasColumn('previous_experiences', 'responsibilities')) {
                $table->text('responsibilities')->nullable(); // المسؤوليات
            }
            if (!Schema::hasColumn('previous_experiences', 'achievements')) {
                $table->text('achievements')->nullable(); // الإنجازات
            }
            if (!Schema::hasColumn('previous_experiences', 'skills_used')) {
                $table->text('skills_used')->nullable(); // المهارات المستخدمة
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('previous_experiences', function (Blueprint $table) {
            $table->dropColumn(['responsibilities', 'achievements', 'skills_used']);
        });
    }
};
