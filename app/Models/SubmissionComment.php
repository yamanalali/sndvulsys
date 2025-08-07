<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionComment extends Model
{
    use HasFactory;

    protected $table = 'submissioncomments';

    protected $fillable = [
        'submission_id',
        'user_id',
        'comment',
        'is_internal'
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    /**
     * العلاقة مع الإرسال
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * نطاق للتعليقات الداخلية
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * نطاق للتعليقات العامة
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }
} 