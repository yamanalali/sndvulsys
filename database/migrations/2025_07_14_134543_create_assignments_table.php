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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['assigned', 'in_progress', 'submitted', 'completed', 'overdue', 'cancelled'])->default('assigned');
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('progress')->default(0); // percentage 0-100
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
