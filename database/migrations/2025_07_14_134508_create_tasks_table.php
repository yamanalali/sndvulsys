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
            $table->enum('status', [
                'new', 
                'in_progress', 
                'pending_review', 
                'awaiting_approval', 
                'approved', 
                'rejected', 
                'on_hold', 
                'completed', 
                'cancelled', 
                'archived'
            ])->default('new');
            $table->enum('priority', ['urgent', 'high', 'medium'   , 'low', 'none'])->default('medium');
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // مفتاح أجنبي للفئة
            $table->unsignedBigInteger('created_by')->nullable()->comment('User who created the task');
            $table->unsignedBigInteger('assigned_to')->nullable()->comment('User assigned to the task');
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->date('completed_at')->nullable();
            $table->unsignedTinyInteger('progress')->default(0)->comment('Progress percentage 0-100');
            $table->text('notes')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable()->comment('e.g. daily, weekly, monthly');
            $table->timestamps();
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
