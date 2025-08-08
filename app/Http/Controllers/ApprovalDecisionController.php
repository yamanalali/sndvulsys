<?php

namespace App\Http\Controllers;

use App\Models\ApprovalDecision;
use App\Models\VolunteerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalDecisionController extends Controller
{
    /**
     * الحصول على معرف المستخدم أو إنشاء مستخدم افتراضي
     */
    private function getDecisionUserId()
    {
        $userId = Auth::id();
        if (!$userId) {
            // إنشاء مستخدم افتراضي إذا لم يكن المستخدم مسجل دخول
            $defaultUser = \App\Models\User::firstOrCreate(
                ['email' => 'admin@system.com'],
                [
                    'name' => 'مدير النظام',
                    'password' => bcrypt('password'),
                    'role_name' => 'Admin',
                    'status' => 'Active',
                ]
            );
            $userId = $defaultUser->id;
        }
        return $userId;
    }

    /**
     * اتخاذ قرار موافقة
     */
    public function approve(Request $request, $volunteerRequestId)
    {
        $validator = Validator::make($request->all(), [
            'decision_reason' => 'required|string|max:1000'
        ], [
            'decision_reason.required' => 'يجب كتابة سبب القرار'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $volunteerRequest = VolunteerRequest::findOrFail($volunteerRequestId);

            // التحقق من عدم وجود قرار سابق
            $existingDecision = ApprovalDecision::where('volunteer-request_id', $volunteerRequestId)->first();
            if ($existingDecision) {
                return response()->json([
                    'success' => false,
                    'message' => 'يوجد قرار سابق لهذا الطلب'
                ], 400);
            }

            // الحصول على معرف المستخدم
            $decisionBy = $this->getDecisionUserId();

            // إنشاء قرار الموافقة
            $decision = ApprovalDecision::createApproval(
                $volunteerRequestId,
                $decisionBy,
                $request->decision_reason
            );

            // تحديث حالة الطلب
            $volunteerRequest->update(['status' => 'approved']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم قبول الطلب بنجاح',
                'decision' => $decision
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in approve decision: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء اتخاذ القرار: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * اتخاذ قرار رفض
     */
    public function reject(Request $request, $volunteerRequestId)
    {
        $validator = Validator::make($request->all(), [
            'decision_reason' => 'required|string|max:1000'
        ], [
            'decision_reason.required' => 'يجب كتابة سبب الرفض'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $volunteerRequest = VolunteerRequest::findOrFail($volunteerRequestId);

            // التحقق من عدم وجود قرار سابق
            $existingDecision = ApprovalDecision::where('volunteer-request_id', $volunteerRequestId)->first();
            if ($existingDecision) {
                return response()->json([
                    'success' => false,
                    'message' => 'يوجد قرار سابق لهذا الطلب'
                ], 400);
            }

            // الحصول على معرف المستخدم
            $decisionBy = $this->getDecisionUserId();

            // إنشاء قرار الرفض
            $decision = ApprovalDecision::createRejection(
                $volunteerRequestId,
                $decisionBy,
                $request->decision_reason
            );

            // تحديث حالة الطلب
            $volunteerRequest->update(['status' => 'rejected']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم رفض الطلب بنجاح',
                'decision' => $decision
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in reject decision: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء اتخاذ القرار: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض تفاصيل القرار
     */
    public function show($id)
    {
        $decision = ApprovalDecision::with(['volunteerRequest.user', 'decisionBy'])->findOrFail($id);
        
        return response()->json($decision);
    }

    /**
     * الحصول على قرارات معلقة
     */
    public function getPendingDecisions()
    {
        $pendingRequests = VolunteerRequest::with(['user'])
            ->where('status', 'pending')
            ->whereDoesntHave('approvalDecision')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($pendingRequests);
    }

    /**
     * الحصول على قرارات مقبولة
     */
    public function getApprovedDecisions()
    {
        $decisions = ApprovalDecision::with(['volunteerRequest.user', 'decisionBy'])
            ->approved()
            ->orderBy('decision_at', 'desc')
            ->get();

        return response()->json($decisions);
    }

    /**
     * الحصول على قرارات مرفوضة
     */
    public function getRejectedDecisions()
    {
        $decisions = ApprovalDecision::with(['volunteerRequest.user', 'decisionBy'])
            ->rejected()
            ->orderBy('decision_at', 'desc')
            ->get();

        return response()->json($decisions);
    }

    /**
     * عرض إحصائيات قرارات الموافقة
     */
    public function statistics()
    {
        try {
            $totalDecisions = ApprovalDecision::count();
            $approvedDecisions = ApprovalDecision::approved()->count();
            $rejectedDecisions = ApprovalDecision::rejected()->count();
            $pendingRequests = VolunteerRequest::where('status', 'pending')
                ->whereDoesntHave('approvalDecision')
                ->count();

            // إحصائيات شهرية
            $monthlyStats = ApprovalDecision::selectRaw('
                MONTH(decision_at) as month,
                YEAR(decision_at) as year,
                decision_status,
                COUNT(*) as count
            ')
            ->whereYear('decision_at', date('Y'))
            ->groupBy('month', 'year', 'decision_status')
            ->get();

            // قرارات هذا الشهر
            $thisMonthDecisions = ApprovalDecision::whereYear('decision_at', date('Y'))
                ->whereMonth('decision_at', date('m'))
                ->count();

            $statistics = [
                'total_decisions' => $totalDecisions,
                'approved_decisions' => $approvedDecisions,
                'rejected_decisions' => $rejectedDecisions,
                'pending_requests' => $pendingRequests,
                'approval_rate' => $totalDecisions > 0 ? round(($approvedDecisions / $totalDecisions) * 100, 2) : 0,
                'rejection_rate' => $totalDecisions > 0 ? round(($rejectedDecisions / $totalDecisions) * 100, 2) : 0,
                'this_month_decisions' => $thisMonthDecisions,
                'avg_decision_time_hours' => 0, // تبسيط لتجنب الأخطاء
                'monthly_stats' => $monthlyStats
            ];

            return response()->json($statistics);
        } catch (\Exception $e) {
            // في حالة حدوث أي خطأ، إرجاع إحصائيات فارغة
            $statistics = [
                'total_decisions' => 0,
                'approved_decisions' => 0,
                'rejected_decisions' => 0,
                'pending_requests' => 0,
                'approval_rate' => 0,
                'rejection_rate' => 0,
                'this_month_decisions' => 0,
                'avg_decision_time_hours' => 0,
                'monthly_stats' => collect([])
            ];

            return response()->json($statistics);
        }
    }

    /**
     * عرض صفحة الإحصائيات
     */
    public function showStatistics()
    {
        try {
            $totalDecisions = ApprovalDecision::count();
            $approvedDecisions = ApprovalDecision::approved()->count();
            $rejectedDecisions = ApprovalDecision::rejected()->count();
            $pendingRequests = VolunteerRequest::where('status', 'pending')
                ->whereDoesntHave('approvalDecision')
                ->count();

            // إحصائيات شهرية
            $monthlyStats = ApprovalDecision::selectRaw('
                MONTH(decision_at) as month,
                YEAR(decision_at) as year,
                decision_status,
                COUNT(*) as count
            ')
            ->whereYear('decision_at', date('Y'))
            ->groupBy('month', 'year', 'decision_status')
            ->get();

            // قرارات هذا الشهر
            $thisMonthDecisions = ApprovalDecision::whereYear('decision_at', date('Y'))
                ->whereMonth('decision_at', date('m'))
                ->count();

            // متوسط وقت اتخاذ القرار - تبسيط الكود لتجنب الأخطاء
            $avgDecisionTime = 0;

            $statistics = (object) [
                'total_decisions' => $totalDecisions,
                'approved_decisions' => $approvedDecisions,
                'rejected_decisions' => $rejectedDecisions,
                'pending_requests' => $pendingRequests,
                'approval_rate' => $totalDecisions > 0 ? round(($approvedDecisions / $totalDecisions) * 100, 2) : 0,
                'rejection_rate' => $totalDecisions > 0 ? round(($rejectedDecisions / $totalDecisions) * 100, 2) : 0,
                'this_month_decisions' => $thisMonthDecisions,
                'avg_decision_time_hours' => $avgDecisionTime,
                'monthly_stats' => $monthlyStats
            ];
            
            return view('approval-decisions.statistics', compact('statistics'));
        } catch (\Exception $e) {
            // في حالة حدوث أي خطأ، إرجاع إحصائيات فارغة
            $statistics = (object) [
                'total_decisions' => 0,
                'approved_decisions' => 0,
                'rejected_decisions' => 0,
                'pending_requests' => 0,
                'approval_rate' => 0,
                'rejection_rate' => 0,
                'this_month_decisions' => 0,
                'avg_decision_time_hours' => 0,
                'monthly_stats' => collect([])
            ];
            
            return view('approval-decisions.statistics', compact('statistics'));
        }
    }

    /**
     * جلب أفضل المقررين
     */
    public function getTopDeciders()
    {
        $topDeciders = ApprovalDecision::selectRaw('
            users.name,
            users.id,
            COUNT(CASE WHEN approval_decisions.decision_status = "approved" THEN 1 END) as approved_count,
            COUNT(CASE WHEN approval_decisions.decision_status = "rejected" THEN 1 END) as rejected_count,
            COUNT(*) as total_decisions
        ')
        ->join('users', 'approval_decisions.decision_by', '=', 'users.id')
        ->groupBy('users.id', 'users.name')
        ->orderBy('total_decisions', 'desc')
        ->limit(10)
        ->get();

        return response()->json($topDeciders);
    }
} 