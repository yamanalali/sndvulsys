<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
   
    public function dependencies()
{
    return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'depends_on_task_id');
}
    
     public function dependents()
{
    return $this->belongsToMany(Task::class, 'task_dependencies', 'depends_on_task_id', 'task_id');
}
}
