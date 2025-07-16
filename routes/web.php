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


//---shahd2---//
use App\Http\Controllers\SkillController;
use App\Http\Controllers\PreviousExperienceController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\VolunteerRequestController;
use App\Http\Controllers\WorkflowController;

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
