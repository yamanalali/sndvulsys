<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AdvancedSearch extends Model
{
    use HasFactory;

    protected $table = 'advanced_search';

    protected $fillable = [
        'search_term',
        'search_type',
        'filters',
        'sort_options',
        'user_id',
        'session_id',
        'ip_address',
        'total_results',
        'search_results',
        'executed_at',
        'execution_time_ms',
        'is_saved',
        'saved_name',
        'notes',
        'is_public',
        'sharing_options'
    ];

    protected $casts = [
        'filters' => 'array',
        'sort_options' => 'array',
        'search_results' => 'array',
        'sharing_options' => 'array',
        'executed_at' => 'datetime',
        'is_saved' => 'boolean',
        'is_public' => 'boolean'
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * نطاق للبحوث المحفوظة
     */
    public function scopeSaved($query)
    {
        return $query->where('is_saved', true);
    }

    /**
     * نطاق للبحوث العامة
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * نطاق حسب نوع البحث
     */
    public function scopeByType($query, $type)
    {
        return $query->where('search_type', $type);
    }

    /**
     * نطاق حسب المستخدم
     */
    public function scopeByUser($query, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        return $query->where('user_id', $userId);
    }

    /**
     * نطاق للبحوث الحديثة
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('executed_at', '>=', now()->subDays($days));
    }

    /**
     * الحصول على أنواع البحث المتاحة
     */
    public static function getSearchTypes()
    {
        return [
            'volunteer-requests' => 'طلبات التطوع',
            'submissions' => 'الإرسالات',
            'workflows' => 'سير المراجعة',
            'cases' => 'الحالات',
            'users' => 'المستخدمين',
            'tasks' => 'المهام',
            'projects' => 'المشاريع'
        ];
    }

    /**
     * الحصول على خيارات الترتيب
     */
    public static function getSortOptions()
    {
        return [
            'created_at_desc' => 'الأحدث أولاً',
            'created_at_asc' => 'الأقدم أولاً',
            'updated_at_desc' => 'آخر تحديث',
            'name_asc' => 'الاسم (أ-ي)',
            'name_desc' => 'الاسم (ي-أ)',
            'status_asc' => 'الحالة (أ-ي)',
            'priority_desc' => 'الأولوية (عاجلة أولاً)',
            'priority_asc' => 'الأولوية (منخفضة أولاً)'
        ];
    }

    /**
     * الحصول على خيارات المرشحات
     */
    public static function getFilterOptions()
    {
        return [
            'status' => [
                'pending' => 'معلق',
                'in_review' => 'قيد المراجعة',
                'approved' => 'موافق عليه',
                'rejected' => 'مرفوض',
                'completed' => 'مكتمل'
            ],
            'priority' => [
                'low' => 'منخفضة',
                'medium' => 'متوسطة',
                'high' => 'عالية',
                'urgent' => 'عاجلة'
            ],
            'date_range' => [
                'today' => 'اليوم',
                'yesterday' => 'أمس',
                'this_week' => 'هذا الأسبوع',
                'this_month' => 'هذا الشهر',
                'last_month' => 'الشهر الماضي',
                'custom' => 'مخصص'
            ]
        ];
    }

    /**
     * حفظ البحث
     */
    public function saveSearch($name, $notes = null)
    {
        $this->update([
            'is_saved' => true,
            'saved_name' => $name,
            'notes' => $notes
        ]);
    }

    /**
     * إلغاء حفظ البحث
     */
    public function unsaveSearch()
    {
        $this->update([
            'is_saved' => false,
            'saved_name' => null
        ]);
    }

    /**
     * جعل البحث عام
     */
    public function makePublic()
    {
        $this->update(['is_public' => true]);
    }

    /**
     * جعل البحث خاص
     */
    public function makePrivate()
    {
        $this->update(['is_public' => false]);
    }

    /**
     * الحصول على وقت التنفيذ بصيغة مقروءة
     */
    public function getExecutionTimeTextAttribute()
    {
        if (!$this->execution_time_ms) {
            return 'غير محدد';
        }

        if ($this->execution_time_ms < 1000) {
            return $this->execution_time_ms . ' مللي ثانية';
        }

        return round($this->execution_time_ms / 1000, 2) . ' ثانية';
    }

    /**
     * الحصول على حجم النتائج بصيغة مقروءة
     */
    public function getResultsCountTextAttribute()
    {
        if ($this->total_results == 0) {
            return 'لا توجد نتائج';
        }

        if ($this->total_results == 1) {
            return 'نتيجة واحدة';
        }

        if ($this->total_results < 10) {
            return $this->total_results . ' نتائج';
        }

        return number_format($this->total_results) . ' نتيجة';
    }

    /**
     * التحقق من إمكانية المشاركة
     */
    public function canBeShared()
    {
        return $this->is_public || $this->user_id === Auth::id();
    }

    /**
     * الحصول على رابط المشاركة
     */
    public function getShareUrlAttribute()
    {
        if (!$this->canBeShared()) {
            return null;
        }

        return route('advanced-search.share', $this->id);
    }

    /**
     * الحصول على إحصائيات البحث
     */
    public function getSearchStatistics()
    {
        return [
            'total_searches' => self::count(),
            'saved_searches' => self::saved()->count(),
            'public_searches' => self::public()->count(),
            'recent_searches' => self::recent()->count(),
            'avg_execution_time' => self::whereNotNull('execution_time_ms')->avg('execution_time_ms'),
            'total_results_found' => self::sum('total_results')
        ];
    }

    /**
     * الحصول على البحوث الشائعة
     */
    public static function getPopularSearches($limit = 10)
    {
        return self::select('search_term', 'search_type')
            ->selectRaw('COUNT(*) as usage_count')
            ->groupBy('search_term', 'search_type')
            ->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * الحصول على البحوث المحفوظة للمستخدم
     */
    public static function getSavedSearches($userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        return self::where('user_id', $userId)
            ->where('is_saved', true)
            ->orderBy('updated_at', 'desc')
            ->get();
    }
} 