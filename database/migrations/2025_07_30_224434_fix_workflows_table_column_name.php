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
        // لا نحتاج لإعادة تسمية العمود لأنه صحيح بالفعل
        // العمود يسمى volunteer-request-id وهو صحيح
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نحتاج لإعادة تسمية العمود لأنه صحيح بالفعل
    }
};
