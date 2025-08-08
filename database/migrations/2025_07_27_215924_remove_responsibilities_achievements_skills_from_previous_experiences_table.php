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
            // حذف الأعمدة غير المطلوبة
            if (Schema::hasColumn('previous_experiences', 'responsibilities')) {
                $table->dropColumn('responsibilities');
            }
            if (Schema::hasColumn('previous_experiences', 'achievements')) {
                $table->dropColumn('achievements');
            }
            if (Schema::hasColumn('previous_experiences', 'skills_used')) {
                $table->dropColumn('skills_used');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('previous_experiences', function (Blueprint $table) {
            // إعادة إضافة الأعمدة المحذوفة
            $table->text('responsibilities')->nullable();
            $table->text('achievements')->nullable();
            $table->text('skills_used')->nullable();
        });
    }
};
