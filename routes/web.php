<?php

use Illuminate\Support\Facades\Route;
//---shahd2---//
use App\Http\Controllers\SkillController;
use App\Http\Controllers\PreviousExperienceController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\VolunteerRequestController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use App\Models\Task;

/** for side bar menu active */
function set_active($route) {
    if (is_array($route )){
        return in_array(Request::path(), $route) ? 'active' : '';
    }
    return Request::path() == $route ? 'active' : '';
}

Route::get('/', function () {
    return view('auth.login');
});

Route::group(['middleware'=>'auth'],function()
{
    Route::get('home',function()
    {
        return view('dashboard.home');
    });
    Route::get('home',function()
    {
        return view('dashboard.home');
    });
});

Auth::routes();

Route::group(['namespace' => 'App\Http\Controllers\Auth'],function()
{
    // ----------------------------login--------------------------------//
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'authenticate');
        Route::get('/logout', 'logout')->name('logout');
    });

    // ------------------------- register ------------------------------//
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'register')->name('register');
        Route::post('/register','storeUser')->name('register');    
    });

    // ------------------------ forget password -----------------------//
    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::get('forget-password', 'getEmail')->name('forget-password');
        Route::post('forget-password', 'postEmail')->name('forget-password');    
    });

    // ----------------------- reset password -------------------------//
    Route::controller(ResetPasswordController::class)->group(function () {
        Route::get('reset-password/{token}', 'getPassword');
        Route::post('password/update', 'updatePassword')->name('password/update');    
    });

    // ------------------------ confirm password -----------------------//
    Route::controller(ConfirmPasswordController::class)->group(function () {   
        Route::get('confirm/password', 'confirmPassword')->name('confirm/password');    
    });


});

Route::group(['namespace' => 'App\Http\Controllers'],function()
{
    // -------------------------- main dashboard ----------------------//
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'index')->middleware('auth')->name('home');

       
    });
});



// مهارات
Route::resource('skills', SkillController::class);

// خبرات سابقة
Route::resource('previous-experiences', PreviousExperienceController::class);

// جدول التوفر
Route::resource('availabilities', AvailabilityController::class);

// طلبات التطوع
Route::resource('volunteer-requests', VolunteerRequestController::class);

// مثال لتغيير حالة الطلب (يمكنك تخصيصه حسب دالتك في الكنترولر)
Route::patch('volunteer-requests/{volunteer_request}/status', [VolunteerRequestController::class, 'updateStatus'])->name('volunteer_requests.updateStatus');


Route::resource('workflows', WorkflowController::class);

Route::resource('tasks', TaskController::class);

// راوتات إضافية للمهام بعد resource لتجنب التضارب
Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
Route::get('/tasks/{id}/dependencies', [TaskController::class, 'showDependenciesForm'])->name('tasks.dependencies.form');
Route::post('/tasks/{id}/dependencies', [TaskController::class, 'storeDependency'])->name('tasks.dependencies.store');
Route::post('/tasks/{task}/assign', [App\Http\Controllers\TaskController::class, 'assign'])->name('tasks.assign');

// Projects routes
Route::resource('projects', ProjectController::class);
Route::get('/projects/my-projects', [ProjectController::class, 'myProjects'])->name('projects.my-projects');
Route::get('/projects/team-tasks', [ProjectController::class, 'teamTasks'])->name('projects.team-tasks');

// API routes for tasks (CRUD)
Route::prefix('api/tasks')->group(function () {
    Route::get('/', function() {
        \Log::info('API: Get all tasks', ['user_id' => auth()->id()]);
        return response()->json(Task::orderByDesc('created_at')->get());
    });
    Route::get('/{id}', function($id) {
        $task = Task::find($id);
        if (!$task) {
            \Log::warning('API: Task not found', ['task_id' => $id, 'user_id' => auth()->id()]);
            return response()->json(['error' => 'Task not found'], 404);
        }
        \Log::info('API: Get task', ['task_id' => $id, 'user_id' => auth()->id()]);
        return response()->json($task);
    });
    Route::post('/', function(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:new,in_progress,pending,completed,cancelled',
            'deadline' => 'required|date',
        ]);
        $validated['project_id'] = 1;
        $validated['category_id'] = 1;
        $task = Task::create($validated);
        \Log::info('API: Task created', ['task_id' => $task->id, 'user_id' => auth()->id(), 'data' => $validated]);
        return response()->json($task, 201);
    });
    Route::put('/{id}', function(Request $request, $id) {
        $task = Task::find($id);
        if (!$task) {
            \Log::warning('API: Task not found for update', ['task_id' => $id, 'user_id' => auth()->id()]);
            return response()->json(['error' => 'Task not found'], 404);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:new,in_progress,pending,completed,cancelled',
            'deadline' => 'required|date',
        ]);
        $task->update($validated);
        \Log::info('API: Task updated', ['task_id' => $task->id, 'user_id' => auth()->id(), 'data' => $validated]);
        return response()->json($task);
    });
    Route::delete('/{id}', function($id) {
        $task = Task::find($id);
        if (!$task) {
            \Log::warning('API: Task not found for delete', ['task_id' => $id, 'user_id' => auth()->id()]);
            return response()->json(['error' => 'Task not found'], 404);
        }
        $task->delete();
        \Log::info('API: Task deleted', ['task_id' => $id, 'user_id' => auth()->id()]);
        return response()->json(['message' => 'Task deleted successfully']);
    });
});
