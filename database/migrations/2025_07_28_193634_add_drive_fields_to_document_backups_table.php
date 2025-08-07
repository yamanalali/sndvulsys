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
        Schema::table('document_backups', function (Blueprint $table) {
            $table->string('drive_file_id')->nullable()->after('backup_date'); // معرف الملف في Google Drive
            $table->text('drive_web_link')->nullable()->after('drive_file_id'); // رابط الويب للملف في Google Drive
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_backups', function (Blueprint $table) {
            $table->dropColumn(['drive_file_id', 'drive_web_link']);
        });
    }
};
