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
        Schema::create('evaluation_criteria', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('evaluator_id');  // değerlendirmeyi yapan kullanıcı
            $table->unsignedBigInteger('volunteer_id');  // değerlendirilen gönüllü (users tablosundaki kişi)

            $table->string('type'); // değerlendirme türü
            $table->tinyInteger('rating');  // 1 ile 5 arası puan
            $table->text('comment')->nullable(); // yorum (isteğe bağlı)

            $table->timestamps();

            // ilişkiler
            $table->foreign('evaluator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('volunteer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_criteria');
    }
}; 