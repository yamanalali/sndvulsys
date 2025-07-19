<?php

namespace App\Http\Controllers;

use App\Models\ApplicationToVolunteer;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ApplicationToVolunteerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['create', 'store']);
    }

    /**
     * Check if user is admin
     */
    private function isAdmin()
    {
        // يمكنك تعديل هذا حسب نظام الأدوار لديك
        // حالياً نفترض أن المستخدم مسؤول إذا كان مسجل دخول
        // يمكنك تغيير هذا ليتناسب مع نظام الأدوار لديك
        return auth()->check();
    }

    /**
     * Check if user can manage applications (admin only)
     */
    private function canManageApplications()
    {
        // يمكنك تعديل هذا حسب نظام الأدوار لديك
        // حالياً نفترض أن أي مستخدم مسجل يمكنه إدارة الطلبات
        // يمكنك تغيير هذا ليتناسب مع نظام الأدوار لديك
        return auth()->check();
    }

    /**
     * Check if user owns the application
     */
    private function ownsApplication($application)
    {
        // يمكنك تعديل هذا حسب كيفية ربط الطلب بالمستخدم
        return $application->user_id === auth()->id() || $this->canManageApplications();
    }

    public function index(Request $request)
    {
        try {
            // إذا كان المستخدم مسؤول، يعرض جميع الطلبات
            if ($this->canManageApplications()) {
                $query = ApplicationToVolunteer::with(['reviewer'])
                    ->orderBy('created_at', 'desc');
            } else {
                // إذا كان مستخدم عادي، يعرض طلباته فقط
                $query = ApplicationToVolunteer::where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc');
            }

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereJsonContains('details->full_name', $search)
                      ->orWhereJsonContains('details->last_name', $search)
                      ->orWhereJsonContains('details->email', $search)
                      ->orWhereJsonContains('details->phone', $search)
                      ->orWhereJsonContains('details->city', $search);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by city
            if ($request->filled('city')) {
                $query->whereJsonContains('details->city', $request->city);
            }

            // Filter by preferred area
            if ($request->filled('preferred_area')) {
                $query->whereJsonContains('details->preferred_area', $request->preferred_area);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Filter by review status
            if ($request->filled('reviewed')) {
                if ($request->reviewed === 'yes') {
                    $query->reviewed();
                } else {
                    $query->unreviewed();
                }
            }

            $applications = $query->paginate(15)->withQueryString();

            // Get statistics
            $stats = [
                'total' => ApplicationToVolunteer::count(),
                'pending' => ApplicationToVolunteer::pending()->count(),
                'approved' => ApplicationToVolunteer::approved()->count(),
                'rejected' => ApplicationToVolunteer::rejected()->count(),
                'withdrawn' => ApplicationToVolunteer::withdrawn()->count(),
                'recent' => ApplicationToVolunteer::recent(7)->count(),
            ];

            // Get unique cities and areas for filters
            $cities = ApplicationToVolunteer::selectRaw('JSON_EXTRACT(details, "$.city") as city')
                ->whereNotNull('details->city')
                ->distinct()
                ->pluck('city')
                ->filter();

            $areas = ApplicationToVolunteer::selectRaw('JSON_EXTRACT(details, "$.preferred_area") as preferred_area')
                ->whereNotNull('details->preferred_area')
                ->distinct()
                ->pluck('preferred_area')
                ->filter();

            return view('applicationtovolunteer.index', compact('applications', 'stats', 'cities', 'areas'));
        } catch (\Exception $e) {
            Log::error('Error in ApplicationToVolunteerController@index: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحميل البيانات');
        }
    }

    public function create()
    {
        try {
            $skills = Skill::orderBy('name')->get();
            $cities = $this->getCitiesList();
            $educationLevels = $this->getEducationLevels();
            $organizationTypes = $this->getOrganizationTypes();
            $preferredAreas = $this->getPreferredAreas();

            return view('applicationtovolunteer.create', compact(
                'skills', 
                'cities', 
                'educationLevels', 
                'organizationTypes', 
                'preferredAreas'
            ));
        } catch (\Exception $e) {
            Log::error('Error in ApplicationToVolunteerController@create: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحميل النموذج');
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $this->validateApplicationData($request);
            
            // Create details array with all fields
            $details = [
                'full_name' => $validatedData['full_name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'national_id' => $validatedData['national_id'] ?? null,
                'birth_date' => $validatedData['birth_date'] ?? null,
                'gender' => $validatedData['gender'] ?? null,
                'address' => $validatedData['address'] ?? null,
                'city' => $validatedData['city'] ?? null,
                'country' => $validatedData['country'] ?? null,
                'education_level' => $validatedData['education_level'] ?? null,
                'occupation' => $validatedData['occupation'] ?? null,
                'skills' => $validatedData['skills'] ?? null,
                'motivation' => $validatedData['motivation'] ?? null,
                'previous_experience' => $validatedData['previous_experience'] ?? null,
                'preferred_area' => $validatedData['preferred_area'] ?? null,
                'availability' => $validatedData['availability'] ?? null,
                'has_previous_volunteering' => $validatedData['has_previous_volunteering'] ?? false,
                'preferred_organization_type' => $validatedData['preferred_organization_type'] ?? null,
                'emergency_contact_name' => $validatedData['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validatedData['emergency_contact_phone'] ?? null,
            ];

            // Create application with UUID and details JSON
            $application = ApplicationToVolunteer::create([
                'details' => $details,
                'status' => 'pending'
            ]);

            DB::commit();

            return redirect()->route('applicationtovolunteer.create')
                ->with('success', 'تم إرسال طلب التطوع بنجاح! سنتواصل معك قريباً.');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in ApplicationToVolunteerController@store: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إرسال الطلب')->withInput();
        }
    }

    public function show($uuid)
    {
        try {
            $application = ApplicationToVolunteer::with(['reviewer'])->where('uuid', $uuid)->firstOrFail();
            
            // التحقق من أن المستخدم يملك الطلب أو مسؤول
            if (!$this->ownsApplication($application)) {
                return back()->with('error', 'ليس لديك صلاحية لعرض هذا الطلب');
            }
            
            return view('applicationtovolunteer.show', compact('application'));
        } catch (\Exception $e) {
            Log::error('Error in ApplicationToVolunteerController@show: ' . $e->getMessage());
            return back()->with('error', 'لم يتم العثور على الطلب');
        }
    }

    /**
     * عرض طلبات المستخدم الخاصة
     */
    public function myApplications(Request $request)
    {
        try {
            $query = ApplicationToVolunteer::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc');

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereJsonContains('details->full_name', $search)
                      ->orWhereJsonContains('details->last_name', $search)
                      ->orWhereJsonContains('details->email', $search)
                      ->orWhereJsonContains('details->phone', $search);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $applications = $query->paginate(10)->withQueryString();

            // Get statistics for user's applications
            $stats = [
                'total' => ApplicationToVolunteer::where('user_id', auth()->id())->count(),
                'pending' => ApplicationToVolunteer::where('user_id', auth()->id())->pending()->count(),
                'approved' => ApplicationToVolunteer::where('user_id', auth()->id())->approved()->count(),
                'rejected' => ApplicationToVolunteer::where('user_id', auth()->id())->rejected()->count(),
                'withdrawn' => ApplicationToVolunteer::where('user_id', auth()->id())->withdrawn()->count(),
                'recent' => ApplicationToVolunteer::where('user_id', auth()->id())->recent(7)->count(),
            ];

            return view('applicationtovolunteer.my-applications', compact('applications', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error in ApplicationToVolunteerController@myApplications: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحميل البيانات');
        }
    }

    public function edit($uuid)
    {
        try {
            $application = ApplicationToVolunteer::where('uuid', $uuid)->firstOrFail();
            
            // التحقق من أن المستخدم يملك الطلب أو مسؤول
            if (!$this->ownsApplication($application)) {
                return back()->with('error', 'ليس لديك صلاحية لتعديل هذا الطلب');
            }
            
            $skills = Skill::orderBy('name')->get();
            $cities = $this->getCitiesList();
            $educationLevels = $this->getEducationLevels();
            $organizationTypes = $this->getOrganizationTypes();
            $preferredAreas = $this->getPreferredAreas();

            return view('applicationtovolunteer.edit', compact(
                'application', 
                'skills', 
                'cities', 
                'educationLevels', 
                'organizationTypes', 
                'preferredAreas'
            ));
        } catch (\Exception $e) {
            Log::error('Error in ApplicationToVolunteerController@edit: ' . $e->getMessage());
            return back()->with('error', 'لم يتم العثور على الطلب');
        }
    }

    public function update(Request $request, $uuid)
    {
        try {
            DB::beginTransaction();

            $application = ApplicationToVolunteer::where('uuid', $uuid)->firstOrFail();
            
            // التحقق من أن المستخدم يملك الطلب أو مسؤول
            if (!$this->ownsApplication($application)) {
                return back()->with('error', 'ليس لديك صلاحية لتعديل هذا الطلب');
            }
            $validatedData = $this->validateApplicationData($request);
            
            // Update details array with all fields
            $details = $application->details;
            $details['full_name'] = $validatedData['full_name'];
            $details['last_name'] = $validatedData['last_name'];
            $details['email'] = $validatedData['email'];
            $details['phone'] = $validatedData['phone'];
            $details['national_id'] = $validatedData['national_id'] ?? null;
            $details['birth_date'] = $validatedData['birth_date'] ?? null;
            $details['gender'] = $validatedData['gender'] ?? null;
            $details['address'] = $validatedData['address'] ?? null;
            $details['city'] = $validatedData['city'] ?? null;
            $details['country'] = $validatedData['country'] ?? null;
            $details['education_level'] = $validatedData['education_level'] ?? null;
            $details['occupation'] = $validatedData['occupation'] ?? null;
            $details['skills'] = $validatedData['skills'] ?? null;
            $details['motivation'] = $validatedData['motivation'] ?? null;
            $details['previous_experience'] = $validatedData['previous_experience'] ?? null;
            $details['preferred_area'] = $validatedData['preferred_area'] ?? null;
            $details['availability'] = $validatedData['availability'] ?? null;
            $details['has_previous_volunteering'] = $validatedData['has_previous_volunteering'] ?? false;
            $details['preferred_organization_type'] = $validatedData['preferred_organization_type'] ?? null;
            $details['emergency_contact_name'] = $validatedData['emergency_contact_name'] ?? null;
            $details['emergency_contact_phone'] = $validatedData['emergency_contact_phone'] ?? null;

            // Update application with details JSON
            $application->update(['details' => $details]);

            DB::commit();

            return redirect()->route('applicationtovolunteer.index')
                ->with('success', 'تم تحديث طلب التطوع بنجاح');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in ApplicationToVolunteerController@update: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث الطلب')->withInput();
        }
    }

    public function destroy($uuid)
    {
        try {
            DB::beginTransaction();

            $application = ApplicationToVolunteer::where('uuid', $uuid)->firstOrFail();
            
            // التحقق من أن المستخدم يملك الطلب أو مسؤول
            if (!$this->ownsApplication($application)) {
                return back()->with('error', 'ليس لديك صلاحية لحذف هذا الطلب');
            }
            $application->delete();

            DB::commit();

            return redirect()->route('applicationtovolunteer.index')
                ->with('success', 'تم حذف طلب التطوع بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in ApplicationToVolunteerController@destroy: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الطلب');
        }
    }

    public function updateStatus(Request $request, $uuid)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,approved,rejected,withdrawn',
                'admin_notes' => 'nullable|string|max:1000'
            ]);

            $application = ApplicationToVolunteer::where('uuid', $uuid)->firstOrFail();
            
            if (!$application->canBeReviewed() && $request->status !== 'withdrawn') {
                return back()->with('error', 'لا يمكن مراجعة هذا الطلب');
            }

            switch ($request->status) {
                case 'approved':
                    $application->approve(auth()->id(), $request->admin_notes);
                    break;
                case 'rejected':
                    $application->reject(auth()->id(), $request->admin_notes);
                    break;
                case 'withdrawn':
                    $application->withdraw();
                    break;
                default:
                    $application->update(['status' => $request->status]);
            }

            return back()->with('success', 'تم تحديث حالة الطلب بنجاح');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error in ApplicationToVolunteerController@updateStatus: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث الحالة');
        }
    }

    private function validateApplicationData(Request $request)
    {
        return $request->validate([
            'full_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'national_id' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'education_level' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'skills' => 'nullable|string|max:500',
            'motivation' => 'nullable|string|max:1000',
            'previous_experience' => 'nullable|string|max:1000',
            'preferred_area' => 'nullable|string|max:100',
            'availability' => 'nullable|string|max:200',
            'has_previous_volunteering' => 'nullable|boolean',
            'preferred_organization_type' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);
    }

    private function getCitiesList()
    {
        return [
            'السعودية' => 'السعودية',
            'تركيا' => 'تركيا',
            'سوريا' => 'سوريا',
            'مصر' => 'مصر',
            'الأردن' => 'الأردن',
            'لبنان' => 'لبنان',
            'العراق' => 'العراق',
            'الكويت' => 'الكويت',
            'الإمارات' => 'الإمارات',
            'عمان' => 'عمان',
            'قطر' => 'قطر',
            'البحرين' => 'البحرين',
            'اليمن' => 'اليمن',
            'فلسطين' => 'فلسطين',
            'المغرب' => 'المغرب',
            'الجزائر' => 'الجزائر',
            'تونس' => 'تونس',
            'ليبيا' => 'ليبيا',
            'السودان' => 'السودان',
            'أخرى' => 'أخرى',
        ];
    }

    private function getEducationLevels()
    {
        return [
            'ابتدائي' => 'ابتدائي',
            'متوسط' => 'متوسط',
            'ثانوي' => 'ثانوي',
            'دبلوم' => 'دبلوم',
            'بكالوريوس' => 'بكالوريوس',
            'ماجستير' => 'ماجستير',
            'دكتوراه' => 'دكتوراه',
        ];
    }

    private function getOrganizationTypes()
    {
        return [
            'منظمات خيرية' => 'منظمات خيرية',
            'مؤسسات تعليمية' => 'مؤسسات تعليمية',
            'مستشفيات' => 'مستشفيات',
            'مراكز ثقافية' => 'مراكز ثقافية',
            'جمعيات اجتماعية' => 'جمعيات اجتماعية',
            'مراكز أبحاث' => 'مراكز أبحاث',
            'أخرى' => 'أخرى',
        ];
    }

    private function getPreferredAreas()
    {
        return [
            'التعليم' => 'التعليم',
            'الصحة' => 'الصحة',
            'البيئة' => 'البيئة',
            'التنمية الاجتماعية' => 'التنمية الاجتماعية',
            'الإغاثة' => 'الإغاثة',
            'الثقافة والفنون' => 'الثقافة والفنون',
            'الرياضة' => 'الرياضة',
            'التقنية' => 'التقنية',
            'أخرى' => 'أخرى',
        ];
    }

    /**
     * Export volunteer requests to CSV
     */
    public function export()
    {
        try {
            $applications = ApplicationToVolunteer::all();
            
            $filename = 'volunteer_requests_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($applications) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // CSV headers
                fputcsv($file, [
                    'ID', 'UUID', 'الاسم الأول', 'الكنية', 'البريد الإلكتروني', 'الهاتف', 'الهوية الوطنية',
                    'تاريخ الميلاد', 'الجنس', 'العنوان', 'المدينة', 'البلد', 'المستوى التعليمي',
                    'المهنة', 'المهارات', 'الدافع', 'الخبرة السابقة', 'المنطقة المفضلة',
                    'التوفر', 'خبرة تطوعية سابقة', 'نوع المؤسسة المفضلة', 'جهة الاتصال في الطوارئ',
                    'رقم جهة الاتصال في الطوارئ', 'الحالة', 'تاريخ المراجعة', 'ملاحظات الإدارة',
                    'تاريخ الإنشاء'
                ]);

                foreach ($applications as $application) {
                    fputcsv($file, [
                        $application->id,
                        $application->uuid,
                        $application->full_name,
                        $application->last_name,
                        $application->email,
                        $application->phone,
                        $application->national_id,
                        $application->birth_date,
                        $application->gender_text,
                        $application->address,
                        $application->city,
                        $application->country,
                        $application->education_level,
                        $application->occupation,
                        $application->skills,
                        $application->motivation,
                        $application->previous_experience,
                        $application->preferred_area,
                        $application->availability,
                        $application->has_previous_volunteering ? 'نعم' : 'لا',
                        $application->preferred_organization_type,
                        $application->emergency_contact_name,
                        $application->emergency_contact_phone,
                        $application->status_text,
                        $application->reviewed_at,
                        $application->admin_notes,
                        $application->created_at,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error in ApplicationToVolunteerController@export: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تصدير البيانات');
        }
    }
} 