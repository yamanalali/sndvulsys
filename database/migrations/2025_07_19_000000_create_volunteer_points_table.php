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
        Schema::create('volunteer_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('volunteer_id'); // gönüllü
            $table->unsignedInteger('total_points')->default(0); // toplam puan
            $table->timestamp('last_updated_at')->nullable(); // son güncelleme tarihi
            $table->timestamps();

            $table->foreign('volunteer_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('volunteer_id'); // her gönüllü için sadece bir kayıt
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_points');
    }
}; 