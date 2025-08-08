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
            if (!Schema::hasColumn('volunteer-requests', 'cv')) {
                $table->string('cv')->nullable()->after('preferred_organization_type'); // السيرة الذاتية
            }
            if (!Schema::hasColumn('volunteer-requests', 'languages')) {
                $table->text('languages')->nullable()->after('skills'); // اللغات ومستوياتها
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteer-requests', function (Blueprint $table) {
            $table->dropColumn(['cv']);
        });
    }
};
