<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleDriveService;
use Illuminate\Support\Facades\Log;

class TestGoogleDrive extends Command
{
    protected $signature = 'google:test';
    protected $description = 'اختبار الاتصال بـ Google Drive';

    public function handle()
    {
        $this->info('بدء اختبار الاتصال بـ Google Drive...');
        
        try {
            // التحقق من متغيرات البيئة
            $this->info('التحقق من متغيرات البيئة...');
            
            $folderId = config('services.google.drive_folder_id');
            $credentialsPath = config('services.google.credentials_path');
            
            $this->info("معرف المجلد: " . ($folderId ?: 'غير محدد'));
            $this->info("مسار الاعتماديات: " . $credentialsPath);
            
            if (!file_exists($credentialsPath)) {
                $this->error('ملف الاعتماديات غير موجود: ' . $credentialsPath);
                return 1;
            }
            
            $this->info('ملف الاعتماديات موجود ✓');
            
            // اختبار الاتصال
            $this->info('اختبار الاتصال بـ Google Drive...');
            
            $googleDrive = new GoogleDriveService();
            
            // محاولة الحصول على قائمة الملفات
            $files = $googleDrive->listBackupFiles();
            
            $this->info('تم الاتصال بنجاح بـ Google Drive ✓');
            $this->info('عدد الملفات في المجلد: ' . count($files));
            
            if (count($files) > 0) {
                $this->info('الملفات الموجودة:');
                foreach ($files as $file) {
                    $this->line("- {$file['name']} ({$file['id']})");
                }
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('فشل في الاتصال بـ Google Drive: ' . $e->getMessage());
            Log::error('خطأ في اختبار Google Drive', ['error' => $e->getMessage()]);
            return 1;
        }
    }
} 