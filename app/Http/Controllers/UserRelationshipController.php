<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserRelationshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $relationships = $user->relationships()
                             ->with('relatedUser')
                             ->orderBy('created_at', 'desc')
                             ->paginate(15);

        return view('user-relationships.index', compact('relationships'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('user-relationships.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'related_user_id' => 'required|exists:users,id',
            'relationship_type' => 'required|in:supervisor,subordinate,colleague,mentor,mentee,project_leader,project_member,team_leader,team_member,client,partner',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // التحقق من عدم وجود علاقة مسبقة
        $existingRelationship = UserRelationship::where('user_id', Auth::id())
            ->where('related_user_id', $request->related_user_id)
            ->where('relationship_type', $request->relationship_type)
            ->first();

        if ($existingRelationship) {
            return redirect()->back()
                ->with('error', 'هذه العلاقة موجودة بالفعل')
                ->withInput();
        }

        UserRelationship::create([
            'user_id' => Auth::id(),
            'related_user_id' => $request->related_user_id,
            'relationship_type' => $request->relationship_type,
            'status' => 'active',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('user-relationships.index')
            ->with('success', 'تم إنشاء العلاقة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserRelationship $userRelationship)
    {
        $user = Auth::user();
        
        if ($userRelationship->user_id !== $user->id && $userRelationship->related_user_id !== $user->id) {
            abort(403, 'ليس لديك صلاحية لعرض هذه العلاقة');
        }

        $userRelationship->load(['user', 'relatedUser']);

        return view('user-relationships.show', compact('userRelationship'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserRelationship $userRelationship)
    {
        $user = Auth::user();
        
        if ($userRelationship->user_id !== $user->id) {
            abort(403, 'ليس لديك صلاحية لتعديل هذه العلاقة');
        }

        $users = User::where('id', '!=', Auth::id())->get();

        return view('user-relationships.edit', compact('userRelationship', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserRelationship $userRelationship)
    {
        $user = Auth::user();
        
        if ($userRelationship->user_id !== $user->id) {
            abort(403, 'ليس لديك صلاحية لتعديل هذه العلاقة');
        }

        $validator = Validator::make($request->all(), [
            'related_user_id' => 'required|exists:users,id',
            'relationship_type' => 'required|in:supervisor,subordinate,colleague,mentor,mentee,project_leader,project_member,team_leader,team_member,client,partner',
            'status' => 'required|in:active,inactive,pending,blocked',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userRelationship->update([
            'related_user_id' => $request->related_user_id,
            'relationship_type' => $request->relationship_type,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('user-relationships.show', $userRelationship)
            ->with('success', 'تم تحديث العلاقة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserRelationship $userRelationship)
    {
        $user = Auth::user();
        
        if ($userRelationship->user_id !== $user->id) {
            abort(403, 'ليس لديك صلاحية لحذف هذه العلاقة');
        }

        $userRelationship->delete();

        return redirect()->route('user-relationships.index')
            ->with('success', 'تم حذف العلاقة بنجاح');
    }

    /**
     * عرض المشرفين المباشرين
     */
    public function supervisors()
    {
        $user = Auth::user();
        $supervisors = UserRelationship::getSupervisors($user->id);

        return view('user-relationships.supervisors', compact('supervisors'));
    }

    /**
     * عرض المرؤوسين المباشرين
     */
    public function subordinates()
    {
        $user = Auth::user();
        $subordinates = UserRelationship::getSubordinates($user->id);

        return view('user-relationships.subordinates', compact('subordinates'));
    }

    /**
     * عرض الزملاء
     */
    public function colleagues()
    {
        $user = Auth::user();
        $colleagues = UserRelationship::getColleagues($user->id);

        return view('user-relationships.colleagues', compact('colleagues'));
    }

    /**
     * البحث عن مستخدمين
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q');
        
        $users = User::where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('user_id', 'like', "%{$query}%")
                    ->where('id', '!=', Auth::id())
                    ->limit(10)
                    ->get(['id', 'name', 'email', 'user_id']);

        return response()->json($users);
    }
}
