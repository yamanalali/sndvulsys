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
            // إضافة الحقول الجديدة
            $table->string('skill_level')->nullable()->after('category');
            $table->string('experience_years')->nullable()->after('skill_level');
            $table->text('certificates')->nullable()->after('experience_years');
            $table->boolean('is_public')->default(true)->after('certificates');
            $table->boolean('available_for_volunteering')->default(true)->after('is_public');
            $table->unsignedBigInteger('user_id')->nullable()->after('available_for_volunteering');
            
            // إضافة foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // إضافة indexes للتحسين
            $table->index(['category', 'skill_level']);
            $table->index(['is_public', 'available_for_volunteering']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            // حذف indexes
            $table->dropIndex(['category', 'skill_level']);
            $table->dropIndex(['is_public', 'available_for_volunteering']);
            $table->dropIndex(['user_id']);
            
            // حذف foreign key
            $table->dropForeign(['user_id']);
            
            // حذف الحقول
            $table->dropColumn([
                'skill_level',
                'experience_years', 
                'certificates',
                'is_public',
                'available_for_volunteering',
                'user_id'
            ]);
        });
    }
};
