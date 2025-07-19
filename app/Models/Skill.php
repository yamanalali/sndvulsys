<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'skill_level',
        'experience_years',
        'certificates',
        'is_public',
        'available_for_volunteering',
        'is_active',
        'is_featured',
        'user_id'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'available_for_volunteering' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // العلاقات
    public function volunteerRequests(): BelongsToMany
    {
        return $this->belongsToMany(VolunteerRequest::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'skill_user');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeAvailableForVolunteering($query)
    {
        return $query->where('available_for_volunteering', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySkillLevel($query, $skillLevel)
    {
        return $query->where('skill_level', $skillLevel);
    }

    public function scopeByExperienceYears($query, $experienceYears)
    {
        return $query->where('experience_years', $experienceYears);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getSkillLevelTextAttribute()
    {
        $levels = [
            'مبتدئ' => 'مبتدئ',
            'متوسط' => 'متوسط',
            'متقدم' => 'متقدم',
            'خبير' => 'خبير'
        ];

        return $levels[$this->skill_level] ?? 'متوسط';
    }

    public function getCategoryTextAttribute()
    {
        $categories = [
            'تقنية' => 'تقنية',
            'تعليمية' => 'تعليمية',
            'طبية' => 'طبية',
            'اجتماعية' => 'اجتماعية',
            'إبداعية' => 'إبداعية',
            'أخرى' => 'أخرى'
        ];

        return $categories[$this->category] ?? 'غير محدد';
    }

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'نشطة' : 'غير نشطة';
    }

    public function getFeaturedTextAttribute()
    {
        return $this->is_featured ? 'مميزة' : 'عادية';
    }

    public function getPublicTextAttribute()
    {
        return $this->is_public ? 'عامة' : 'خاصة';
    }

    public function getAvailableForVolunteeringTextAttribute()
    {
        return $this->available_for_volunteering ? 'متاحة' : 'غير متاحة';
    }

    // Methods
    public function toggleActive()
    {
        $this->update(['is_active' => !$this->is_active]);
        return $this;
    }

    public function toggleFeatured()
    {
        $this->update(['is_featured' => !$this->is_featured]);
        return $this;
    }

    public function togglePublic()
    {
        $this->update(['is_public' => !$this->is_public]);
        return $this;
    }

    public function toggleAvailableForVolunteering()
    {
        $this->update(['available_for_volunteering' => !$this->available_for_volunteering]);
        return $this;
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
        return $this;
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
        return $this;
    }

    public function markAsFeatured()
    {
        $this->update(['is_featured' => true]);
        return $this;
    }

    public function unmarkAsFeatured()
    {
        $this->update(['is_featured' => false]);
        return $this;
    }

    public function makePublic()
    {
        $this->update(['is_public' => true]);
        return $this;
    }

    public function makePrivate()
    {
        $this->update(['is_public' => false]);
        return $this;
    }

    public function makeAvailableForVolunteering()
    {
        $this->update(['available_for_volunteering' => true]);
        return $this;
    }

    public function makeUnavailableForVolunteering()
    {
        $this->update(['available_for_volunteering' => false]);
        return $this;
    }

    // Helper methods
    public function isOwnedBy($user)
    {
        return $this->user_id === $user->id;
    }

    public function canBeEditedBy($user)
    {
        return $this->isOwnedBy($user) || $user->user_type === 'admin';
    }

    public function canBeDeletedBy($user)
    {
        return $this->isOwnedBy($user) || $user->user_type === 'admin';
    }

    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    public function getFormattedUpdatedAtAttribute()
    {
        return $this->updated_at->format('Y-m-d H:i:s');
    }
}


