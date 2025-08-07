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
        Schema::create('previous_experiences', function (Blueprint $table) {
            $table->id();

            // مفتاح خارجي يربط الخبرة بطلب التطوع
            $table->foreignId('volunteer-request_id')->constrained('volunteer-requests')->onDelete('cascade');

            // عنوان الخبرة، ووصفها
            $table->string('title');
            $table->text('description')->nullable();
            
            // معلومات المؤسسة والمنصب
            $table->string('organization');
            $table->string('position');
            
            // تواريخ البداية والنهاية
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            
            // تفاصيل إضافية
            $table->text('responsibilities')->nullable(); // المسؤوليات
            $table->text('achievements')->nullable(); // الإنجازات
            $table->text('skills_used')->nullable(); // المهارات المستخدمة

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('previous_experiences');
    }
};
