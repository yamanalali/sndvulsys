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
            $table->text('description')->nullable()->after('name');
            $table->string('category')->nullable()->after('description');
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate')->after('category');
            $table->boolean('is_active')->default(true)->after('difficulty');
            $table->boolean('is_featured')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn(['description', 'category', 'difficulty', 'is_active', 'is_featured']);
        });
    }
};
