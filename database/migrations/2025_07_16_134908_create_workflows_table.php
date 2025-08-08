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
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();

            $table->foreignId('volunteer-request-id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');

            $table->enum('status', ['pending', 'in_review', 'approved', 'rejected', 'needs_revision', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('notes')->nullable();
            
            // معلومات الخطوة
            $table->integer('step')->default(1);
            $table->string('step_name')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->integer('next_step')->nullable();
            
            // معلومات إضافية
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->integer('estimated_duration')->nullable(); // بالساعات
            $table->integer('actual_duration')->nullable(); // بالساعات
            $table->timestamp('due_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};
