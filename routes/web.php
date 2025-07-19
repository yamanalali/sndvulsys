<?php

use Illuminate\Support\Facades\Route;

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
    })->name('home');
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
        // Route::get('/home', 'index')->middleware('auth')->name('home'); // Commented out - duplicate route
    });
});


//---shahd2---//
use App\Http\Controllers\SkillController;
use App\Http\Controllers\PreviousExperienceController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ApplicationToVolunteerController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TaskController;

// مهارات
Route::resource('skills', SkillController::class)->middleware('auth');
Route::get('skills/export', [SkillController::class, 'export'])->middleware('auth')->name('skills.export');
Route::get('skills/categories', [SkillController::class, 'getCategories'])->middleware('auth')->name('skills.categories');

// مهارات المستخدم
Route::get('my-skills', [SkillController::class, 'mySkills'])->middleware('auth')->name('skills.my-skills');
Route::post('skills/{skill}/add-to-user', [SkillController::class, 'addToUser'])->middleware('auth')->name('skills.add-to-user');
Route::delete('skills/{skill}/remove-from-user', [SkillController::class, 'removeFromUser'])->middleware('auth')->name('skills.remove-from-user');

// خبرات سابقة
Route::resource('previous-experiences', PreviousExperienceController::class)->middleware('auth');

// جدول التوفر
Route::resource('availabilities', AvailabilityController::class)->middleware('auth');

// طلبات التطوع
// جعل إنشاء الطلب متاح للجميع (بدون مصادقة)
Route::get('applicationtovolunteer/create', [ApplicationToVolunteerController::class, 'create'])->name('applicationtovolunteer.create');
Route::post('applicationtovolunteer', [ApplicationToVolunteerController::class, 'store'])->name('applicationtovolunteer.store');

// باقي عمليات resource تتطلب auth
Route::resource('applicationtovolunteer', ApplicationToVolunteerController::class)->except(['create', 'store'])->middleware('auth');

// طلبات المستخدم الخاصة
Route::get('my-applications', [ApplicationToVolunteerController::class, 'myApplications'])->middleware('auth')->name('my.applications');

// تحديث حالة الطلب (للمسؤولين فقط)
Route::patch('applicationtovolunteer/{uuid}/status', [ApplicationToVolunteerController::class, 'updateStatus'])->middleware('auth')->name('applicationtovolunteer.update-status');

// تصدير طلبات التطوع (للمسؤولين فقط)
Route::get('applicationtovolunteer/export', [ApplicationToVolunteerController::class, 'export'])->middleware('auth')->name('applicationtovolunteer.export');

// تحديث حالة الطلب
Route::patch('applicationtovolunteer/{application}/status', [ApplicationToVolunteerController::class, 'updateStatus'])->middleware('auth')->name('applicationtovolunteer.updateStatus');


Route::resource('workflows', WorkflowController::class)->middleware('auth');

// المهام
Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->middleware('auth')->name('tasks.updateStatus');
Route::get('/tasks/{id}', [TaskController::class, 'show'])->middleware('auth')->name('tasks.show');
Route::get('/tasks/{task}/dependencies', [TaskController::class, 'showDependenciesForm'])->middleware('auth')->name('tasks.dependencies.form');
Route::post('/tasks/{task}/dependencies', [TaskController::class, 'storeDependency'])->middleware('auth')->name('tasks.dependencies.store');

Route::resource('documents', DocumentController::class)->middleware('auth');

Route::get('/backup', [BackupController::class, 'index'])->middleware('auth')->name('backup.index');
Route::get('/backup-documents', [BackupController::class, 'backupDocuments'])->middleware('auth')->name('backup.documents');
