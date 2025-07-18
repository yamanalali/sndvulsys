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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('volunteer_id'); // değerlendirilen gönüllü
            $table->unsignedBigInteger('evaluation_type_id'); // değerlendirme türü (FK)
            $table->unsignedTinyInteger('score'); // 1-5 arası puan
            $table->date('date'); // değerlendirme tarihi
            $table->unsignedBigInteger('evaluator_id'); // değerlendiren kişi
            $table->timestamps();

            $table->foreign('volunteer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('evaluation_type_id')->references('id')->on('evaluation_types')->onDelete('cascade');
            $table->foreign('evaluator_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
}; 