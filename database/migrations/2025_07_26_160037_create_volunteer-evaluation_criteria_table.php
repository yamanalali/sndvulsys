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
        if (!Schema::hasTable('volunteer-evaluation_criteria')) {
            Schema::create('volunteer-evaluation_criteria', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('evaluation_id');
                $table->string('criteria_name');
                $table->text('criteria_description')->nullable();
                $table->decimal('score', 5, 2)->default(0);
                $table->decimal('max_score', 5, 2)->default(100);
                $table->decimal('weight', 5, 2)->default(0);
                $table->text('comments')->nullable();
                $table->timestamp('evaluated_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer-evaluation_criteria');
    }
};
