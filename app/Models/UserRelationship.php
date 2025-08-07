<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'related_user_id',
        'relationship_type',
        'status',
        'start_date',
        'end_date',
        'notes',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // علاقة مع المستخدم الرئيسي
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة مع المستخدم المرتبط
    public function relatedUser()
    {
        return $this->belongsTo(User::class, 'related_user_id');
    }

    // التحقق من أن العلاقة نشطة
    public function isActive()
    {
        return $this->status === 'active' && 
               (!$this->end_date || $this->end_date->isFuture());
    }

    // الحصول على جميع العلاقات النشطة للمستخدم
    public static function getActiveRelationships($userId)
    {
        return self::where('user_id', $userId)
                   ->where('status', 'active')
                   ->where(function($query) {
                       $query->whereNull('end_date')
                             ->orWhere('end_date', '>', now());
                   })
                   ->with('relatedUser')
                   ->get();
    }

    // الحصول على المشرفين المباشرين
    public static function getSupervisors($userId)
    {
        return self::where('related_user_id', $userId)
                   ->where('relationship_type', 'supervisor')
                   ->where('status', 'active')
                   ->with('user')
                   ->get();
    }

    // الحصول على المرؤوسين المباشرين
    public static function getSubordinates($userId)
    {
        return self::where('user_id', $userId)
                   ->where('relationship_type', 'subordinate')
                   ->where('status', 'active')
                   ->with('relatedUser')
                   ->get();
    }

    // الحصول على الزملاء
    public static function getColleagues($userId)
    {
        return self::where('user_id', $userId)
                   ->where('relationship_type', 'colleague')
                   ->where('status', 'active')
                   ->with('relatedUser')
                   ->get();
    }
}
