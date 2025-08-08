<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    private $service;
    private $folderId;

    public function __construct()
    {
        $this->folderId = config('services.google.drive_folder_id');
        $this->initializeGoogleDrive();
    }

    /**
     * تهيئة Google Drive API
     */
    private function initializeGoogleDrive()
    {
        try {
            // التحقق من وجود ملف الاعتماديات
            $credentialsPath = config('services.google.credentials_path', storage_path('app/google-credentials.json'));
            
            Log::info('محاولة تهيئة Google Drive', [
                'credentials_path' => $credentialsPath,
                'folder_id' => $this->folderId
            ]);
            
            if (!file_exists($credentialsPath)) {
                throw new \Exception('ملف اعتماديات Google Drive غير موجود: ' . $credentialsPath);
            }
            
            $client = new Google_Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope(Google_Service_Drive::DRIVE);
            
            // حل مشاكل الاتصال في بيئة التطوير المحلية
            if (app()->environment('local')) {
                $client->setHttpClient(new \GuzzleHttp\Client([
                    'verify' => false, // تجاهل التحقق من شهادة SSL
                    'timeout' => 60, // زيادة مهلة الاتصال
                    'connect_timeout' => 30, // مهلة الاتصال الأولي
                    'http_errors' => false, // عدم رمي أخطاء HTTP
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CONNECTTIMEOUT => 30,
                        CURLOPT_TIMEOUT => 60
                    ]
                ]));
            }
            
            $this->service = new Google_Service_Drive($client);
            
            // اختبار الاتصال
            $this->testConnection();
            
            Log::info('تم تهيئة Google Drive بنجاح');
            
        } catch (\Exception $e) {
            Log::error('خطأ في تهيئة Google Drive', [
                'error' => $e->getMessage(),
                'credentials_path' => $credentialsPath ?? 'غير محدد',
                'folder_id' => $this->folderId ?? 'غير محدد'
            ]);
            throw new \Exception('فشل في الاتصال بـ Google Drive: ' . $e->getMessage());
        }
    }

    /**
     * اختبار الاتصال بـ Google Drive
     */
    private function testConnection()
    {
        try {
            // محاولة الحصول على معلومات المجلد
            if ($this->folderId) {
                $this->service->files->get($this->folderId);
            }
        } catch (\Exception $e) {
            // في بيئة التطوير المحلية، تجاهل أخطاء الاتصال
            if (app()->environment('local')) {
                Log::warning('تحذير: فشل في الاتصال بـ Google Drive في بيئة التطوير المحلية', [
                    'error' => $e->getMessage(),
                    'folder_id' => $this->folderId
                ]);
                // لا نرمي استثناء في بيئة التطوير المحلية
                return;
            }
            throw new \Exception('لا يمكن الوصول إلى مجلد Google Drive: ' . $e->getMessage());
        }
    }

    /**
     * رفع ملف إلى Google Drive
     */
    public function uploadFile($localPath, $fileName, $mimeType = null)
    {
        try {
            if (!Storage::exists($localPath)) {
                throw new \Exception("الملف غير موجود: {$localPath}");
            }

            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $fileName,
                'parents' => [$this->folderId]
            ]);

            $content = Storage::get($localPath);
            
            $file = $this->service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id,name,size,webViewLink'
            ]);

            Log::info('تم رفع الملف إلى Google Drive', [
                'file_id' => $file->getId(),
                'file_name' => $file->getName(),
                'local_path' => $localPath
            ]);

            return [
                'file_id' => $file->getId(),
                'file_name' => $file->getName(),
                'file_size' => $file->getSize(),
                'web_view_link' => $file->getWebViewLink(),
                'drive_path' => "https://drive.google.com/file/d/{$file->getId()}/view"
            ];

        } catch (\Exception $e) {
            Log::error('خطأ في رفع الملف إلى Google Drive', [
                'local_path' => $localPath,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * تحميل ملف من Google Drive
     */
    public function downloadFile($fileId, $localPath)
    {
        try {
            $response = $this->service->files->get($fileId, [
                'alt' => 'media'
            ]);

            $content = $response->getBody()->getContents();
            Storage::put($localPath, $content);

            Log::info('تم تحميل الملف من Google Drive', [
                'file_id' => $fileId,
                'local_path' => $localPath
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('خطأ في تحميل الملف من Google Drive', [
                'file_id' => $fileId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * حذف ملف من Google Drive
     */
    public function deleteFile($fileId)
    {
        try {
            $this->service->files->delete($fileId);

            Log::info('تم حذف الملف من Google Drive', [
                'file_id' => $fileId
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('خطأ في حذف الملف من Google Drive', [
                'file_id' => $fileId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * الحصول على معلومات الملف
     */
    public function getFileInfo($fileId)
    {
        try {
            $file = $this->service->files->get($fileId, [
                'fields' => 'id,name,size,createdTime,modifiedTime,webViewLink'
            ]);

            return [
                'file_id' => $file->getId(),
                'file_name' => $file->getName(),
                'file_size' => $file->getSize(),
                'created_time' => $file->getCreatedTime(),
                'modified_time' => $file->getModifiedTime(),
                'web_view_link' => $file->getWebViewLink()
            ];

        } catch (\Exception $e) {
            Log::error('خطأ في الحصول على معلومات الملف', [
                'file_id' => $fileId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * إنشاء نسخة احتياطية في Google Drive
     */
    public function createBackup($document, $backupType = 'automatic')
    {
        try {
            $fileName = "backup_{$document->document_id}_{$backupType}_" . now()->format('Y-m-d_H-i-s') . ".{$document->file_type}";
            
            $result = $this->uploadFile(
                $document->file_path,
                $fileName,
                $document->mime_type
            );

            Log::info('تم إنشاء نسخة احتياطية في Google Drive', [
                'document_id' => $document->id,
                'backup_file_id' => $result['file_id'],
                'backup_type' => $backupType
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('خطأ في إنشاء نسخة احتياطية في Google Drive', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * استعادة نسخة احتياطية من Google Drive
     */
    public function restoreBackup($backupFileId, $document)
    {
        try {
            $restorePath = 'documents/restored/' . $document->document_id . '_restored_' . time() . '.' . $document->file_type;
            
            $this->downloadFile($backupFileId, $restorePath);

            Log::info('تم استعادة نسخة احتياطية من Google Drive', [
                'document_id' => $document->id,
                'backup_file_id' => $backupFileId,
                'restore_path' => $restorePath
            ]);

            return $restorePath;

        } catch (\Exception $e) {
            Log::error('خطأ في استعادة نسخة احتياطية من Google Drive', [
                'document_id' => $document->id,
                'backup_file_id' => $backupFileId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * الحصول على قائمة الملفات في مجلد النسخ الاحتياطية
     */
    public function listBackupFiles()
    {
        try {
            $files = $this->service->files->listFiles([
                'q' => "'{$this->folderId}' in parents and trashed=false",
                'fields' => 'files(id,name,size,createdTime,modifiedTime)',
                'orderBy' => 'createdTime desc'
            ]);

            return $files->getFiles();

        } catch (\Exception $e) {
            Log::error('خطأ في الحصول على قائمة الملفات', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
} 