<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'join_date',
        'last_login',
        'phone_number',
        'status',
        'role_name',
        'email',
        'role_name',
        'avatar',
        'position',
        'department',
        'password',
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $getUser = self::orderBy('user_id', 'desc')->first();

            if ($getUser) {
                $latestID = intval(substr($getUser->user_id, 4));
                $nextID = $latestID + 1;
            } else {
                $nextID = 1;
            }
            $model->user_id = 'KH_' . sprintf("%04s", $nextID);
            while (self::where('user_id', $model->user_id)->exists()) {
                $nextID++;
                $model->user_id = 'KH_' . sprintf("%04s", $nextID);
            }
        });
    }

    public function assignments()
    {
        return $this->hasMany(\App\Models\Assignment::class, 'user_id');
    }
    public function tasksThroughAssignments()
    {
        return $this->hasManyThrough(\App\Models\Task::class, \App\Models\Assignment::class, 'user_id', 'id', 'id', 'task_id');
    }

    // علاقة مع المستندات المملوكة
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // علاقة مع صلاحيات المستندات
    public function documentPermissions()
    {
        return $this->hasMany(DocumentPermission::class);
    }

    // علاقة مع العلاقات التي يكون فيها المستخدم هو الطرف الرئيسي
    public function relationships()
    {
        return $this->hasMany(UserRelationship::class);
    }

    // علاقة مع العلاقات التي يكون فيها المستخدم هو الطرف المرتبط
    public function relatedRelationships()
    {
        return $this->hasMany(UserRelationship::class, 'related_user_id');
    }

    // الحصول على جميع المستندات التي يمكن للمستخدم الوصول إليها
    public function accessibleDocuments()
    {
        return Document::where(function($query) {
            $query->where('user_id', $this->id)
                  ->orWhereHas('permissions', function($q) {
                      $q->where('user_id', $this->id)
                        ->where(function($subQ) {
                            $subQ->whereNull('expires_at')
                                 ->orWhere('expires_at', '>', now());
                        });
                  });
        });
    }

    // الحصول على المشرفين المباشرين
    public function supervisors()
    {
        return $this->hasManyThrough(
            User::class,
            UserRelationship::class,
            'related_user_id',
            'id',
            'id',
            'user_id'
        )->where('relationship_type', 'supervisor')
         ->where('status', 'active');
    }

    // الحصول على المرؤوسين المباشرين
    public function subordinates()
    {
        return $this->hasManyThrough(
            User::class,
            UserRelationship::class,
            'user_id',
            'id',
            'id',
            'related_user_id'
        )->where('relationship_type', 'subordinate')
         ->where('status', 'active');
    }

    // الحصول على الزملاء
    public function colleagues()
    {
        return $this->hasManyThrough(
            User::class,
            UserRelationship::class,
            'user_id',
            'id',
            'id',
            'related_user_id'
        )->where('relationship_type', 'colleague')
         ->where('status', 'active');
    }

    // التحقق من صلاحية على مستند معين
    public function hasDocumentPermission($documentId, $permissionType)
    {
        return DocumentPermission::hasUserPermission($this->id, $documentId, $permissionType);
    }

    // منح صلاحية لمستند
    public function grantDocumentPermission($documentId, $permissionType, $expiresAt = null)
    {
        return DocumentPermission::grantPermission($this->id, $documentId, $permissionType, 'direct', $expiresAt);
    }
}
