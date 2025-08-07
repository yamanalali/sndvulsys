<?php

namespace App\Http\Controllers;

use App\Models\AdvancedSearch;
use App\Models\VolunteerRequest;
use App\Models\Submission;
use App\Models\Workflow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AdvancedSearchController extends Controller
{
    /**
     * عرض صفحة البحث المتقدم
     */
    public function index()
    {
        $searchTypes = AdvancedSearch::getSearchTypes();
        $sortOptions = AdvancedSearch::getSortOptions();
        $filterOptions = AdvancedSearch::getFilterOptions();
        $savedSearches = AdvancedSearch::getSavedSearches();
        $popularSearches = AdvancedSearch::getPopularSearches(5);
        $statistics = $this->getSearchStatistics();

        return view('advanced-search.index', compact(
            'searchTypes',
            'sortOptions',
            'filterOptions',
            'savedSearches',
            'popularSearches',
            'statistics'
        ));
    }

    /**
     * تنفيذ البحث المتقدم
     */
    public function search(Request $request)
    {
        $startTime = microtime(true);

        $validator = Validator::make($request->all(), [
            'search_term' => 'nullable|string|max:255',
            'search_type' => 'required|in:' . implode(',', array_keys(AdvancedSearch::getSearchTypes())),
            'filters' => 'nullable|array',
            'sort_options' => 'nullable|array',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // بناء استعلام البحث
            $query = $this->buildSearchQuery($request);
            
            // تطبيق الترتيب
            $query = $this->applySorting($query, $request->sort_options);
            
            // الحصول على النتائج
            $perPage = $request->per_page ?? 15;
            $results = $query->paginate($perPage);
            
            // حساب وقت التنفيذ
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            // حفظ البحث
            $searchRecord = $this->saveSearchRecord($request, $results->total(), $executionTime);
            
            // إضافة النتائج للاستجابة
            $response = [
                'success' => true,
                'results' => $results,
                'search_id' => $searchRecord->id,
                'execution_time' => $executionTime,
                'total_results' => $results->total(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'per_page' => $results->perPage()
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('خطأ في البحث المتقدم: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء البحث'], 500);
        }
    }

    /**
     * حفظ البحث
     */
    public function saveSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_id' => 'required|exists:advanced_search,id',
            'name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $search = AdvancedSearch::findOrFail($request->search_id);
        
        // التحقق من ملكية البحث
        if ($search->user_id !== Auth::id()) {
            return response()->json(['error' => 'غير مصرح لك بحفظ هذا البحث'], 403);
        }

        $search->saveSearch($request->name, $request->notes);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ البحث بنجاح',
            'search' => $search
        ]);
    }

    /**
     * إلغاء حفظ البحث
     */
    public function unsaveSearch($id)
    {
        $search = AdvancedSearch::findOrFail($id);
        
        // التحقق من ملكية البحث
        if ($search->user_id !== Auth::id()) {
            return response()->json(['error' => 'غير مصرح لك بإلغاء حفظ هذا البحث'], 403);
        }

        $search->unsaveSearch();

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء حفظ البحث بنجاح'
        ]);
    }

    /**
     * مشاركة البحث
     */
    public function shareSearch($id)
    {
        $search = AdvancedSearch::findOrFail($id);
        
        if (!$search->canBeShared()) {
            return response()->json(['error' => 'لا يمكن مشاركة هذا البحث'], 403);
        }

        $search->makePublic();

        return response()->json([
            'success' => true,
            'message' => 'تم مشاركة البحث بنجاح',
            'share_url' => $search->share_url
        ]);
    }

    /**
     * عرض البحث المحفوظ
     */
    public function showSavedSearch($id)
    {
        $search = AdvancedSearch::findOrFail($id);
        
        // التحقق من الصلاحية
        if ($search->user_id !== Auth::id() && !$search->is_public) {
            abort(403, 'غير مصرح لك بعرض هذا البحث');
        }

        $searchTypes = AdvancedSearch::getSearchTypes();
        $sortOptions = AdvancedSearch::getSortOptions();
        $filterOptions = AdvancedSearch::getFilterOptions();

        return view('advanced-search.show', compact(
            'search',
            'searchTypes',
            'sortOptions',
            'filterOptions'
        ));
    }

    /**
     * حذف البحث
     */
    public function destroy($id)
    {
        $search = AdvancedSearch::findOrFail($id);
        
        // التحقق من ملكية البحث
        if ($search->user_id !== Auth::id()) {
            return response()->json(['error' => 'غير مصرح لك بحذف هذا البحث'], 403);
        }

        $search->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف البحث بنجاح'
        ]);
    }

    /**
     * الحصول على البحوث المحفوظة
     */
    public function getSavedSearches()
    {
        $savedSearches = AdvancedSearch::getSavedSearches();
        
        return response()->json([
            'success' => true,
            'searches' => $savedSearches
        ]);
    }

    /**
     * الحصول على البحوث الشائعة
     */
    public function getPopularSearches()
    {
        $popularSearches = AdvancedSearch::getPopularSearches(10);
        
        return response()->json([
            'success' => true,
            'searches' => $popularSearches
        ]);
    }

    /**
     * الحصول على إحصائيات البحث
     */
    public function getStatistics()
    {
        $statistics = $this->getSearchStatistics();
        
        return response()->json([
            'success' => true,
            'statistics' => $statistics
        ]);
    }

    /**
     * تصدير نتائج البحث
     */
    public function exportResults(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_id' => 'required|exists:advanced_search,id',
            'format' => 'required|in:csv,excel,pdf'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $search = AdvancedSearch::findOrFail($request->search_id);
        
        // التحقق من الصلاحية
        if ($search->user_id !== Auth::id() && !$search->is_public) {
            return response()->json(['error' => 'غير مصرح لك بتصدير هذا البحث'], 403);
        }

        // إعادة تنفيذ البحث للحصول على النتائج الحالية
        $results = $this->reExecuteSearch($search);
        
        // تصدير النتائج
        $filename = 'search_results_' . $search->id . '_' . now()->format('Y-m-d_H-i-s');
        
        switch ($request->format) {
            case 'csv':
                return $this->exportToCsv($results, $filename);
            case 'excel':
                return $this->exportToExcel($results, $filename);
            case 'pdf':
                return $this->exportToPdf($results, $filename);
        }
    }

    /**
     * بناء استعلام البحث
     */
    private function buildSearchQuery(Request $request)
    {
        $searchType = $request->search_type;
        $searchTerm = $request->search_term;
        $filters = $request->filters ?? [];

        switch ($searchType) {
            case 'volunteer-requests':
                return $this->buildVolunteerRequestsQuery($searchTerm, $filters);
            case 'submissions':
                return $this->buildSubmissionsQuery($searchTerm, $filters);
            case 'workflows':
                return $this->buildWorkflowsQuery($searchTerm, $filters);
            case 'users':
                return $this->buildUsersQuery($searchTerm, $filters);
            case 'tasks':
                return $this->buildTasksQuery($searchTerm, $filters);
            case 'projects':
                return $this->buildProjectsQuery($searchTerm, $filters);
            default:
                throw new \Exception('نوع البحث غير مدعوم');
        }
    }

    /**
     * بناء استعلام طلبات التطوع
     */
    private function buildVolunteerRequestsQuery($searchTerm, $filters)
    {
        $query = VolunteerRequest::query();

        // تطبيق مصطلح البحث
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('full_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%")
                  ->orWhere('address', 'like', "%{$searchTerm}%");
            });
        }

        // تطبيق المرشحات
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['date_range'])) {
            $query = $this->applyDateRangeFilter($query, $filters['date_range']);
        }

        return $query;
    }

    /**
     * بناء استعلام الإرسالات
     */
    private function buildSubmissionsQuery($searchTerm, $filters)
    {
        $query = Submission::with(['volunteerRequest', 'reviewer', 'assignedTo']);

        // تطبيق مصطلح البحث
        if ($searchTerm) {
            $query->whereHas('volunteerRequest', function($q) use ($searchTerm) {
                $q->where('full_name', 'like', "%{$searchTerm}%");
            });
        }

        // تطبيق المرشحات
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        return $query;
    }

    /**
     * بناء استعلام سير المراجعة
     */
    private function buildWorkflowsQuery($searchTerm, $filters)
    {
        $query = Workflow::with(['volunteerRequest', 'reviewer', 'assignedTo']);

        // تطبيق مصطلح البحث
        if ($searchTerm) {
            $query->whereHas('volunteerRequest', function($q) use ($searchTerm) {
                $q->where('full_name', 'like', "%{$searchTerm}%");
            });
        }

        // تطبيق المرشحات
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['step'])) {
            $query->where('step', $filters['step']);
        }

        return $query;
    }

    /**
     * بناء استعلام المستخدمين
     */
    private function buildUsersQuery($searchTerm, $filters)
    {
        $query = User::query();

        // تطبيق مصطلح البحث
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // تطبيق المرشحات
        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        return $query;
    }

    /**
     * بناء استعلام المهام
     */
    private function buildTasksQuery($searchTerm, $filters)
    {
        $query = \App\Models\Task::query();

        // تطبيق مصطلح البحث
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // تطبيق المرشحات
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        return $query;
    }

    /**
     * بناء استعلام المشاريع
     */
    private function buildProjectsQuery($searchTerm, $filters)
    {
        $query = \App\Models\Project::query();

        // تطبيق مصطلح البحث
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // تطبيق المرشحات
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * تطبيق مرشح النطاق الزمني
     */
    private function applyDateRangeFilter($query, $dateRange)
    {
        switch ($dateRange) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', today()->subDay());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'last_month':
                $query->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
                break;
        }

        return $query;
    }

    /**
     * تطبيق الترتيب
     */
    private function applySorting($query, $sortOptions)
    {
        if (!$sortOptions) {
            return $query->orderBy('created_at', 'desc');
        }

        foreach ($sortOptions as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        return $query;
    }

    /**
     * حفظ سجل البحث
     */
    private function saveSearchRecord(Request $request, $totalResults, $executionTime)
    {
        return AdvancedSearch::create([
            'search_term' => $request->search_term,
            'search_type' => $request->search_type,
            'filters' => $request->filters,
            'sort_options' => $request->sort_options,
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'ip_address' => $request->ip(),
            'total_results' => $totalResults,
            'executed_at' => now(),
            'execution_time_ms' => $executionTime
        ]);
    }

    /**
     * إعادة تنفيذ البحث
     */
    private function reExecuteSearch(AdvancedSearch $search)
    {
        $request = new Request([
            'search_term' => $search->search_term,
            'search_type' => $search->search_type,
            'filters' => $search->filters,
            'sort_options' => $search->sort_options
        ]);

        $query = $this->buildSearchQuery($request);
        $query = $this->applySorting($query, $search->sort_options);

        return $query->get();
    }

    /**
     * الحصول على إحصائيات البحث
     */
    private function getSearchStatistics()
    {
        $model = new AdvancedSearch();
        return $model->getSearchStatistics();
    }

    /**
     * تصدير إلى CSV
     */
    private function exportToCsv($results, $filename)
    {
        // تنفيذ التصدير إلى CSV
        return response()->json(['message' => 'سيتم إضافة التصدير إلى CSV لاحقاً']);
    }

    /**
     * تصدير إلى Excel
     */
    private function exportToExcel($results, $filename)
    {
        // تنفيذ التصدير إلى Excel
        return response()->json(['message' => 'سيتم إضافة التصدير إلى Excel لاحقاً']);
    }

    /**
     * تصدير إلى PDF
     */
    private function exportToPdf($results, $filename)
    {
        // تنفيذ التصدير إلى PDF
        return response()->json(['message' => 'سيتم إضافة التصدير إلى PDF لاحقاً']);
    }
} 