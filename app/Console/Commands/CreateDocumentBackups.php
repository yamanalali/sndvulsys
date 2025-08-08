<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\DocumentBackup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateDocumentBackups extends Command
{
    protected $signature = 'documents:backup 
                            {--type=automatic : نوع النسخة الاحتياطية (automatic/manual)}
                            {--storage=local : نوع التخزين (drive/local/both)}
                            {--document-id= : معرف المستند المحدد}
                            {--all : إنشاء نسخ احتياطية لجميع المستندات}';

    protected $description = 'إنشاء نسخ احتياطية للمستندات';

    public function handle()
    {
        $type = $this->option('type');
        $storage = $this->option('storage');
        $documentId = $this->option('document-id');
        $all = $this->option('all');

        try {
            if ($documentId) {
                // إنشاء نسخة احتياطية لمستند محدد
                $document = Document::find($documentId);
                if (!$document) {
                    $this->error("المستند غير موجود: {$documentId}");
                    return 1;
                }
                
                $this->createBackupForDocument($document, $type, $storage);
                
            } elseif ($all) {
                // إنشاء نسخ احتياطية لجميع المستندات
                $documents = Document::where('status', 'active')->get();
                
                $this->info("إنشاء نسخ احتياطية لـ {$documents->count()} مستند...");
                
                $bar = $this->output->createProgressBar($documents->count());
                $bar->start();
                
                foreach ($documents as $document) {
                    $this->createBackupForDocument($document, $type, $storage);
                    $bar->advance();
                }
                
                $bar->finish();
                $this->newLine();
                
            } else {
                $this->error('يجب تحديد --document-id أو --all');
                return 1;
            }
            
            $this->info('تم إنشاء النسخ الاحتياطية بنجاح!');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('حدث خطأ: ' . $e->getMessage());
            Log::error('خطأ في إنشاء النسخ الاحتياطية', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    private function createBackupForDocument($document, $type, $storage)
    {
        try {
            $notes = "نسخة احتياطية {$type} من خلال الأمر";
            
            if ($storage === 'drive' || $storage === 'both') {
                try {
                    $backup = $document->createDriveBackup($type, $notes);
                    $this->info("تم إنشاء نسخة احتياطية في Google Drive للمستند: {$document->title}");
                } catch (\Exception $e) {
                    $this->warn("فشل في إنشاء نسخة احتياطية في Google Drive للمستند: {$document->title} - {$e->getMessage()}");
                }
            }
            
            if ($storage === 'local' || $storage === 'both') {
                try {
                    $backup = $document->createBackup($type, $notes);
                    $this->info("تم إنشاء نسخة احتياطية محلية للمستند: {$document->title}");
                } catch (\Exception $e) {
                    $this->warn("فشل في إنشاء نسخة احتياطية محلية للمستند: {$document->title} - {$e->getMessage()}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("خطأ في إنشاء نسخة احتياطية للمستند: {$document->title} - {$e->getMessage()}");
        }
    }
}
