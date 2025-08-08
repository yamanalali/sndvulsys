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
        Schema::create('volunteer-requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ربط الطلب بالمستخدم
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('national_id')->nullable(); // رقم الهوية الوطنية
            $table->date('birth_date')->nullable(); // تاريخ الميلاد
            $table->enum('gender', ['male', 'female', 'other'])->nullable(); // الجنس
            $table->enum('social_status', ['single', 'married'])->nullable(); // الحالة الاجتماعية 
            $table->string('address')->nullable(); // العنوان
            $table->string('country')->nullable(); // الدولة
            $table->string('city')->nullable(); // المدينة
            $table->string('education_level')->nullable(); // المستوى التعليمي
            $table->string('field_of_study')->nullable(); // التخصص الدراسي
            $table->string('occupation')->nullable(); // الوظيفة الحالية
            $table->string('skills')->nullable(); // المهارات
            $table->text('languages')->nullable(); // اللغات ومستوياتها
            $table->text('motivation')->nullable(); // سبب التقديم
            $table->text('previous_experience')->nullable(); // الخبرة السابقة في العمل التطوعي
            $table->string('preferred_area')->nullable(); // المجال التطوعي المفضل
            $table->string('availability')->nullable(); // مدى التفرغ (أيام/ساعات التطوع)
            $table->boolean('has_previous_volunteering')->default(false); // هل سبق له التطوع
            $table->string('preferred_organization_type')->nullable(); // نوع المنظمة المفضلة
            $table->string('cv')->nullable(); // السيرة الذاتية
            $table->enum('status', ['pending', 'approved', 'rejected', 'withdrawn'])->default('pending'); // حالة الطلب
            $table->timestamp('reviewed_at')->nullable(); // تاريخ مراجعة الطلب
            $table->unsignedBigInteger('reviewed_by')->nullable(); // من قام بمراجعة الطلب
            $table->text('admin_notes')->nullable(); // ملاحظات الإدارة
            $table->timestamps();

            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });

       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer-requests');
    }
};
