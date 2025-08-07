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

    /**
     * علاقة مع إعدادات الإشعارات
     */
    public function notificationSettings()
    {
        return $this->hasOne(NotificationSetting::class);
    }

    /**
     * الحصول على إعدادات الإشعارات أو إنشاء إعدادات افتراضية
     */
    public function getNotificationSettings()
    {
        return $this->notificationSettings()->firstOrCreate([], [
            'assignment_notifications' => true,
            'assignment_email' => true,
            'assignment_database' => true,
            'status_update_notifications' => true,
            'status_update_email' => true,
            'status_update_database' => true,
            'deadline_reminder_notifications' => true,
            'deadline_reminder_email' => true,
            'deadline_reminder_database' => true,
            'deadline_reminder_days' => 1,
            'dependency_notifications' => true,
            'dependency_email' => true,
            'dependency_database' => true,
            'email_notifications' => true,
            'database_notifications' => true,
            'browser_notifications' => false,
        ]);
    }
}
