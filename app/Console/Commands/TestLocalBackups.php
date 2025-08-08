<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;
use Illuminate\Support\Facades\Log;

class TestLocalBackups extends Command
{
    protected $signature = 'backups:test-local {document-id}';
    protected $description = 'اختبار النسخ الاحتياطية المحلية';

    public function handle()
    {
        $documentId = $this->argument('document-id');
        
        try {
            $document = Document::find($documentId);
            
            if (!$document) {
                $this->error("المستند غير موجود: {$documentId}");
                return 1;
            }
            
            $this->info("اختبار النسخ الاحتياطية للمستند: {$document->title}");
            
            // إنشاء نسخة احتياطية محلية
            $this->info('إنشاء نسخة احتياطية محلية...');
            
            $backup = $document->createBackup('manual', 'نسخة احتياطية تجريبية من الأمر');
            
            if ($backup) {
                $this->info('تم إنشاء النسخة الاحتياطية بنجاح ✓');
                $this->info("معرف النسخة: {$backup->id}");
                $this->info("المسار: {$backup->backup_path}");
                $this->info("الحجم: {$backup->backup_size} بايت");
                $this->info("التاريخ: {$backup->backup_date}");
            } else {
                $this->error('فشل في إنشاء النسخة الاحتياطية');
                return 1;
            }
            
            // عرض النسخ الاحتياطية الموجودة
            $this->info('النسخ الاحتياطية الموجودة:');
            $backups = $document->backups()->orderBy('backup_date', 'desc')->get();
            
            if ($backups->count() > 0) {
                foreach ($backups as $backup) {
                    $this->line("- {$backup->backup_type} - {$backup->backup_date} - {$backup->backup_size} بايت");
                }
            } else {
                $this->line('لا توجد نسخ احتياطية');
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('حدث خطأ: ' . $e->getMessage());
            Log::error('خطأ في اختبار النسخ الاحتياطية المحلية', [
                'document_id' => $documentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
} 