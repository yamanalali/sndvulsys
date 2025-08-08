<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionAttachment extends Model
{
    use HasFactory;

    protected $table = 'submissionattachments';

    protected $fillable = [
        'submission_id',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'description'
    ];

    /**
     * العلاقة مع الإرسال
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    /**
     * الحصول على حجم الملف بصيغة مقروءة
     */
    public function getFileSizeTextAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * الحصول على رابط تحميل الملف
     */
    public function getDownloadUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * التحقق من نوع الملف
     */
    public function isImage()
    {
        return in_array($this->file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    /**
     * التحقق من كون الملف PDF
     */
    public function isPdf()
    {
        return $this->file_type === 'application/pdf';
    }

    /**
     * التحقق من كون الملف مستند
     */
    public function isDocument()
    {
        return in_array($this->file_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }
} 