<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_status_id',
        'user_id',
        'note',
        'is_internal'
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    /**
     * العلاقة مع حالة الحالة
     */
    public function caseStatus(): BelongsTo
    {
        return $this->belongsTo(CaseStatus::class, 'case_status_id');
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * نطاق للملاحظات الداخلية
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * نطاق للملاحظات العامة
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }
} 