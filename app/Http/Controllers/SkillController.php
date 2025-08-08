<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillController extends Controller
{
    public function index(Request $request) {
        $query = Skill::withCount('volunteerRequests');
        
        // فلترة حسب الفئة
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // فلترة حسب المستوى
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        
        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }
        
        // البحث في اسم المهارة والوصف
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        // فلترة المهارات النشطة فقط
        $query->active();
        
        // ترتيب النتائج
        $query->orderBy('name');
        
        $skills = $query->get();
        $categories = Skill::getCategories();
        $levels = Skill::getLevels();
        
        return view('skills.index', compact('skills', 'categories', 'levels'));
    }

    public function create() {
        $categories = Skill::getCategories();
        $levels = Skill::getLevels();
        $volunteerRequests = \App\Models\VolunteerRequest::all();
        
        return view('skills.create', compact('categories', 'levels', 'volunteerRequests'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:skills',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:technical,soft_skills,language,management,creative,other',
            'level' => 'required|in:beginner,intermediate,advanced,expert',
            'is_active' => 'boolean',
            'volunteer-request_id' => 'required|exists:volunteer-requests,id',
            'years_experience' => 'required|integer|min:0|max:50'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // إنشاء المهارة
        $skill = Skill::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'level' => $request->level,
            'is_active' => $request->has('is_active')
        ]);

        // ربط المهارة بالمتطوع المحدد
        $volunteerRequest = \App\Models\VolunteerRequest::findOrFail($request->input('volunteer-request_id'));
        
        // التحقق من عدم وجود ربط سابق
        $existingAssignment = $volunteerRequest->skills()->where('skill_id', $skill->id)->first();
        if (!$existingAssignment) {
            $volunteerRequest->skills()->attach($skill->id, [
                'level' => $request->input('level'),
                'years_experience' => $request->input('years_experience')
            ]);
        }

        return redirect()->route('skills.index')->with('success', 'تمت إضافة المهارة وربطها بالمتطوع بنجاح');
    }

    public function show($id) {
        $skill = Skill::with(['volunteerRequests', 'users'])->findOrFail($id);
        return view('skills.show', compact('skill'));
    }

    public function edit($id) {
        $skill = Skill::findOrFail($id);
        $categories = Skill::getCategories();
        $levels = Skill::getLevels();
        
        return view('skills.edit', compact('skill', 'categories', 'levels'));
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:skills,name,'.$id,
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:technical,soft_skills,language,management,creative,other',
            'level' => 'required|in:beginner,intermediate,advanced,expert',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $skill = Skill::findOrFail($id);
        $skill->update($request->all());
        
        return redirect()->route('skills.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy($id) {
        $skill = Skill::findOrFail($id);
        
        // التحقق من عدم وجود علاقات
        if ($skill->volunteerRequests()->count() > 0 || $skill->users()->count() > 0) {
            return redirect()->route('skills.index')->with('error', 'لا يمكن حذف المهارة لوجود علاقات مرتبطة بها');
        }
        
        $skill->delete();
        return redirect()->route('skills.index')->with('success', 'تم الحذف بنجاح');
    }

    // API methods
    public function getSkillsByCategory($category) {
        $skills = Skill::active()->byCategory($category)->get();
        return response()->json($skills);
    }

    public function toggleStatus($id) {
        $skill = Skill::findOrFail($id);
        $skill->update(['is_active' => !$skill->is_active]);
        
        return response()->json([
            'success' => true,
            'is_active' => $skill->is_active,
            'message' => $skill->is_active ? 'تم تفعيل المهارة' : 'تم إلغاء تفعيل المهارة'
        ]);
    }
} 