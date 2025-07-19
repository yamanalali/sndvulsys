<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'uuid',
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

    /**
     * Get the documents for the user.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the skills for the user.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'skill_user')
                    ->withPivot(['skill_level', 'experience_years', 'notes', 'is_verified', 'verified_at'])
                    ->withTimestamps();
    }

    /**
     * Get the skills owned by the user.
     */
    public function ownedSkills(): HasMany
    {
        return $this->hasMany(Skill::class, 'user_id');
    }

    /**
     * Get the skills verified by the user.
     */
    public function verifiedSkills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'skill_user', 'verified_by', 'skill_id')
                    ->withTimestamps();
    }

    /**
     * Check if user has a specific skill
     */
    public function hasSkill($skillId)
    {
        return $this->skills()->where('skill_id', $skillId)->exists();
    }

    /**
     * Get user's skill level for a specific skill
     */
    public function getSkillLevel($skillId)
    {
        $skill = $this->skills()->where('skill_id', $skillId)->first();
        return $skill ? $skill->pivot->skill_level : null;
    }

    /**
     * Get user's experience years for a specific skill
     */
    public function getExperienceYears($skillId)
    {
        $skill = $this->skills()->where('skill_id', $skillId)->first();
        return $skill ? $skill->pivot->experience_years : null;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    /**
     * Get user's verified skills count
     */
    public function getVerifiedSkillsCountAttribute()
    {
        return $this->skills()->wherePivot('is_verified', true)->count();
    }

    /**
     * Get user's total skills count
     */
    public function getTotalSkillsCountAttribute()
    {
        return $this->skills()->count();
    }
}
