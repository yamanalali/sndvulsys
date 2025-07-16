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
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade')->comment('The task that has a dependency');
            $table->foreignId('depends_on_task_id')->constrained('tasks')->onDelete('cascade')->comment('The task this task depends on');
            $table->enum('dependency_type', ['finish_to_start', 'start_to_start', 'finish_to_finish', 'start_to_finish'])->default('finish_to_start')->comment('Type of dependency');
            $table->boolean('is_mandatory')->default(true)->comment('Is this dependency mandatory?');
            $table->text('notes')->nullable()->comment('Additional notes about the dependency');
            $table->timestamps();

            $table->unique(['task_id', 'depends_on_task_id'], 'unique_task_dependency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_dependencies');
    }
};
