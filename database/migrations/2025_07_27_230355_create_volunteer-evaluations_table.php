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
        Schema::create('volunteer-evaluations', function (Blueprint $table) {
            $table->id();
            
            // مفتاح خارجي يربط التقييم بطلب التطوع
            $table->foreignId('volunteer-request_id')->constrained('volunteer-requests')->onDelete('cascade');
            
            // معلومات التقييم
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('evaluation_date');
            $table->text('notes')->nullable();
            $table->enum('recommendation', ['strong_approve', 'approve', 'conditional', 'reject', 'strong_reject']);
            
            // تقييمات مفصلة
            $table->decimal('interview_score', 5, 2)->default(0);
            $table->decimal('skills_assessment_score', 5, 2)->default(0);
            $table->decimal('motivation_score', 5, 2)->default(0);
            $table->decimal('availability_score', 5, 2)->default(0);
            $table->decimal('experience_score', 5, 2)->default(0);
            $table->decimal('communication_score', 5, 2)->default(0);
            $table->decimal('teamwork_score', 5, 2)->default(0);
            $table->decimal('reliability_score', 5, 2)->default(0);
            $table->decimal('adaptability_score', 5, 2)->default(0);
            $table->decimal('leadership_score', 5, 2)->default(0);
            $table->decimal('technical_skills_score', 5, 2)->default(0);
            $table->decimal('cultural_fit_score', 5, 2)->default(0);
            $table->decimal('commitment_score', 5, 2)->default(0);
            
            // النتيجة الإجمالية
            $table->decimal('overall_score', 5, 2)->default(0);
            
            // حالة التقييم
            $table->enum('status', ['pending', 'in_progress', 'completed', 'approved', 'rejected'])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer-evaluations');
    }
};
