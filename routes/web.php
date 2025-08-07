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
Route::get('/tasks/{task}/dependencies', [TaskController::class, 'showDependenciesForm'])->name('tasks.dependencies.form');
Route::post('/tasks/{task}/dependencies', [TaskController::class, 'storeDependency'])->name('tasks.dependencies.store');
Route::delete('/tasks/{task}/dependencies/{dependency}', [TaskController::class, 'destroyDependency'])->name('tasks.dependencies.destroy');
Route::post('/tasks/{task}/assign', [App\Http\Controllers\TaskController::class, 'assign'])->name('tasks.assign');

// راوتات تتبع التقدم للمهام
Route::post('/tasks/{task}/progress', [TaskController::class, 'updateProgress'])->name('tasks.updateProgress');
Route::get('/tasks/{task}/progress-history', [TaskController::class, 'progressHistory'])->name('tasks.progressHistory');
Route::get('/tasks/{task}/progress-stats', [TaskController::class, 'progressStats'])->name('tasks.progressStats');

// Projects routes
Route::resource('projects', ProjectController::class);
Route::get('/projects/my-projects', [ProjectController::class, 'myProjects'])->name('projects.my-projects');
Route::get('/projects/team-tasks', [ProjectController::class, 'teamTasks'])->name('projects.team-tasks');

// Analytics routes
Route::prefix('analytics')->name('analytics.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('index');
    Route::get('/reports', [App\Http\Controllers\AnalyticsController::class, 'reports'])->name('reports');
    Route::get('/efficiency', [App\Http\Controllers\AnalyticsController::class, 'efficiency'])->name('efficiency');
    Route::get('/export', [App\Http\Controllers\AnalyticsController::class, 'export'])->name('export');
});

// Notification routes
Route::prefix('notifications')->name('notifications.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::get('/settings', [App\Http\Controllers\NotificationController::class, 'settings'])->name('settings');
    Route::post('/settings', [App\Http\Controllers\NotificationController::class, 'updateSettings'])->name('update-settings');
    Route::post('/settings/reset', [App\Http\Controllers\NotificationController::class, 'resetSettings'])->name('reset-settings');
    Route::post('/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-as-read');
    Route::post('/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
    Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/', [App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('destroy-all');
    Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('unread-count');
    Route::get('/unread', [App\Http\Controllers\NotificationController::class, 'getUnreadNotifications'])->name('unread');
    Route::post('/test-reminders', [App\Http\Controllers\NotificationController::class, 'sendTestReminders'])->name('test-reminders');
});

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

// Task Events routes
Route::prefix('task-events')->name('task-events.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\TaskEventController::class, 'index'])->name('index');
    Route::get('/{task}', [App\Http\Controllers\TaskEventController::class, 'show'])->name('show');
    Route::get('/export', [App\Http\Controllers\TaskEventController::class, 'export'])->name('export');
});

// Volunteer Dashboard routes
Route::prefix('volunteer')->name('volunteer.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\VolunteerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/calendar', [App\Http\Controllers\VolunteerDashboardController::class, 'calendar'])->name('calendar');
    Route::get('/upcoming-tasks', [App\Http\Controllers\VolunteerDashboardController::class, 'upcomingTasks'])->name('upcoming-tasks');
    Route::get('/statistics', [App\Http\Controllers\VolunteerDashboardController::class, 'statistics'])->name('statistics');
});

        // Task status update route for completion interface (removed duplicate)

        // Task History and Timeline routes
        Route::prefix('task-history')->name('task-history.')->middleware('auth')->group(function () {
            Route::get('/', [App\Http\Controllers\TaskHistoryController::class, 'index'])->name('index');
            Route::get('/timeline/{task}', [App\Http\Controllers\TaskHistoryController::class, 'timeline'])->name('timeline');
            Route::get('/archive', [App\Http\Controllers\TaskHistoryController::class, 'archive'])->name('archive');
            Route::post('/restore/{task}', [App\Http\Controllers\TaskHistoryController::class, 'restore'])->name('restore');
            Route::post('/archive/{task}', [App\Http\Controllers\TaskHistoryController::class, 'archiveTask'])->name('archive-task');
            Route::get('/history/{task}', [App\Http\Controllers\TaskHistoryController::class, 'getTaskHistory'])->name('get-history');
            Route::get('/activity-summary', [App\Http\Controllers\TaskHistoryController::class, 'activitySummary'])->name('activity-summary');
            Route::get('/export', [App\Http\Controllers\TaskHistoryController::class, 'export'])->name('export');
        });

// Progress routes
Route::prefix('progress')->name('progress.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\ProgressController::class, 'index'])->name('index');
    Route::get('/calendar', [App\Http\Controllers\ProgressController::class, 'calendar'])->name('calendar');
    Route::get('/project/{project}', [App\Http\Controllers\ProgressController::class, 'projectProgress'])->name('project');
});

// Test route for status update
Route::get('/test-status-update/{task?}', function($taskId = null) {
    $task = $taskId ? \App\Models\Task::find($taskId) : \App\Models\Task::first();
    return view('test-status-update', compact('task'));
})->name('test.status.update');

// Recurring Task routes
Route::prefix('recurring-tasks')->name('recurring-tasks.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\RecurringTaskController::class, 'index'])->name('index');
    Route::get('/statistics', [App\Http\Controllers\RecurringTaskController::class, 'statistics'])->name('statistics');
    Route::post('/preview', [App\Http\Controllers\RecurringTaskController::class, 'preview'])->name('preview');
    Route::get('/{task}', [App\Http\Controllers\RecurringTaskController::class, 'show'])->name('show');
    Route::get('/{task}/edit', [App\Http\Controllers\RecurringTaskController::class, 'edit'])->name('edit');
    Route::put('/{task}', [App\Http\Controllers\RecurringTaskController::class, 'update'])->name('update');
    Route::post('/{task}/generate', [App\Http\Controllers\RecurringTaskController::class, 'generate'])->name('generate');
    Route::post('/{task}/toggle-active', [App\Http\Controllers\RecurringTaskController::class, 'toggleActive'])->name('toggle-active');
    Route::get('/{task}/exceptions', [App\Http\Controllers\RecurringTaskController::class, 'exceptions'])->name('exceptions');
    Route::post('/{task}/exceptions', [App\Http\Controllers\RecurringTaskController::class, 'createException'])->name('exceptions.create');
    Route::delete('/{task}/exceptions/{exception}', [App\Http\Controllers\RecurringTaskController::class, 'deleteException'])->name('exceptions.delete');
});


