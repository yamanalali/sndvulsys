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
        Schema::create('evaluation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // değerlendirme türü adı (ör: Görev, Davranış, Katılım)
            $table->string('description')->nullable(); // açıklama
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_types');
    }
}; 