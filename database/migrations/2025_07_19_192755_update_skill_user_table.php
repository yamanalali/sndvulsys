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
        Schema::table('skill_user', function (Blueprint $table) {
            // إضافة الحقول المفقودة إذا لم تكن موجودة
            if (!Schema::hasColumn('skill_user', 'skill_level')) {
                $table->string('skill_level')->nullable()->after('user_id');
            }
            
            if (!Schema::hasColumn('skill_user', 'experience_years')) {
                $table->string('experience_years')->nullable()->after('skill_level');
            }
            
            if (!Schema::hasColumn('skill_user', 'notes')) {
                $table->text('notes')->nullable()->after('experience_years');
            }
            
            if (!Schema::hasColumn('skill_user', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('notes');
            }
            
            if (!Schema::hasColumn('skill_user', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('is_verified');
            }
            
            if (!Schema::hasColumn('skill_user', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
                $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            }
            
            // إضافة indexes إذا لم تكن موجودة
            if (!Schema::hasIndex('skill_user', 'skill_user_user_id_skill_level_index')) {
                $table->index(['user_id', 'skill_level']);
            }
            
            if (!Schema::hasIndex('skill_user', 'skill_user_skill_id_is_verified_index')) {
                $table->index(['skill_id', 'is_verified']);
            }
            
            if (!Schema::hasIndex('skill_user', 'skill_user_verified_by_index')) {
                $table->index('verified_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skill_user', function (Blueprint $table) {
            // حذف indexes
            $table->dropIndex(['user_id', 'skill_level']);
            $table->dropIndex(['skill_id', 'is_verified']);
            $table->dropIndex(['verified_by']);
            
            // حذف foreign key
            $table->dropForeign(['verified_by']);
            
            // حذف الحقول
            $table->dropColumn([
                'skill_level',
                'experience_years',
                'notes',
                'is_verified',
                'verified_at',
                'verified_by'
            ]);
        });
    }
};
