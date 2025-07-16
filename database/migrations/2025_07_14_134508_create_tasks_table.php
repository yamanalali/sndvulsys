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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('project_id');
            // Remove user_id from tasks table to allow many-to-many or one-to-many assignment via assignments table
            $table->text('description')->nullable();
            $table->enum('status', ['new', 'in_progress', 'pending', 'completed', 'cancelled'])->default('new');
            $table->enum('priority', ['urgent', 'high', 'medium', 'low'])->default('medium');
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // مفتاح أجنبي للفئة
            $table->date('deadline')->nullable();
            $table->timestamps();
            // No user_id foreign key here; assignments table will handle user-task relationships
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
