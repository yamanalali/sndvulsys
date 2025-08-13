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
     * Show questionnaire-based evaluation form with guidance answers
     */
    public function questionnaire(int $volunteerRequestId)
    {
        $volunteerRequest = VolunteerRequest::with(['user', 'skills', 'availabilities', 'previousExperiences'])
            ->findOrFail($volunteerRequestId);

        $questions = $this->getQuestionnaireDefinition();

        // Build volunteer answers mapped to questions
        $answers = [
            'identity_validation' => trim(collect([
                'الاسم: ' . ($volunteerRequest->full_name ?? 'غير متوفر'),
                'الهوية: ' . ($volunteerRequest->national_id ?? 'غير متوفر'),
                'البريد: ' . ($volunteerRequest->email ?? 'غير متوفر'),
                'الهاتف: ' . ($volunteerRequest->phone ?? 'غير متوفر'),
            ])->implode(' | ')),
            'motivation_truthfulness' => $volunteerRequest->motivation ?? 'غير متوفر',
            'skills_relevance' => $volunteerRequest->skills ?? 'غير متوفر',
            'experience_authenticity' => $volunteerRequest->previous_experience ?? 'غير متوفر',
            'availability_fit' => $volunteerRequest->availability ?? 'غير متوفر',
            'communication' => 'N/A',
            'teamwork' => 'N/A',
            'reliability' => 'N/A',
            'adaptability' => 'N/A',
            'leadership' => 'N/A',
            'technical' => $volunteerRequest->skills ?? 'غير متوفر',
            'cultural_fit' => $volunteerRequest->preferred_area ?? 'غير متوفر',
            'commitment' => ($volunteerRequest->has_previous_volunteering ? 'سبق له التطوع' : 'لم يسبق له') . ' | التوفر: ' . ($volunteerRequest->availability ?? 'غير متوفر'),
        ];

        $fullMark = array_sum(array_map(fn($q) => (int)$q['max'], $questions));

        return view('volunteer-evaluations.questionnaire', compact('volunteerRequest', 'questions', 'answers', 'fullMark'));
    }

    /**
     * Store questionnaire-based evaluation
     */
    public function storeQuestionnaire(Request $request, int $volunteerRequestId)
    {
        $questions = $this->getQuestionnaireDefinition();

        // Validate dynamic question scores
        $rules = [
            'evaluation_date' => 'required|date',
            'notes' => 'nullable|string'
        ];
        foreach ($questions as $questionKey => $question) {
            $rules["questions.$questionKey.score"] = 'required|numeric|min:0|max:' . ((int) $question['max']);
            $rules["questions.$questionKey.comment"] = 'nullable|string';
        }
        $validated = $request->validate($rules);

        // Compute weighted total percentage 0..100
        $totalWeight = array_sum(array_column($questions, 'weight')) ?: 1;
        $totalPercent = 0.0;
        foreach ($questions as $key => $q) {
            $score = (float)($request->input("questions.$key.score", 0));
            $max = (float)$q['max'];
            $weight = (float)$q['weight'];
            if ($max > 0) {
                $totalPercent += (($score / $max) * 100.0) * ($weight / $totalWeight);
            }
        }
        $totalPercent = round($totalPercent, 2);

        // Map a recommendation based on total percent
        $recommendation = match (true) {
            $totalPercent >= 90 => 'strong_approve',
            $totalPercent >= 75 => 'approve',
            $totalPercent >= 60 => 'conditional',
            $totalPercent >= 40 => 'reject',
            default => 'strong_reject',
        };

        try {
            DB::beginTransaction();

            // Create main evaluation; use total percent for detailed fields as a pragmatic default
            $evaluation = VolunteerEvaluation::create([
                'volunteer-request_id' => $volunteerRequestId,
                'evaluator_id' => auth()->id() ?? 1,
                'evaluation_date' => $request->evaluation_date,
                'notes' => $request->notes,
                'recommendation' => $recommendation,
                'interview_score' => $totalPercent,
                'skills_assessment_score' => $totalPercent,
                'motivation_score' => $totalPercent,
                'availability_score' => $totalPercent,
                'experience_score' => $totalPercent,
                'communication_score' => $totalPercent,
                'teamwork_score' => $totalPercent,
                'reliability_score' => $totalPercent,
                'adaptability_score' => $totalPercent,
                'leadership_score' => $totalPercent,
                'technical_skills_score' => $totalPercent,
                'cultural_fit_score' => $totalPercent,
                'commitment_score' => $totalPercent,
                'status' => 'completed',
            ]);

            // Save per-question criteria rows for traceability
            foreach ($questions as $key => $q) {
                VolunteerEvaluationCriteria::create([
                    'evaluation_id' => $evaluation->id,
                    'criteria_name' => $key,
                    'criteria_description' => $q['text'],
                    'score' => (float)$request->input("questions.$key.score", 0),
                    'max_score' => (float)$q['max'],
                    'weight' => (float)$q['weight'],
                    'comments' => $request->input("questions.$key.comment"),
                    'evaluated_at' => now(),
                ]);
            }

            // Calculate overall score based on detailed fields
            $overallScore = $evaluation->calculateOverallScore();
            $evaluation->update(['overall_score' => $overallScore]);

            // Update volunteer request status
            $volunteerRequest = VolunteerRequest::find($volunteerRequestId);
            $newStatus = $this->getStatusFromRecommendation($recommendation);
            $volunteerRequest->update([
                'status' => $newStatus,
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id() ?? 1,
                'admin_notes' => $request->notes
            ]);

            DB::commit();

            return redirect()->route('volunteer-evaluations.show', $evaluation->id)
                ->with('success', 'تم حفظ تقييم الاستبيان بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حفظ تقييم الاستبيان: ' . $e->getMessage());
        }
    }

    /**
     * Definition of questionnaire questions with guidance answers
     * Each item: key => [text, guidance, max, weight]
     */
    private function getQuestionnaireDefinition(): array
    {
        return [
            'identity_validation' => [
                'text' => 'هل معلومات الهوية والسجل الشخصي متسقة مع ما قُدِّم في الطلب؟',
                'guidance' => 'يجب تطابق الاسم، رقم الهوية، وتواريخ الميلاد والاتصال مع المستندات.',
                'max' => 10,
                'weight' => 10,
            ],
            'motivation_truthfulness' => [
                'text' => 'هل دافع المتطوع منطقي ومتسق عبر المقابلة والنموذج؟',
                'guidance' => 'أجب بنعم إذا كانت الأمثلة محددة وغير عامة، ومتسقة مع الخبرة.',
                'max' => 10,
                'weight' => 10,
            ],
            'skills_relevance' => [
                'text' => 'مدى ملاءمة المهارات للمهام المطلوبة في المنظمة.',
                'guidance' => 'يُفضّل وجود أمثلة عملية وشهادات أو مشاريع تدعم الادعاء.',
                'max' => 10,
                'weight' => 12,
            ],
            'experience_authenticity' => [
                'text' => 'هل الخبرات السابقة قابلة للتحقق ومتسقة؟',
                'guidance' => 'ابحث عن مراجع، تواريخ منطقية، وتوافق مع المهارات.',
                'max' => 10,
                'weight' => 10,
            ],
            'availability_fit' => [
                'text' => 'هل التوفر الزمني مناسب لاحتياجات المنظمة؟',
                'guidance' => 'تحقق من القدرة على الالتزام بالأوقات الحرجة للبرنامج.',
                'max' => 10,
                'weight' => 8,
            ],
            'communication' => [
                'text' => 'تقييم وضوح التواصل والصدق أثناء المقابلة.',
                'guidance' => 'إجابات مباشرة، أمثلة واضحة، عدم تناقض.',
                'max' => 10,
                'weight' => 10,
            ],
            'teamwork' => [
                'text' => 'القدرة على العمل ضمن فريق.',
                'guidance' => 'مواقف سابقة توضّح التعاون وحل النزاعات.',
                'max' => 10,
                'weight' => 8,
            ],
            'reliability' => [
                'text' => 'موثوقية الالتزام بالمواعيد والتعليمات.',
                'guidance' => 'سجل التزام سابق أو توصيات.',
                'max' => 10,
                'weight' => 8,
            ],
            'adaptability' => [
                'text' => 'القدرة على التكيف مع تغييرات مفاجئة.',
                'guidance' => 'أمثلة على تغيير خطة/دور والتعامل معه بنجاح.',
                'max' => 10,
                'weight' => 6,
            ],
            'leadership' => [
                'text' => 'مبادرة وقيادة عند الحاجة.',
                'guidance' => 'مواقف قيادة سابقة أو مبادرات تطوعية.',
                'max' => 10,
                'weight' => 6,
            ],
            'technical' => [
                'text' => 'ملاءمة المهارات التقنية للأدوار المقترحة.',
                'guidance' => 'اختبارات قصيرة أو نماذج عمل إن توفرت.',
                'max' => 10,
                'weight' => 6,
            ],
            'cultural_fit' => [
                'text' => 'الملاءمة مع قيم وثقافة المنظمة.',
                'guidance' => 'احترام السياسات، روح الخدمة، الحساسية الثقافية.',
                'max' => 10,
                'weight' => 6,
            ],
            'commitment' => [
                'text' => 'درجة الالتزام والاستمرارية المتوقعة.',
                'guidance' => 'وضوح المدة، تحمّل المسؤوليات، الحضور.',
                'max' => 10,
                'weight' => 10,
            ],
        ];
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
            'recommendation' => 'required|in:strong_approve,approve,conditional,reject,strong_reject,accepted,training_required,rejected',
            'interview_score' => 'required|numeric|min:0|max:10',
            'skills_assessment_score' => 'required|numeric|min:0|max:10',
            'motivation_score' => 'required|numeric|min:0|max:10',
            'availability_score' => 'required|numeric|min:0|max:10',
            'experience_score' => 'required|numeric|min:0|max:10',
            'communication_score' => 'required|numeric|min:0|max:10',
            'teamwork_score' => 'required|numeric|min:0|max:10',
            'reliability_score' => 'required|numeric|min:0|max:10',
            'adaptability_score' => 'required|numeric|min:0|max:10',
            'leadership_score' => 'required|numeric|min:0|max:10',
            'technical_skills_score' => 'required|numeric|min:0|max:10',
            'cultural_fit_score' => 'required|numeric|min:0|max:10',
            'commitment_score' => 'required|numeric|min:0|max:10',

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
            'recommendation' => 'required|in:strong_approve,approve,conditional,reject,strong_reject,accepted,training_required,rejected',
            'interview_score' => 'required|numeric|min:0|max:10',
            'skills_assessment_score' => 'required|numeric|min:0|max:10',
            'motivation_score' => 'required|numeric|min:0|max:10',
            'availability_score' => 'required|numeric|min:0|max:10',
            'experience_score' => 'required|numeric|min:0|max:10',
            'communication_score' => 'required|numeric|min:0|max:10',
            'teamwork_score' => 'required|numeric|min:0|max:10',
            'reliability_score' => 'required|numeric|min:0|max:10',
            'adaptability_score' => 'required|numeric|min:0|max:10',
            'leadership_score' => 'required|numeric|min:0|max:10',
            'technical_skills_score' => 'required|numeric|min:0|max:10',
            'cultural_fit_score' => 'required|numeric|min:0|max:10',
            'commitment_score' => 'required|numeric|min:0|max:10',

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
            'strong_approve', 'approve', 'accepted' => 'approved',
            'conditional', 'training_required' => 'pending',
            'reject', 'strong_reject', 'rejected' => 'rejected',
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