<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get all active categories
     */
    public static function getActiveCategories()
    {
        return self::where('is_active', true)->get();
    }

    /**
     * Get root categories (no parent)
     */
    public static function getRootCategories()
    {
        return self::whereNull('parent_id')->where('is_active', true)->get();
    }

    /**
     * Get category with all its children
     */
    public function getFullHierarchy()
    {
        return $this->load('children.children');
    }

    /**
     * Get all descendants of this category
     */
    public function getAllDescendants()
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }
        
        return $descendants;
    }

    /**
     * Get all ancestors of this category
     */
    public function getAllAncestors()
    {
        $ancestors = collect();
        $current = $this->parent;
        
        while ($current) {
            $ancestors->push($current);
            $current = $current->parent;
        }
        
        return $ancestors->reverse();
    }

    /**
     * Get full path of category (e.g., "Parent > Child > Grandchild")
     */
    public function getFullPath(): string
    {
        $path = collect([$this->name]);
        $current = $this->parent;
        
        while ($current) {
            $path->prepend($current->name);
            $current = $current->parent;
        }
        
        return $path->implode(' > ');
    }

    /**
     * Check if category has children
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Check if category is a leaf (no children)
     */
    public function isLeaf(): bool
    {
        return !$this->hasChildren();
    }

    /**
     * Get tasks count for this category
     */
    public function getTasksCount(): int
    {
        return $this->tasks()->count();
    }

    /**
     * Get active tasks count for this category
     */
    public function getActiveTasksCount(): int
    {
        return $this->tasks()
                   ->whereNotIn('status', [Task::STATUS_COMPLETED, Task::STATUS_CANCELLED])
                   ->count();
    }

    /**
     * Get completed tasks count for this category
     */
    public function getCompletedTasksCount(): int
    {
        return $this->tasks()->where('status', Task::STATUS_COMPLETED)->count();
    }

    /**
     * Get overdue tasks count for this category
     */
    public function getOverdueTasksCount(): int
    {
        return $this->tasks()->overdue()->count();
    }

    // Relationships
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    // Boot method to generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (!$category->slug) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
