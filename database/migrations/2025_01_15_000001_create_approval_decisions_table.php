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
        Schema::create('approval_decisions', function (Blueprint $table) {
            $table->id();
            
            // ربط القرار بالطلب
            $table->foreignId('volunteer-request_id')->constrained('volunteer-requests')->onDelete('cascade');
            
            // بيانات القرار المبسطة
            $table->enum('decision_status', ['approved', 'rejected'])->comment('حالة القرار: مقبول أو مرفوض');
            $table->text('decision_reason')->comment('سبب القرار');
            $table->foreignId('decision_by')->constrained('users')->onDelete('cascade')->comment('المستخدم الإداري الذي اتخذ القرار');
            $table->timestamp('decision_at')->nullable()->comment('تاريخ القرار');
            
            $table->timestamps();
            
            // فهارس للبحث السريع
            $table->index(['volunteer-request_id']);
            $table->index(['decision_status']);
            $table->index('decision_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_decisions');
    }
}; 