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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_volunteer-request');
    }
};
