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
            $table->foreignId('volunteer_request_id')->constrained()->onDelete('cascade');

            // عنوان الخبرة، ووصفها
            $table->string('title');
            $table->text('description')->nullable();

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
