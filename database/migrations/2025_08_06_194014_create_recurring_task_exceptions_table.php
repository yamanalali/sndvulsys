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
        Schema::create('recurring_task_exceptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_task_id')->comment('Parent recurring task ID');
            $table->date('exception_date')->comment('Date of the exception');
            $table->enum('exception_type', ['skip', 'reschedule', 'modify'])->comment('Type of exception');
            $table->date('new_date')->nullable()->comment('New date if rescheduled');
            $table->json('modified_data')->nullable()->comment('Modified task data for this occurrence');
            $table->text('reason')->nullable()->comment('Reason for the exception');
            $table->unsignedBigInteger('created_by')->comment('User who created the exception');
            $table->timestamps();

            // Foreign keys
            $table->foreign('parent_task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate exceptions for same date
            $table->unique(['parent_task_id', 'exception_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_task_exceptions');
    }
};
