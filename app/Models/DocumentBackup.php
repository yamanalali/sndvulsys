<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Services\GoogleDriveService;

class DocumentBackup extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'backup_path',
        'backup_hash',
        'backup_size',
        'backup_type',
        'backup_notes',
        'backup_date',
        'drive_file_id', // معرف الملف في Google Drive
        'drive_web_link', // رابط الويب للملف في Google Drive
    ];

    protected $casts = [
        'backup_date' => 'datetime',
    ];

    // علاقة مع المستند
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    // الحصول على URL النسخة الاحتياطية
    public function getBackupUrlAttribute()
    {
        // إذا كان الملف في Google Drive
        if ($this->drive_file_id) {
            return $this->drive_web_link;
        }
        
        // إذا كان الملف محلي
        return Storage::url($this->backup_path);
    }

    // التحقق من صحة النسخة الاحتياطية
    public function isValid()
    {
        // إذا كان الملف في Google Drive
        if ($this->drive_file_id) {
            try {
                $driveService = new GoogleDriveService();
                $fileInfo = $driveService->getFileInfo($this->drive_file_id);
                return $fileInfo['file_size'] == $this->backup_size;
            } catch (\Exception $e) {
                return false;
            }
        }
        
        // إذا كان الملف محلي
        return Storage::exists($this->backup_path) && 
               hash_file('sha256', Storage::path($this->backup_path)) === $this->backup_hash;
    }

    // استعادة النسخة الاحتياطية
    public function restore()
    {
        try {
            if ($this->drive_file_id) {
                // استعادة من Google Drive
                $driveService = new GoogleDriveService();
                $restorePath = $driveService->restoreBackup($this->drive_file_id, $this->document);
                
                $this->document->update([
                    'file_path' => $restorePath,
                    'file_size' => Storage::size($restorePath),
                    'hash' => hash_file('sha256', Storage::path($restorePath)),
                ]);
            } else {
                // استعادة من التخزين المحلي
                if ($this->isValid() && Storage::exists($this->backup_path)) {
                    $document = $this->document;
                    $newPath = 'documents/' . $document->document_id . '_restored_' . time() . '.' . $document->file_type;
                    
                    if (Storage::copy($this->backup_path, $newPath)) {
                        $document->update([
                            'file_path' => $newPath,
                            'file_size' => Storage::size($newPath),
                            'hash' => hash_file('sha256', Storage::path($newPath)),
                        ]);
                    }
                }
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('خطأ في استعادة النسخة الاحتياطية', [
                'backup_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    // إنشاء نسخة احتياطية في Google Drive
    public static function createDriveBackup($document, $backupType = 'automatic', $notes = null)
    {
        try {
            $driveService = new GoogleDriveService();
            $result = $driveService->createBackup($document, $backupType);
            
            return self::create([
                'document_id' => $document->id,
                'backup_path' => null, // لا يوجد مسار محلي
                'backup_hash' => $document->hash,
                'backup_size' => $result['file_size'],
                'backup_type' => $backupType,
                'backup_notes' => $notes,
                'backup_date' => now(),
                'drive_file_id' => $result['file_id'],
                'drive_web_link' => $result['web_view_link'],
            ]);
            
        } catch (\Exception $e) {
            \Log::error('خطأ في إنشاء نسخة احتياطية في Google Drive', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    // الحصول على نوع التخزين
    public function getStorageTypeAttribute()
    {
        return $this->drive_file_id ? 'google_drive' : 'local';
    }

    // الحصول على حجم الملف بصيغة مقروءة
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->backup_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
