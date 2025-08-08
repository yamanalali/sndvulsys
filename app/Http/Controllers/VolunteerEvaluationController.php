<?php

namespace App\Http\Controllers;

use App\Models\VolunteerRequest;
use App\Models\VolunteerEvaluation;
use App\Models\VolunteerEvaluationCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VolunteerEvaluationController extends Controller
{
    /**
     * Display a listing of evaluations
     */
    public function index()
    {
        // جلب التقييمات الموجودة
        $evaluations = VolunteerEvaluation::with(['volunteerRequest', 'evaluator'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // جلب طلبات التطوع التي لم يتم تقييمها بعد
        $unevaluatedRequests = VolunteerRequest::whereDoesntHave('evaluations')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('volunteer-evaluations.index', compact('evaluations', 'unevaluatedRequests'));
    }

    /**
     * Show the form for creating a new evaluation
     */
    public function create($volunteerRequestId)
    {
        try {
            $volunteerRequest = VolunteerRequest::with(['user', 'skills', 'availabilities', 'previousExperiences'])
                ->findOrFail($volunteerRequestId);
                
            $criteria = VolunteerEvaluationCriteria::getAvailableCriteria();
            
            return view('volunteer-evaluations.create', compact('volunteerRequest', 'criteria'));
        } catch (\Exception $e) {
            return redirect()->route('volunteer-requests.index')
                ->with('error', 'حدث خطأ أثناء تحميل صفحة التقييم: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created evaluation
     */
    public function store(Request $request, $volunteerRequestId)
    {
        // إضافة debugging
        \Log::info('Evaluation store method called', [
            'volunteerRequestId' => $volunteerRequestId,
            'request_data' => $request->all()
        ]);

        $request->validate([
            'evaluation_date' => 'required|date',
            'notes' => 'nullable|string',
            'recommendation' => 'required|in:strong_approve,approve,conditional,reject,strong_reject',
            'interview_score' => 'required|numeric|min:0|max:100',
            'skills_assessment_score' => 'required|numeric|min:0|max:100',
            'motivation_score' => 'required|numeric|min:0|max:100',
            'availability_score' => 'required|numeric|min:0|max:100',
            'experience_score' => 'required|numeric|min:0|max:100',
            'communication_score' => 'required|numeric|min:0|max:100',
            'teamwork_score' => 'required|numeric|min:0|max:100',
            'reliability_score' => 'required|numeric|min:0|max:100',
            'adaptability_score' => 'required|numeric|min:0|max:100',
            'leadership_score' => 'required|numeric|min:0|max:100',
            'technical_skills_score' => 'required|numeric|min:0|max:100',
            'cultural_fit_score' => 'required|numeric|min:0|max:100',
            'commitment_score' => 'required|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Create evaluation
            $evaluation = VolunteerEvaluation::create([
                'volunteer-request_id' => $volunteerRequestId,
                'evaluator_id' => auth()->id() ?? 1, // استخدام ID افتراضي إذا لم يكن المستخدم مسجل دخول
                'evaluation_date' => $request->evaluation_date,
                'notes' => $request->notes,
                'recommendation' => $request->recommendation,
                'interview_score' => $request->interview_score,
                'skills_assessment_score' => $request->skills_assessment_score,
                'motivation_score' => $request->motivation_score,
                'availability_score' => $request->availability_score,
                'experience_score' => $request->experience_score,
                'communication_score' => $request->communication_score,
                'teamwork_score' => $request->teamwork_score,
                'reliability_score' => $request->reliability_score,
                'adaptability_score' => $request->adaptability_score,
                'leadership_score' => $request->leadership_score,
                'technical_skills_score' => $request->technical_skills_score,
                'cultural_fit_score' => $request->cultural_fit_score,
                'commitment_score' => $request->commitment_score,
                'status' => 'completed',
            ]);

            // Calculate overall score
            $overallScore = $evaluation->calculateOverallScore();
            $evaluation->update(['overall_score' => $overallScore]);

            // لا نحتاج لإنشاء criteria evaluations منفصلة - البيانات محفوظة في الجدول الرئيسي

            // Update volunteer request status based on recommendation
            $volunteerRequest = VolunteerRequest::find($volunteerRequestId);
            $newStatus = $this->getStatusFromRecommendation($request->recommendation);
            $volunteerRequest->update([
                'status' => $newStatus,
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id() ?? 1,
                'admin_notes' => $request->notes
            ]);

            DB::commit();

            \Log::info('Evaluation saved successfully', [
                'evaluation_id' => $evaluation->id,
                'volunteer-request_id' => $volunteerRequestId
            ]);

            return redirect()->route('volunteer-evaluations.show', $evaluation->id)
                ->with('success', 'تم حفظ التقييم بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Evaluation save failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'volunteerRequestId' => $volunteerRequestId
            ]);
            return back()->with('error', 'حدث خطأ أثناء حفظ التقييم: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified evaluation
     */
    public function show($id)
    {
        $evaluation = VolunteerEvaluation::with([
            'volunteerRequest.user',
            'volunteerRequest.skills',
            'volunteerRequest.availabilities',
            'volunteerRequest.previousExperiences',
            'evaluator',
            'criteriaEvaluations'
        ])->findOrFail($id);

        return view('volunteer-evaluations.show', compact('evaluation'));
    }

    /**
     * Show the form for editing the specified evaluation
     */
    public function edit($id)
    {
        $evaluation = VolunteerEvaluation::with([
            'volunteerRequest',
            'criteriaEvaluations'
        ])->findOrFail($id);

        $criteria = VolunteerEvaluationCriteria::getAvailableCriteria();

        return view('volunteer-evaluations.edit', compact('evaluation', 'criteria'));
    }

    /**
     * Update the specified evaluation
     */
    public function update(Request $request, $id)
    {
        $evaluation = VolunteerEvaluation::findOrFail($id);

        $request->validate([
            'evaluation_date' => 'required|date',
            'notes' => 'nullable|string',
            'recommendation' => 'required|in:strong_approve,approve,conditional,reject,strong_reject',
            'interview_score' => 'required|numeric|min:0|max:100',
            'skills_assessment_score' => 'required|numeric|min:0|max:100',
            'motivation_score' => 'required|numeric|min:0|max:100',
            'availability_score' => 'required|numeric|min:0|max:100',
            'experience_score' => 'required|numeric|min:0|max:100',
            'communication_score' => 'required|numeric|min:0|max:100',
            'teamwork_score' => 'required|numeric|min:0|max:100',
            'reliability_score' => 'required|numeric|min:0|max:100',
            'adaptability_score' => 'required|numeric|min:0|max:100',
            'leadership_score' => 'required|numeric|min:0|max:100',
            'technical_skills_score' => 'required|numeric|min:0|max:100',
            'cultural_fit_score' => 'required|numeric|min:0|max:100',
            'commitment_score' => 'required|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Update evaluation
            $evaluation->update([
                'evaluation_date' => $request->evaluation_date,
                'notes' => $request->notes,
                'recommendation' => $request->recommendation,
                'interview_score' => $request->interview_score,
                'skills_assessment_score' => $request->skills_assessment_score,
                'motivation_score' => $request->motivation_score,
                'availability_score' => $request->availability_score,
                'experience_score' => $request->experience_score,
                'communication_score' => $request->communication_score,
                'teamwork_score' => $request->teamwork_score,
                'reliability_score' => $request->reliability_score,
                'adaptability_score' => $request->adaptability_score,
                'leadership_score' => $request->leadership_score,
                'technical_skills_score' => $request->technical_skills_score,
                'cultural_fit_score' => $request->cultural_fit_score,
                'commitment_score' => $request->commitment_score,
            ]);

            // Recalculate overall score
            $overallScore = $evaluation->calculateOverallScore();
            $evaluation->update(['overall_score' => $overallScore]);

            // Update criteria evaluations
            $criteria = VolunteerEvaluationCriteria::getAvailableCriteria();
            foreach ($criteria as $criteriaName => $criteriaData) {
                $scoreField = $criteriaName . '_score';
                if ($request->has($scoreField)) {
                    $criteriaEvaluation = $evaluation->criteriaEvaluations()
                        ->where('criteria_name', $criteriaName)
                        ->first();

                    if ($criteriaEvaluation) {
                        $criteriaEvaluation->update([
                            'score' => $request->$scoreField,
                            'evaluated_at' => now(),
                        ]);
                    } else {
                        VolunteerEvaluationCriteria::create([
                            'evaluation_id' => $evaluation->id,
                            'criteria_name' => $criteriaName,
                            'criteria_description' => $criteriaData['description'],
                            'score' => $request->$scoreField,
                            'max_score' => $criteriaData['max_score'],
                            'weight' => $criteriaData['weight'],
                            'evaluated_at' => now(),
                        ]);
                    }
                }
            }

            // Update volunteer request status
            $volunteerRequest = $evaluation->volunteerRequest;
            $newStatus = $this->getStatusFromRecommendation($request->recommendation);
            $volunteerRequest->update([
                'status' => $newStatus,
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'admin_notes' => $request->notes
            ]);

            DB::commit();

            return redirect()->route('volunteer-evaluations.show', $evaluation->id)
                ->with('success', 'تم تحديث التقييم بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث التقييم: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified evaluation
     */
    public function destroy($id)
    {
        $evaluation = VolunteerEvaluation::findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            // Delete criteria evaluations
            $evaluation->criteriaEvaluations()->delete();
            
            // Delete evaluation
            $evaluation->delete();
            
            DB::commit();
            
            return redirect()->route('volunteer-evaluations.index')
                ->with('success', 'تم حذف التقييم بنجاح');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف التقييم: ' . $e->getMessage());
        }
    }

    /**
     * Get volunteer request status from recommendation
     */
    private function getStatusFromRecommendation(string $recommendation): string
    {
        return match($recommendation) {
            'strong_approve', 'approve' => 'approved',
            'conditional' => 'pending',
            'reject', 'strong_reject' => 'rejected',
            default => 'pending'
        };
    }

    /**
     * Get evaluation statistics
     */
    public function statistics()
    {
        try {
            $stats = [
                'total_evaluations' => VolunteerEvaluation::count(),
                'pending_evaluations' => VolunteerEvaluation::where('status', 'pending')->count(),
                'completed_evaluations' => VolunteerEvaluation::where('status', 'completed')->count(),
                'approved_evaluations' => VolunteerEvaluation::where('recommendation', 'approve')->orWhere('recommendation', 'strong_approve')->count(),
                'rejected_evaluations' => VolunteerEvaluation::where('recommendation', 'reject')->orWhere('recommendation', 'strong_reject')->count(),
                'average_score' => VolunteerEvaluation::where('overall_score', '>', 0)->avg('overall_score') ?? 0,
            ];
        } catch (\Exception $e) {
            $stats = [
                'total_evaluations' => 0,
                'pending_evaluations' => 0,
                'completed_evaluations' => 0,
                'approved_evaluations' => 0,
                'rejected_evaluations' => 0,
                'average_score' => 0,
            ];
        }

        return view('volunteer-evaluations.statistics', compact('stats'));
    }
} 