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
            // حذف الأعمدة المرجعية
            if (Schema::hasColumn('previous_experiences', 'reference_name')) {
                $table->dropColumn('reference_name');
            }
            if (Schema::hasColumn('previous_experiences', 'reference_contact')) {
                $table->dropColumn('reference_contact');
            }
            if (Schema::hasColumn('previous_experiences', 'reference_email')) {
                $table->dropColumn('reference_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('previous_experiences', function (Blueprint $table) {
            // إعادة إضافة الأعمدة المرجعية
            $table->string('reference_name')->nullable();
            $table->string('reference_contact')->nullable();
            $table->string('reference_email')->nullable();
        });
    }
};
