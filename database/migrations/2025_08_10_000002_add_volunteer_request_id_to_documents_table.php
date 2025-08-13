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
        Schema::table('documents', function (Blueprint $table) {
            // Link the document directly to the volunteer request
            if (!Schema::hasColumn('documents', 'volunteer-request_id')) {
                $table->foreignId('volunteer-request_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('volunteer-requests')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'volunteer-request_id')) {
                $table->dropConstrainedForeignId('volunteer-request_id');
            }
        });
    }
};


