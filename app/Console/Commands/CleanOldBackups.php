<?php

namespace App\Console\Commands;

use App\Models\DocumentBackup;
use App\Services\GoogleDriveService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanOldBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:clean-old-backups {--days=30 : عدد الأيام} {--storage=both : نوع التخزين (drive/local/both)} {--dry-run : عرض النسخ فقط دون حذفها}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'تنظيف النسخ الاحتياطية القديمة';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $storage = $this->option('storage');
        $dryRun = $this->option('dry-run');
        
        $cutoffDate = now()->subDays($days);
        
        $this->info("تنظيف النسخ الاحتياطية الأقدم من {$days} يوم...");
        $this->info("تاريخ القطع: {$cutoffDate->format('Y-m-d H:i:s')}");
        
        if ($dryRun) {
            $this->info("وضع المعاينة - لن يتم حذف أي نسخة احتياطية");
        }
        
        $deletedCount = 0;
        $errorCount = 0;
        
        // تنظيف النسخ المحلية
        if (in_array($storage, ['local', 'both'])) {
            $localBackups = DocumentBackup::whereNull('drive_file_id')
                ->where('backup_date', '<', $cutoffDate)
                ->get();
                
            $this->info("النسخ المحلية القديمة: " . $localBackups->count());
            
            foreach ($localBackups as $backup) {
                try {
                    if ($dryRun) {
                        $this->line("• {$backup->document->title} - {$backup->backup_date->format('Y-m-d H:i:s')} (محلي)");
                    } else {
                        // حذف الملف من التخزين
                        if ($backup->backup_path && \Storage::exists($backup->backup_path)) {
                            \Storage::delete($backup->backup_path);
                        }
                        
                        // حذف من قاعدة البيانات
                        $backup->delete();
                        
                        $deletedCount++;
                        $this->info("✓ تم حذف نسخة محلية: {$backup->document->title}");
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->error("✗ خطأ في حذف نسخة محلية: {$backup->document->title} - {$e->getMessage()}");
                }
            }
        }
        
        // تنظيف النسخ في Google Drive
        if (in_array($storage, ['drive', 'both'])) {
            $driveBackups = DocumentBackup::whereNotNull('drive_file_id')
                ->where('backup_date', '<', $cutoffDate)
                ->get();
                
            $this->info("النسخ في Google Drive القديمة: " . $driveBackups->count());
            
            foreach ($driveBackups as $backup) {
                try {
                    if ($dryRun) {
                        $this->line("• {$backup->document->title} - {$backup->backup_date->format('Y-m-d H:i:s')} (Google Drive)");
                    } else {
                        // حذف من Google Drive
                        $driveService = new GoogleDriveService();
                        $driveService->deleteFile($backup->drive_file_id);
                        
                        // حذف من قاعدة البيانات
                        $backup->delete();
                        
                        $deletedCount++;
                        $this->info("✓ تم حذف نسخة من Google Drive: {$backup->document->title}");
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->error("✗ خطأ في حذف نسخة من Google Drive: {$backup->document->title} - {$e->getMessage()}");
                    Log::error('خطأ في حذف نسخة من Google Drive', [
                        'backup_id' => $backup->id,
                        'drive_file_id' => $backup->drive_file_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        if (!$dryRun) {
            $this->info("تم الانتهاء من تنظيف النسخ الاحتياطية:");
            $this->info("✓ تم حذف: {$deletedCount}");
            $this->info("✗ أخطاء: {$errorCount}");
        }
        
        return 0;
    }
}
