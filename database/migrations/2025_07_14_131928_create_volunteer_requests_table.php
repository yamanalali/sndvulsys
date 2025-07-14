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
        Schema::create('volunteer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ربط الطلب بالمستخدم
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->text('motivation')->nullable(); // سبب التقديم
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // حالة الطلب
            $table->timestamps();
        });

        Schema::create('evaluation_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('criterion_name'); // اسم معيار التقييم
            $table->unsignedInteger('weight'); // الوزن النسبي للمعيار (مثلاً 40%)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_requests');
    }
};
