<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'permission_type',
        'grant_type',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // علاقة مع المستند
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    // علاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // التحقق من أن الصلاحية نشطة وغير منتهية
    public function isActive()
    {
        return !$this->expires_at || $this->expires_at->isFuture();
    }

    // التحقق من صلاحية محددة
    public function hasPermission($permissionType)
    {
        return $this->permission_type === $permissionType && $this->isActive();
    }

    // الحصول على جميع صلاحيات المستخدم على مستند معين
    public static function getUserPermissions($userId, $documentId)
    {
        return self::where('user_id', $userId)
                   ->where('document_id', $documentId)
                   ->where(function($query) {
                       $query->whereNull('expires_at')
                             ->orWhere('expires_at', '>', now());
                   })
                   ->get();
    }

    // التحقق من وجود صلاحية معينة للمستخدم
    public static function hasUserPermission($userId, $documentId, $permissionType)
    {
        return self::where('user_id', $userId)
                   ->where('document_id', $documentId)
                   ->where('permission_type', $permissionType)
                   ->where(function($query) {
                       $query->whereNull('expires_at')
                             ->orWhere('expires_at', '>', now());
                   })
                   ->exists();
    }

    // منح صلاحية جديدة
    public static function grantPermission($userId, $documentId, $permissionType, $grantType = 'direct', $expiresAt = null, $notes = null)
    {
        return self::create([
            'user_id' => $userId,
            'document_id' => $documentId,
            'permission_type' => $permissionType,
            'grant_type' => $grantType,
            'expires_at' => $expiresAt,
            'notes' => $notes,
        ]);
    }

    // إلغاء صلاحية
    public static function revokePermission($userId, $documentId, $permissionType)
    {
        return self::where('user_id', $userId)
                   ->where('document_id', $documentId)
                   ->where('permission_type', $permissionType)
                   ->delete();
    }

    // الحصول على جميع المستخدمين الذين لديهم صلاحيات على مستند معين
    public static function getDocumentUsers($documentId)
    {
        return self::where('document_id', $documentId)
                   ->where(function($query) {
                       $query->whereNull('expires_at')
                             ->orWhere('expires_at', '>', now());
                   })
                   ->with('user')
                   ->get();
    }
}
