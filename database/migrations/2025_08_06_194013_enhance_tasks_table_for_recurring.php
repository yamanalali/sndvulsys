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
        Schema::table('tasks', function (Blueprint $table) {
            // Enhanced recurring task fields
            $table->json('recurrence_config')->nullable()->after('recurrence_pattern')->comment('JSON configuration for recurrence rules');
            $table->date('recurrence_start_date')->nullable()->after('recurrence_config')->comment('Start date for recurring tasks');
            $table->date('recurrence_end_date')->nullable()->after('recurrence_start_date')->comment('End date for recurring tasks (null = infinite)');
            $table->integer('recurrence_max_occurrences')->nullable()->after('recurrence_end_date')->comment('Maximum number of occurrences (null = infinite)');
            $table->integer('recurrence_current_count')->default(0)->after('recurrence_max_occurrences')->comment('Current number of generated occurrences');
            $table->unsignedBigInteger('parent_task_id')->nullable()->after('recurrence_current_count')->comment('Parent task ID for recurring instances');
            $table->boolean('is_recurring_instance')->default(false)->after('parent_task_id')->comment('Is this task an instance of a recurring task');
            $table->datetime('next_occurrence_date')->nullable()->after('is_recurring_instance')->comment('Next scheduled occurrence for this recurring task');
            $table->boolean('recurring_active')->default(true)->after('next_occurrence_date')->comment('Is the recurring schedule active');
            
            // Add foreign key for parent task
            $table->foreign('parent_task_id')->references('id')->on('tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['parent_task_id']);
            $table->dropColumn([
                'recurrence_config',
                'recurrence_start_date', 
                'recurrence_end_date',
                'recurrence_max_occurrences',
                'recurrence_current_count',
                'parent_task_id',
                'is_recurring_instance',
                'next_occurrence_date',
                'recurring_active'
            ]);
        });
    }
};