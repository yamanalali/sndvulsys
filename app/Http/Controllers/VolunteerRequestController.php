<?php

namespace App\Http\Controllers;

use App\Models\VolunteerRequest;
use Illuminate\Http\Request;

class VolunteerRequestController extends Controller
{
    public function index() {
        $requests = VolunteerRequest::with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('volunteer-requests.list', compact('requests'));
    }

    public function list() {
        $requests = VolunteerRequest::with(['user', 'approvalDecision.decisionBy'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // إذا لم تكن هناك بيانات، إنشاء بيانات تجريبية
        if ($requests->isEmpty()) {
            // إنشاء بيانات تجريبية
            $this->createSampleData();
            $requests = VolunteerRequest::with(['user', 'approvalDecision.decisionBy'])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('volunteer-requests.list', compact('requests'));
    }

    public function resetData() {
        // مسح جميع البيانات التجريبية
        VolunteerRequest::truncate();
        
        // إنشاء بيانات تجريبية جديدة
        $this->createSampleData();
        
        return redirect()->route('volunteer-requests.list')->with('success', 'تم إعادة تعيين البيانات التجريبية بنجاح');
    }

    public function clearOldData() {
        // مسح البيانات القديمة التي قد تسبب مشاكل
        VolunteerRequest::where('full_name', 'like', '%aya%')
            ->orWhere('full_name', 'like', '%shahd%')
            ->orWhere('full_name', 'like', '%nur%')
            ->orWhere('email', 'like', '%shahdda50%')
            ->delete();
        
        return redirect()->route('volunteer-requests.list')->with('success', 'تم مسح البيانات القديمة بنجاح');
    }

    private function createSampleData() {
        $sampleData = [
            [
                'full_name' => 'أحمد محمد علي - متطوع رقم 1',
                'email' => 'ahmed.volunteer1@example.com',
                'phone' => '0501234567',
                'national_id' => '1234567890',
                'birth_date' => '1990-05-15',
                'gender' => 'male',
                'social_status' => 'single',
                'address' => 'شارع الملك فهد، الرياض',
                'city' => 'الرياض',
                'country' => 'السعودية',
                'education_level' => 'bachelor',
                'field_of_study' => 'علوم الحاسوب',
                'occupation' => 'مبرمج',
                'skills' => 'البرمجة, العمل الجماعي, القيادة',
                'languages' => 'العربية: لغة أم, الإنجليزية: متقدم',
                'motivation' => 'أرغب في المساهمة في خدمة المجتمع وتطوير مهاراتي',
                'previous_experience' => 'عملت متطوعاً في جمعية خيرية لمدة سنتين',
                'preferred_area' => 'التعليم والتكنولوجيا',
                'availability' => 'يومان في الأسبوع، 4 ساعات يومياً',
                'has_previous_volunteering' => true,
                'preferred_organization_type' => 'جمعيات خيرية',
                'status' => 'pending',
                'user_id' => 1
            ],
            [
                'full_name' => 'فاطمة أحمد حسن - متطوعة رقم 2',
                'email' => 'fatima.volunteer2@example.com',
                'phone' => '0509876543',
                'national_id' => '0987654321',
                'birth_date' => '1995-08-20',
                'gender' => 'female',
                'social_status' => 'married',
                'address' => 'شارع التحلية، جدة',
                'city' => 'جدة',
                'country' => 'السعودية',
                'education_level' => 'master',
                'field_of_study' => 'إدارة الأعمال',
                'occupation' => 'مديرة مشاريع',
                'skills' => 'الإدارة, التواصل, التنظيم',
                'languages' => 'العربية: لغة أم, الإنجليزية: ممتاز',
                'motivation' => 'أريد المساهمة في تطوير المجتمع ومساعدة المحتاجين',
                'previous_experience' => 'متطوعة في حملات التوعية الصحية',
                'preferred_area' => 'الصحة والرعاية الاجتماعية',
                'availability' => '3 أيام في الأسبوع، 6 ساعات يومياً',
                'has_previous_volunteering' => true,
                'preferred_organization_type' => 'منظمات صحية',
                'status' => 'pending',
                'user_id' => 1
            ],
            [
                'full_name' => 'محمد عبدالله سالم - متطوع رقم 3',
                'email' => 'mohammed.volunteer3@example.com',
                'phone' => '0505555555',
                'national_id' => '5555555555',
                'birth_date' => '1988-12-10',
                'gender' => 'male',
                'social_status' => 'married',
                'address' => 'شارع العليا، الدمام',
                'city' => 'الدمام',
                'country' => 'السعودية',
                'education_level' => 'phd',
                'field_of_study' => 'الهندسة المدنية',
                'occupation' => 'مهندس',
                'skills' => 'حل المشكلات, التصميم, العمل الجماعي',
                'languages' => 'العربية: لغة أم, الإنجليزية: متقدم',
                'motivation' => 'أرغب في المساهمة في مشاريع البنية التحتية المجتمعية',
                'previous_experience' => 'متطوع في مشاريع البناء المجتمعي',
                'preferred_area' => 'البنية التحتية والتنمية',
                'availability' => 'يوم واحد في الأسبوع، 8 ساعات',
                'has_previous_volunteering' => true,
                'preferred_organization_type' => 'منظمات تنموية',
                'status' => 'pending',
                'user_id' => 1
            ]
        ];

        foreach ($sampleData as $data) {
            VolunteerRequest::create($data);
        }
    }

    public function create() {
        // جلب المهارات لعرضها في النموذج
        $skills = \App\Models\Skill::all();
        return view('volunteer-requests.create', compact('skills'));
    }

    public function store(Request $request) {
        \Log::info('Store method called with data: ' . json_encode($request->all()));
        
        try {
            $data = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'national_id' => 'nullable|string|max:20|unique:volunteer-requests,national_id',
                'birth_date' => 'nullable|date|before:today',
                'gender' => 'nullable|in:male,female',
                'social_status' => 'nullable|in:single,married',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'education_level' => 'nullable|string|max:50',
                'field_of_study' => 'nullable|string|max:200',
                'occupation' => 'nullable|string|max:200',
                'skills' => 'nullable|array',
                'languages' => 'nullable|array',
                'motivation' => 'nullable|string|max:1000',
                'previous_experience' => 'nullable|string|max:1000',
                'preferred_area' => 'nullable|string|max:200',
                'availability' => 'nullable|string|max:200',
                'has_previous_volunteering' => 'nullable|boolean',
                'preferred_organization_type' => 'nullable|string|max:200',
                'cv' => 'nullable|file|mimes:pdf|max:2048',
            ], [
                'full_name.required' => 'الاسم الكامل مطلوب.',
                'full_name.max' => 'الاسم الكامل يجب أن يكون أقل من 255 حرف.',
                'email.required' => 'البريد الإلكتروني مطلوب.',
                'email.email' => 'البريد الإلكتروني يجب أن يكون صحيحاً.',
                'phone.required' => 'رقم الجوال مطلوب.',
                'national_id.unique' => 'رقم الهوية مسجل مسبقاً، يرجى التحقق من البيانات.',
                'birth_date.before' => 'تاريخ الميلاد يجب أن يكون في الماضي.',
                'cv.mimes' => 'يجب أن يكون الملف بصيغة PDF فقط.',
                'cv.max' => 'حجم الملف يجب أن يكون أقل من 2 ميجابايت.',
            ]);
            
            // معالجة رفع السيرة الذاتية
            if ($request->hasFile('cv')) {
                $cvFile = $request->file('cv');
                $cvFileName = time() . '_' . $cvFile->getClientOriginalName();
                $cvPath = $cvFile->storeAs('cvs', $cvFileName, 'public');
                $data['cv'] = $cvPath;
            }
            
            // معالجة user_id - استخدام المستخدم المصادق عليه أو إنشاء مستخدم افتراضي
            if (auth()->check()) {
                $data['user_id'] = auth()->id();
            } else {
                // إنشاء مستخدم افتراضي إذا لم يكن هناك مستخدم مصادق عليه
                $defaultUser = \App\Models\User::firstOrCreate(
                    ['email' => 'volunteer@system.com'],
                    [
                        'name' => 'متطوع النظام',
                        'password' => bcrypt('password'),
                        'role_name' => 'User Normal',
                        'status' => 'Active',
                    ]
                );
                $data['user_id'] = $defaultUser->id;
            }
            
            $data['status'] = 'pending'; // حالة افتراضية
            
            // معالجة has_previous_volunteering
            if (isset($data['has_previous_volunteering'])) {
                $data['has_previous_volunteering'] = ($data['has_previous_volunteering'] == '1' || $data['has_previous_volunteering'] === true) ? true : false;
            } else {
                $data['has_previous_volunteering'] = false;
            }
            
            // معالجة skills و languages
            $selectedSkills = [];
            if (isset($data['skills']) && is_array($data['skills'])) {
                $selectedSkills = $data['skills'];
                $data['skills'] = implode(', ', $data['skills']);
            }
            
            if (isset($data['languages']) && is_array($data['languages'])) {
                $languages = [];
                foreach ($data['languages'] as $lang => $level) {
                    if ($level && $level !== 'null' && $level !== 'none') {
                        $languages[] = $lang . ': ' . $level;
                    }
                }
                $data['languages'] = implode(', ', $languages);
            }
            
            \Log::info('Creating volunteer request with data: ' . json_encode($data));
            
            // تنظيف البيانات قبل الحفظ
            $cleanData = [];
            foreach ($data as $key => $value) {
                if ($value !== null && $value !== '' && $value !== 'null' && $value !== [] && $value !== '{}' && $value !== '[]') {
                    $cleanData[$key] = $value;
                }
            }
            
            \Log::info('Clean data for creation: ' . json_encode($cleanData));
            $volunteerRequest = VolunteerRequest::create($cleanData);
            \Log::info('Volunteer request created successfully with ID: ' . $volunteerRequest->id);
            
            // ربط المهارات المحددة
            if (!empty($selectedSkills)) {
                foreach ($selectedSkills as $skillName) {
                    $skill = \App\Models\Skill::firstOrCreate(
                        ['name' => $skillName],
                        [
                            'description' => 'مهارة ' . $skillName,
                            'category' => 'other',
                            'level' => 'beginner',
                            'is_active' => true
                        ]
                    );
                    
                    // ربط المهارة بطلب التطوع
                    $volunteerRequest->skills()->attach($skill->id, [
                        'level' => 'intermediate',
                        'years_experience' => 1
                    ]);
                }
            }
            
            return redirect()->route('volunteer-requests.index')->with('success', 'تم إرسال الطلب بنجاح! سيتم مراجعته من قبل الإدارة.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . json_encode($e->errors()));
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Error creating volunteer request: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Data: ' . json_encode($request->all()));
            
            // إرجاع رسالة خطأ أكثر وضوحاً
            $errorMessage = 'حدث خطأ أثناء حفظ الطلب. ';
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                $errorMessage .= 'مشكلة في ربط البيانات.';
            } elseif (str_contains($e->getMessage(), 'duplicate entry')) {
                $errorMessage .= 'البيانات موجودة مسبقاً.';
            } elseif (str_contains($e->getMessage(), 'SQLSTATE')) {
                $errorMessage .= 'مشكلة في قاعدة البيانات.';
            } else {
                $errorMessage .= $e->getMessage();
            }
            
            return back()->with('error', $errorMessage)->withInput();
        }
    }

    public function show($id) {
        $request = VolunteerRequest::findOrFail($id);
        return view('volunteer-requests.show', compact('request'));
    }

    public function edit($id) {
        $request = VolunteerRequest::findOrFail($id);
        try {
            $skills = \App\Models\Skill::all();
        } catch (\Exception $e) {
            $skills = collect();
        }
        return view('volunteer-requests.edit', compact('request', 'skills'));
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'national_id' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'social_status' => 'nullable|in:single,married',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'education_level' => 'nullable|string',
            'field_of_study' => 'nullable|string',
            'occupation' => 'nullable|string',
            'skills' => 'nullable|string',
            'languages' => 'nullable|string',
            'motivation' => 'nullable|string',
            'previous_experience' => 'nullable|string',
            'preferred_area' => 'nullable|string',
            'availability' => 'nullable|string',
            'has_previous_volunteering' => 'nullable|boolean',
            'preferred_organization_type' => 'nullable|string',
            'cv' => 'required|file|mimes:pdf|max:2048', // السيرة الذاتية - PDF فقط - حجم أقصى 2MB
        ], [
            'cv.required' => 'السيرة الذاتية مطلوبة.',
            'cv.mimes' => 'يجب أن يكون الملف بصيغة PDF فقط.',
            'cv.max' => 'حجم الملف يجب أن يكون أقل من 2 ميجابايت.',
        ]);

        $volunteerRequest = VolunteerRequest::findOrFail($id);
        
        // معالجة رفع السيرة الذاتية
        if ($request->hasFile('cv')) {
            $cvFile = $request->file('cv');
            $cvFileName = time() . '_' . $cvFile->getClientOriginalName();
            $cvPath = $cvFile->storeAs('cvs', $cvFileName, 'public');
            $data['cv'] = $cvPath;
        }

        $volunteerRequest->update($data);

        return redirect()->route('volunteer-requests.show', $volunteerRequest->id)->with('success', 'تم التعديل بنجاح');
    }

    public function destroy($id) {
        VolunteerRequest::destroy($id);
        return redirect()->route('volunteer-requests.index')->with('success', 'تم الحذف');
    }

    public function updateStatus(Request $request, $id) {
        $request->validate(['status' => 'required|in:pending,approved,rejected,withdrawn']);
        $volunteerRequest = VolunteerRequest::findOrFail($id);
        $volunteerRequest->update(['status' => $request->status]);
        return back()->with('success', 'تم تحديث الحالة');
    }
} 