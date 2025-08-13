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
        Schema::table('volunteer-evaluations', function (Blueprint $table) {
            // تغيير عمود recommendation ليشمل القيم الجديدة
            $table->enum('recommendation', [
                'strong_approve', 
                'approve', 
                'conditional', 
                'reject', 
                'strong_reject',
                'accepted',
                'training_required',
                'rejected'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteer-evaluations', function (Blueprint $table) {
            // إرجاع القيم الأصلية
            $table->enum('recommendation', [
                'strong_approve', 
                'approve', 
                'conditional', 
                'reject', 
                'strong_reject'
            ])->change();
        });
    }
};

