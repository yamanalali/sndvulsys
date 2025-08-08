<?php

use Illuminate\Support\Facades\Route;
//---shahd2---//
use App\Http\Controllers\SkillController;
use App\Http\Controllers\PreviousExperienceController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\VolunteerRequestController;
use App\Http\Controllers\VolunteerEvaluationController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UserRelationshipController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ReviewWorkflowController;
use App\Http\Controllers\CaseManagementController;
use App\Http\Controllers\AdvancedSearchController;
use Illuminate\Http\Request;
use App\Models\Task;



Route::get('/', function () {
    return view('auth.login');
});

// مسار اختبار للتأكد من أن النظام يعمل
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'النظام يعمل بشكل صحيح',
        'user' => auth()->user(),
        'documents_count' => App\Models\Document::count(),
        'users_count' => App\Models\User::count()
    ]);
});

// مسار اختبار للنسخ الاحتياطية
Route::get('/test-backups/{document}', function (App\Models\Document $document) {
    return response()->json([
        'status' => 'success',
        'document' => $document,
        'backups_count' => $document->backups()->count(),
        'can_access' => $document->canAccess(auth()->user()),
        'user' => auth()->user()
    ]);
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
Route::get('skills/category/{category}', [SkillController::class, 'getSkillsByCategory'])->name('skills.by-category');
Route::patch('skills/{id}/toggle-status', [SkillController::class, 'toggleStatus'])->name('skills.toggle-status');

// خبرات سابقة
Route::resource('previous-experiences', PreviousExperienceController::class);
Route::get('previous-experiences/volunteer/{volunteerRequestId}', [PreviousExperienceController::class, 'getExperiencesByVolunteer'])->name('previous-experiences.by-volunteer');
Route::get('previous-experiences/current', [PreviousExperienceController::class, 'getCurrentExperiences'])->name('previous-experiences.current');
Route::get('previous-experiences/past', [PreviousExperienceController::class, 'getPastExperiences'])->name('previous-experiences.past');

// جدول التوفر
Route::resource('availabilities', AvailabilityController::class);
Route::get('availabilities/volunteer/{volunteerRequestId}', [AvailabilityController::class, 'getAvailabilitiesByVolunteer'])->name('availabilities.by-volunteer');
Route::get('availabilities/available/{day}/{timeSlot?}', [AvailabilityController::class, 'getAvailableVolunteers'])->name('availabilities.available');
Route::patch('availabilities/{id}/toggle', [AvailabilityController::class, 'toggleAvailability'])->name('availabilities.toggle');
Route::get('availabilities/schedule/{volunteerRequestId}', [AvailabilityController::class, 'getWeeklySchedule'])->name('availabilities.schedule');

// طلبات التطوع
Route::get('volunteer-requests/list', [VolunteerRequestController::class, 'list'])->name('volunteer-requests.list');
Route::get('volunteer-requests/reset-data', [VolunteerRequestController::class, 'resetData'])->name('volunteer-requests.reset-data');
Route::get('volunteer-requests/clear-old-data', [VolunteerRequestController::class, 'clearOldData'])->name('volunteer-requests.clear-old-data');
Route::resource('volunteer-requests', VolunteerRequestController::class);

// مثال لتغيير حالة الطلب (يمكنك تخصيصه حسب دالتك في الكنترولر)
Route::patch('volunteer-requests/{volunteer-request}/status', [VolunteerRequestController::class, 'updateStatus'])->name('volunteer-requests.updateStatus');

// تقييمات المتطوعين
Route::get('volunteer-evaluations/statistics', [VolunteerEvaluationController::class, 'statistics'])->name('volunteer-evaluations.statistics');
Route::resource('volunteer-evaluations', VolunteerEvaluationController::class);
Route::get('volunteer-evaluations/{volunteerRequestId}/create', [VolunteerEvaluationController::class, 'create'])->name('volunteer-evaluations.create');
Route::post('volunteer-evaluations/{volunteerRequestId}', [VolunteerEvaluationController::class, 'store'])->name('volunteer-evaluations.store');

// سير العمل
Route::resource('workflows', WorkflowController::class);
Route::patch('workflows/{id}/status', [WorkflowController::class, 'updateStatus'])->name('workflows.update-status');
Route::get('workflows/status/{status}', [WorkflowController::class, 'getWorkflowsByStatus'])->name('workflows.by-status');
Route::get('workflows/pending', [WorkflowController::class, 'getPendingWorkflows'])->name('workflows.pending');
Route::patch('workflows/{id}/assign', [WorkflowController::class, 'assignToUser'])->name('workflows.assign');
Route::patch('workflows/{id}/next-step', [WorkflowController::class, 'proceedToNextStep'])->name('workflows.next-step');

// الإرسالات - نظام معالجة الطلبات
Route::resource('submissions', SubmissionController::class);
Route::patch('submissions/{id}/status', [SubmissionController::class, 'updateStatus'])->name('submissions.update-status');
Route::patch('submissions/{id}/assign', [SubmissionController::class, 'assignReviewer'])->name('submissions.assign');
Route::post('submissions/{id}/comment', [SubmissionController::class, 'addComment'])->name('submissions.comment');
Route::get('submissions/search', [SubmissionController::class, 'search'])->name('submissions.search');
Route::get('submissions/export', [SubmissionController::class, 'export'])->name('submissions.export');

// سير المراجعة المحسن
Route::prefix('review-workflows')->group(function () {
    Route::get('dashboard', [ReviewWorkflowController::class, 'dashboard'])->name('review-workflows.dashboard');
    Route::get('/', [ReviewWorkflowController::class, 'index'])->name('review-workflows.index');
    Route::get('/{id}', [ReviewWorkflowController::class, 'show'])->name('review-workflows.show');
    Route::patch('/{id}/status', [ReviewWorkflowController::class, 'updateStatus'])->name('review-workflows.update-status');
    Route::patch('/{id}/assign', [ReviewWorkflowController::class, 'assignReviewer'])->name('review-workflows.assign');
    Route::patch('/{id}/next-step', [ReviewWorkflowController::class, 'proceedToNextStep'])->name('review-workflows.next-step');
    Route::patch('/{id}/reassign', [ReviewWorkflowController::class, 'reassign'])->name('review-workflows.reassign');
    Route::get('/{id}/progress', [ReviewWorkflowController::class, 'trackProgress'])->name('review-workflows.progress');
    Route::get('/search', [ReviewWorkflowController::class, 'search'])->name('review-workflows.search');
    Route::get('/export', [ReviewWorkflowController::class, 'export'])->name('review-workflows.export');
});

// إدارة الحالات
Route::prefix('case-management')->group(function () {
    Route::get('dashboard', [CaseManagementController::class, 'dashboard'])->name('case-management.dashboard');
    Route::get('/', [CaseManagementController::class, 'index'])->name('case-management.index');
    Route::get('/{id}', [CaseManagementController::class, 'show'])->name('case-management.show');
    Route::patch('/{id}/status', [CaseManagementController::class, 'updateCaseStatus'])->name('case-management.update-status');
    Route::patch('/{id}/assign', [CaseManagementController::class, 'assignCase'])->name('case-management.assign');
    Route::post('/{id}/note', [CaseManagementController::class, 'addCaseNote'])->name('case-management.note');
    Route::get('/{id}/progress', [CaseManagementController::class, 'trackProgress'])->name('case-management.progress');
    Route::get('/{id}/report', [CaseManagementController::class, 'exportCaseReport'])->name('case-management.report');
});

// صفحة اختبار الحالات
Route::get('/cases/test', function () {
    return view('cases.test');
})->name('cases.test');

// صفحة ترحيب إدارة الحالات
Route::get('/cases/welcome', function () {
    return view('cases.welcome');
})->name('cases.welcome');

// صفحة اختبار الإرسالات
Route::get('/test-submissions', function () {
    return view('test-submissions');
})->name('test-submissions');

// صفحة اختبار إنشاء سير العمل
Route::get('/test-workflow-create', function () {
    return view('test-workflow-create');
})->name('test-workflow-create');

// مسارات قرارات الموافقة/الرفض
Route::prefix('approval-decisions')->name('approval-decisions.')->group(function () {
    Route::post('/approve/{volunteerRequestId}', [App\Http\Controllers\ApprovalDecisionController::class, 'approve'])->name('approve');
    Route::post('/reject/{volunteerRequestId}', [App\Http\Controllers\ApprovalDecisionController::class, 'reject'])->name('reject');
    Route::get('/statistics', [App\Http\Controllers\ApprovalDecisionController::class, 'showStatistics'])->name('statistics');
    
    // API routes
    Route::get('/api/pending', [App\Http\Controllers\ApprovalDecisionController::class, 'getPendingDecisions'])->name('api.pending');
    Route::get('/api/approved', [App\Http\Controllers\ApprovalDecisionController::class, 'getApprovedDecisions'])->name('api.approved');
    Route::get('/api/rejected', [App\Http\Controllers\ApprovalDecisionController::class, 'getRejectedDecisions'])->name('api.rejected');
    Route::get('/api/top-deciders', [App\Http\Controllers\ApprovalDecisionController::class, 'getTopDeciders'])->name('api.top-deciders');
    
    // يجب أن يكون هذا في النهاية لتجنب التضارب مع الروابط الأخرى
    Route::get('/{id}', [App\Http\Controllers\ApprovalDecisionController::class, 'show'])->name('show');
});

// Test route for volunteer registration
Route::get('/test-volunteer-registration', function () {
    return view('test-volunteer-request');
})->name('test-volunteer-registration');

// مسارات الإشعارات
Route::prefix('notifications')->name('notifications.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::get('/{id}', [App\Http\Controllers\NotificationController::class, 'show'])->name('show');
    Route::post('/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-as-read');
    Route::post('/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
    Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/destroy-read', [App\Http\Controllers\NotificationController::class, 'destroyRead'])->name('destroy-read');
    Route::get('/statistics', [App\Http\Controllers\NotificationController::class, 'statistics'])->name('statistics');
    Route::get('/settings', [App\Http\Controllers\NotificationController::class, 'settings'])->name('settings');
    Route::post('/settings', [App\Http\Controllers\NotificationController::class, 'updateSettings'])->name('update-settings');
    Route::get('/export', [App\Http\Controllers\NotificationController::class, 'export'])->name('export');
    
    // API routes
    Route::get('/api/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('api.unread-count');
    Route::get('/api/unread', [App\Http\Controllers\NotificationController::class, 'getUnreadNotifications'])->name('api.unread');
    Route::post('/api/test', [App\Http\Controllers\NotificationController::class, 'sendTestNotification'])->name('api.test');
});

// صفحة اختبار نظام الموافقة/الرفض والإشعارات
Route::get('/test-approval-system', function () {
    return view('test-approval-system');
})->name('test-approval-system');

// صفحة اختبار نظام الموافقة/الرفض المبسط
Route::get('/test-simple-approval-system', function () {
    return view('test-simple-approval-system');
})->name('test-simple-approval-system');

// صفحة اختبار طلب التطوع
Route::get('/test-volunteer-request', function () {
    return view('test-volunteer-request');
})->name('test-volunteer-request');

// صفحة الاختبار النهائية
Route::get('/test-final-system', function () {
    return view('test-final-system');
})->name('test-final-system');

// صفحة اختبار إنشاء طلب تطوع بسيط
Route::get('/test-simple-create', function () {
    return view('test-simple-create');
})->name('test-simple-create');

// صفحة اختبار بسيط جداً
Route::get('/test-minimal-create', function () {
    return view('test-minimal-create');
})->name('test-minimal-create');

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


// المستندات - نظام تخزين المستندات
Route::middleware(['auth'])->group(function () {
    Route::resource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::post('documents/{document}/share', [DocumentController::class, 'share'])->name('documents.share');
    Route::post('documents/{document}/revoke-permission', [DocumentController::class, 'revokePermission'])->name('documents.revoke-permission');
    Route::post('documents/{document}/backup', [DocumentController::class, 'createBackup'])->name('documents.backup');
    Route::get('documents/{document}/backups', [DocumentController::class, 'showBackups'])->name('documents.backups');
    Route::get('documents/backups', [DocumentController::class, 'allBackups'])->name('documents.all-backups');
    Route::post('document-backups/{backup}/restore', [DocumentController::class, 'restoreBackup'])->name('document-backups.restore');
});

// علاقات المستخدمين
Route::resource('user-relationships', UserRelationshipController::class);
Route::get('user-relationships/supervisors', [UserRelationshipController::class, 'supervisors'])->name('user-relationships.supervisors');
Route::get('user-relationships/subordinates', [UserRelationshipController::class, 'subordinates'])->name('user-relationships.subordinates');
Route::get('user-relationships/colleagues', [UserRelationshipController::class, 'colleagues'])->name('user-relationships.colleagues');
Route::get('user-relationships/search-users', [UserRelationshipController::class, 'searchUsers'])->name('user-relationships.search-users');

// البحث المتقدم
Route::prefix('advanced-search')->group(function () {
    Route::get('/', [AdvancedSearchController::class, 'index'])->name('advanced-search.index');
    Route::post('/search', [AdvancedSearchController::class, 'search'])->name('advanced-search.search');
    Route::post('/save', [AdvancedSearchController::class, 'saveSearch'])->name('advanced-search.save');
    Route::patch('/{id}/share', [AdvancedSearchController::class, 'shareSearch'])->name('advanced-search.share');
    Route::get('/saved/{id}', [AdvancedSearchController::class, 'showSavedSearch'])->name('advanced-search.saved');
    Route::delete('/{id}', [AdvancedSearchController::class, 'destroy'])->name('advanced-search.destroy');
    Route::get('/saved-searches', [AdvancedSearchController::class, 'getSavedSearches'])->name('advanced-search.saved-searches');
    Route::get('/popular-searches', [AdvancedSearchController::class, 'getPopularSearches'])->name('advanced-search.popular-searches');
    Route::get('/statistics', [AdvancedSearchController::class, 'getStatistics'])->name('advanced-search.statistics');
    Route::get('/export', [AdvancedSearchController::class, 'exportResults'])->name('advanced-search.export');

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
////
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

// Test route to clear duplicate national IDs
Route::get('/clear-test-data', function () {
    \App\Models\VolunteerRequest::where('national_id', '24243775400')->delete();
    return redirect()->back()->with('success', 'تم مسح البيانات التجريبية بنجاح');
})->name('clear-test-data');

// Route to manually link skills to volunteer requests
Route::get('/link-skills-to-volunteers', function () {
    $volunteerRequests = \App\Models\VolunteerRequest::all();
    $skills = \App\Models\Skill::all();
    
    if ($volunteerRequests->count() == 0) {
        return redirect()->back()->with('error', 'لا توجد طلبات تطوع');
    }
    
    if ($skills->count() == 0) {
        return redirect()->back()->with('error', 'لا توجد مهارات');
    }
    
    foreach ($volunteerRequests as $request) {
        $request->skills()->detach();
        
        $maxSkills = min(3, $skills->count());
        $minSkills = min(2, $maxSkills);
        $numSkills = rand($minSkills, $maxSkills);
        
        $randomSkills = $skills->random($numSkills);
        foreach ($randomSkills as $skill) {
            $request->skills()->attach($skill->id, [
                'level' => ['beginner', 'intermediate', 'advanced', 'expert'][rand(0, 3)],
                'years_experience' => rand(1, 5)
            ]);
        }
    }
    
    return redirect()->back()->with('success', 'تم ربط المهارات بطلبات التطوع بنجاح');
})->name('link-skills-to-volunteers');

// Route to toggle skill status
Route::patch('/skills/{id}/toggle-status', [\App\Http\Controllers\SkillController::class, 'toggleStatus'])->name('skills.toggle-status');

