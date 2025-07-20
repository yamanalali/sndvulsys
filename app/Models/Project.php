<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'manager_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function teamMembers()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->withTimestamps();
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'on_hold' => 'معلق',
            'cancelled' => 'ملغي',
            default => 'غير محدد',
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-700',
            'completed' => 'bg-blue-100 text-blue-700',
            'on_hold' => 'bg-yellow-100 text-yellow-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getProgressAttribute()
    {
        if ($this->tasks->count() === 0) {
            return 0;
        }

        $completedTasks = $this->tasks->where('status', 'completed')->count();
        return round(($completedTasks / $this->tasks->count()) * 100);
    }
}
