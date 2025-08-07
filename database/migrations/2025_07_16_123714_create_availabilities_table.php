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
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();

            // كل توفر مرتبط بطلب تطوع
            $table->foreignId('volunteer-request_id')->constrained()->onDelete('cascade');

            // اليوم المتاح (مثلاً: الإثنين، أو Monday)
            $table->enum('day', ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday']);

            // فترة الوقت
            $table->enum('time_slot', ['morning', 'afternoon', 'evening', 'night', 'flexible'])->nullable();
            
            // وقت البداية والنهاية المحدد
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            // حالة التوفر
            $table->boolean('is_available')->default(true);
            
            // ملاحظات إضافية
            $table->text('notes')->nullable();
            
            // الساعات المفضلة في الأسبوع
            $table->integer('preferred_hours_per_week')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
