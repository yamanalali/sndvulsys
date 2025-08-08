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
        Schema::table('volunteer-requests', function (Blueprint $table) {
            if (!Schema::hasColumn('volunteer-requests', 'social_status')) {
                $table->enum('social_status', ['single', 'married'])->nullable()->after('gender'); // الحالة الاجتماعية
            }
            if (!Schema::hasColumn('volunteer-requests', 'field_of_study')) {
                $table->string('field_of_study')->nullable()->after('education_level'); // التخصص الدراسي
            }
            if (!Schema::hasColumn('volunteer-requests', 'cv')) {
                $table->string('cv')->nullable()->after('preferred_organization_type'); // السيرة الذاتية
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteer-requests', function (Blueprint $table) {
            $table->dropColumn(['social_status', 'field_of_study', 'cv']);
        });
    }
};
