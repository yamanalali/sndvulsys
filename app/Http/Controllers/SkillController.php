<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SkillController extends Controller
{
    public function index() {
        try {
            $skills = Skill::withCount('users')->orderBy('name')->get();
            return view('skills.index', compact('skills'));
        } catch (\Exception $e) {
            Log::error('Error in SkillController@index: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحميل المهارات');
        }
    }

    public function create() {
        try {
            $skills = Skill::orderBy('name')->get();
            return view('skills.create', compact('skills'));
        } catch (\Exception $e) {
            Log::error('Error in SkillController@create: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحميل النموذج');
        }
    }

    public function store(Request $request) {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:skills',
                'description' => 'nullable|string|max:300',
                'category' => 'nullable|string|max:100',
                'skill_level' => 'nullable|string|in:مبتدئ,متوسط,متقدم,خبير',
                'experience_years' => 'nullable|string|max:100',
                'certificates' => 'nullable|string|max:500',
                'is_public' => 'boolean',
                'available_for_volunteering' => 'boolean',
                'is_active' => 'boolean',
                'is_featured' => 'boolean'
            ]);

            // تحويل القيم إلى boolean
            $validatedData['is_public'] = $request->has('is_public');
            $validatedData['available_for_volunteering'] = $request->has('available_for_volunteering');
            $validatedData['is_active'] = $request->has('is_active') || !$request->has('is_active'); // default true
            $validatedData['is_featured'] = $request->has('is_featured');

            // إضافة المستخدم الحالي كمالك للمهارة
            $validatedData['user_id'] = auth()->id();

            Skill::create($validatedData);

            return redirect()->route('skills.index')
                ->with('success', 'تمت إضافة المهارة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in SkillController@store: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حفظ المهارة: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id) {
        try {
            $skill = Skill::findOrFail($id);
            return view('skills.edit', compact('skill'));
        } catch (\Exception $e) {
            Log::error('Error in SkillController@edit: ' . $e->getMessage());
            return back()->with('error', 'لم يتم العثور على المهارة');
        }
    }

    public function update(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:skills,name,'.$id,
                'description' => 'nullable|string|max:300',
                'category' => 'nullable|string|max:100',
                'skill_level' => 'nullable|string|in:مبتدئ,متوسط,متقدم,خبير',
                'experience_years' => 'nullable|string|max:100',
                'certificates' => 'nullable|string|max:500',
                'is_public' => 'boolean',
                'available_for_volunteering' => 'boolean',
                'is_active' => 'boolean',
                'is_featured' => 'boolean'
            ]);

            // تحويل القيم إلى boolean
            $validatedData['is_public'] = $request->has('is_public');
            $validatedData['available_for_volunteering'] = $request->has('available_for_volunteering');
            $validatedData['is_active'] = $request->has('is_active') || !$request->has('is_active'); // default true
            $validatedData['is_featured'] = $request->has('is_featured');

            $skill = Skill::findOrFail($id);
            $skill->update($validatedData);

            return redirect()->route('skills.index')
                ->with('success', 'تم تحديث المهارة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in SkillController@update: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث المهارة: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id) {
        try {
            $skill = Skill::findOrFail($id);
            $skill->delete();

            return redirect()->route('skills.index')
                ->with('success', 'تم حذف المهارة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in SkillController@destroy: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف المهارة');
        }
    }

    public function show($id) {
        try {
            $skill = Skill::with('users')->findOrFail($id);
            return view('skills.show', compact('skill'));
        } catch (\Exception $e) {
            Log::error('Error in SkillController@show: ' . $e->getMessage());
            return back()->with('error', 'لم يتم العثور على المهارة');
        }
    }

    public function export() {
        try {
            $skills = Skill::all();
            
            $filename = 'skills_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($skills) {
                $file = fopen('php://output', 'w');
                
                // Header row
                fputcsv($file, ['ID', 'الاسم', 'الوصف', 'الفئة', 'مستوى المهارة', 'سنوات الخبرة', 'الشهادات', 'عامة', 'متاحة للتطوع', 'نشطة', 'مميزة', 'تاريخ الإنشاء']);
                
                // Data rows
                foreach ($skills as $skill) {
                    fputcsv($file, [
                        $skill->id,
                        $skill->name,
                        $skill->description,
                        $skill->category,
                        $skill->skill_level,
                        $skill->experience_years,
                        $skill->certificates,
                        $skill->is_public ? 'نعم' : 'لا',
                        $skill->available_for_volunteering ? 'نعم' : 'لا',
                        $skill->is_active ? 'نعم' : 'لا',
                        $skill->is_featured ? 'نعم' : 'لا',
                        $skill->created_at->format('Y-m-d H:i:s')
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error in SkillController@export: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تصدير المهارات');
        }
    }

    public function getCategories() {
        try {
            $categories = Skill::whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->filter();
            
            return response()->json($categories);
        } catch (\Exception $e) {
            Log::error('Error in SkillController@getCategories: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    // إضافة مهارة للمستخدم الحالي
    public function addToUser($skillId) {
        try {
            $skill = Skill::findOrFail($skillId);
            $user = auth()->user();
            
            if (!$user->skills()->where('skill_id', $skillId)->exists()) {
                $user->skills()->attach($skillId, [
                    'skill_level' => $skill->skill_level ?? 'متوسط',
                    'experience_years' => $skill->experience_years ?? '1-2 سنة',
                    'notes' => 'تمت الإضافة تلقائياً'
                ]);
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'تمت إضافة المهارة إلى ملفك الشخصي'
                    ]);
                }
                
                return back()->with('success', 'تمت إضافة المهارة إلى ملفك الشخصي');
            } else {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'المهارة موجودة بالفعل في ملفك الشخصي'
                    ]);
                }
                
                return back()->with('info', 'المهارة موجودة بالفعل في ملفك الشخصي');
            }
        } catch (\Exception $e) {
            Log::error('Error in SkillController@addToUser: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إضافة المهارة'
                ], 500);
            }
            
            return back()->with('error', 'حدث خطأ أثناء إضافة المهارة');
        }
    }

    // إزالة مهارة من المستخدم الحالي
    public function removeFromUser($skillId) {
        try {
            $skill = Skill::findOrFail($skillId);
            $user = auth()->user();
            
            $user->skills()->detach($skillId);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تمت إزالة المهارة من ملفك الشخصي'
                ]);
            }
            
            return back()->with('success', 'تمت إزالة المهارة من ملفك الشخصي');
        } catch (\Exception $e) {
            Log::error('Error in SkillController@removeFromUser: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إزالة المهارة'
                ], 500);
            }
            
            return back()->with('error', 'حدث خطأ أثناء إزالة المهارة');
        }
    }

    // عرض مهارات المستخدم الحالي
    public function mySkills() {
        try {
            $user = auth()->user();
            $skills = $user->skills()->withCount('users')->get();
            return view('skills.my-skills', compact('skills'));
        } catch (\Exception $e) {
            Log::error('Error in SkillController@mySkills: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحميل مهاراتك');
        }
    }
} 