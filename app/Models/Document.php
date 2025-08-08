<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'hash',
        'status',
        'privacy_level',
        'metadata',
        'expires_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        // إنشاء معرف فريد للمستند
        self::creating(function ($model) {
            $model->document_id = 'DOC_' . Str::random(8) . '_' . time();
        });
        
        // حذف الملف عند حذف المستند
        self::deleting(function ($model) {
            if (Storage::exists($model->file_path)) {
                Storage::delete($model->file_path);
            }
        });
    }

    // علاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة مع النسخ الاحتياطية
    public function backups()
    {
        return $this->hasMany(DocumentBackup::class);
    }

    // علاقة مع الصلاحيات
    public function permissions()
    {
        return $this->hasMany(DocumentPermission::class);
    }

    // الحصول على URL الملف
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    // التحقق من انتهاء الصلاحية
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // التحقق من إمكانية الوصول للمستخدم
    public function canAccess($user)
    {
        // التحقق من وجود المستخدم
        if (!$user) {
            return false;
        }
        
        // المالك يمكنه الوصول دائماً
        if ($this->user_id === $user->id) {
            return true;
        }

        // المستندات العامة يمكن للجميع الوصول إليها
        if ($this->privacy_level === 'public') {
            return true;
        }

        // التحقق من الصلاحيات المباشرة
        $permission = $this->permissions()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        return $permission !== null;
    }
    
    // التحقق من أن المستند نشط
    public function isActive()
    {
        return $this->status === 'active';
    }

    // إنشاء نسخة احتياطية محلية
    public function createBackup($type = 'automatic', $notes = null)
    {
        try {
            // التأكد من وجود مجلد النسخ الاحتياطية
            $backupDir = 'backups/' . date('Y/m/d/');
            if (!Storage::exists($backupDir)) {
                Storage::makeDirectory($backupDir);
            }
            
            $backupPath = $backupDir . $this->document_id . '_' . time() . '.' . $this->file_type;
            
            // التحقق من وجود الملف الأصلي
            if (!Storage::exists($this->file_path)) {
                \Log::error('الملف الأصلي غير موجود', [
                    'document_id' => $this->id,
                    'file_path' => $this->file_path
                ]);
                return null;
            }
            
            // نسخ الملف
            if (Storage::copy($this->file_path, $backupPath)) {
                // التحقق من أن النسخة تم إنشاؤها بنجاح
                if (Storage::exists($backupPath)) {
                    $backup = $this->backups()->create([
                        'backup_path' => $backupPath,
                        'backup_hash' => hash_file('sha256', Storage::path($backupPath)),
                        'backup_size' => Storage::size($backupPath),
                        'backup_type' => $type,
                        'backup_notes' => $notes,
                        'backup_date' => now(),
                    ]);
                    
                    \Log::info('تم إنشاء نسخة احتياطية بنجاح', [
                        'document_id' => $this->id,
                        'backup_id' => $backup->id,
                        'backup_path' => $backupPath
                    ]);
                    
                    return $backup;
                }
            }
            
            \Log::error('فشل في نسخ الملف', [
                'document_id' => $this->id,
                'source_path' => $this->file_path,
                'backup_path' => $backupPath
            ]);
            
            return null;
        } catch (\Exception $e) {
            \Log::error('خطأ في إنشاء النسخة الاحتياطية', [
                'document_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    // إنشاء نسخة احتياطية في Google Drive
    public function createDriveBackup($type = 'automatic', $notes = null)
    {
        return DocumentBackup::createDriveBackup($this, $type, $notes);
    }

    // إنشاء نسخة احتياطية (محلية أو في Google Drive حسب الإعدادات)
    public function createBackupWithStorage($type = 'automatic', $notes = null, $useDrive = true)
    {
        if ($useDrive) {
            return $this->createDriveBackup($type, $notes);
        } else {
            return $this->createBackup($type, $notes);
        }
    }

    // الحصول على النسخ الاحتياطية في Google Drive
    public function getDriveBackups()
    {
        try {
            // محاولة الحصول من قاعدة البيانات أولاً
            $dbBackups = $this->backups()->whereNotNull('drive_file_id')->get();
            
            // محاولة الحصول من Google Drive مباشرة
            $googleDrive = new GoogleDriveService();
            $driveFiles = $googleDrive->listBackupFiles();
            
            Log::info('تم جلب النسخ الاحتياطية من Google Drive', [
                'document_id' => $this->id,
                'db_count' => $dbBackups->count(),
                'drive_count' => count($driveFiles)
            ]);
            
            return $dbBackups;
        } catch (\Exception $e) {
            Log::error('خطأ في جلب النسخ الاحتياطية من Google Drive', [
                'document_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    // الحصول على النسخ الاحتياطية المحلية
    public function getLocalBackups()
    {
        return $this->backups()->whereNull('drive_file_id')->get();
    }

    // الحصول على حجم الملف بصيغة مقروءة
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // الحصول على حالة المستند بالعربية
    public function getStatusTextAttribute()
    {
        $statuses = [
            'active' => 'نشط',
            'archived' => 'مؤرشف',
            'deleted' => 'محذوف'
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    // الحصول على مستوى الخصوصية بالعربية
    public function getPrivacyLevelTextAttribute()
    {
        $levels = [
            'public' => 'عام',
            'private' => 'خاص',
            'restricted' => 'مقيد'
        ];
        
        return $levels[$this->privacy_level] ?? $this->privacy_level;
    }
}
