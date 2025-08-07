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
        Schema::create('document_backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('backup_path'); // مسار النسخة الاحتياطية
            $table->string('backup_hash'); // هاش النسخة الاحتياطية
            $table->bigInteger('backup_size'); // حجم النسخة الاحتياطية
            $table->enum('backup_type', ['automatic', 'manual', 'scheduled'])->default('automatic');
            $table->text('backup_notes')->nullable(); // ملاحظات النسخة الاحتياطية
            $table->timestamp('backup_date'); // تاريخ إنشاء النسخة الاحتياطية
            $table->timestamps();
            
            // فهارس للأداء
            $table->index(['document_id', 'backup_date']);
            $table->index('backup_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_backups');
    }
};
