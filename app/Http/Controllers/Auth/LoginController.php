<?php

namespace App\Http\Controllers\Auth;

use DB;
use Auth;
use Session;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
    */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /** index login page */
    public function login()
    {
        return view('auth.login');
    }

    /** login page to check database table users */
    public function authenticate(Request $request)
    {
        $request->validate([
            'email'       => 'required|string',
            'password'    => 'required|string',
        ]);
        try {
            $username = $request->email;
            $password = $request->password;

            $dt         = Carbon::now();
            $todayDate  = $dt->toDayDateTimeString();
            
            if (Auth::attempt(['email'=> $username,'password'=> $password])) {
                /** get session */
                $user = Auth::User();
                Session::put('name', $user->name);
                Session::put('email', $user->email);
                Session::put('user_id', $user->user_id);
                Session::put('join_date', $user->join_date);
                Session::put('last_login', $user->join_date);
                Session::put('phone_number', $user->phone_number);
                Session::put('status', $user->status);
                Session::put('role_name', $user->role_name);
                Session::put('avatar', $user->avatar);
                Session::put('position', $user->position);
                Session::put('department', $user->department);

                $updateLastLogin = ['last_login' => $todayDate,];
                User::where('email',$username)->update($updateLastLogin);
                Toastr::success('Login successfully :)','Success');
                return redirect()->intended('home');
            } else {
                Toastr::error('fail, Wrong Login :)','Error');
                return redirect('login');
            }
        }catch(\Exception $e) {
            \Log::info($e);
            DB::rollback();
            Toastr::error('Login fail :)','Error');
            return redirect()->back();
        }
    }

    /** logout and forget session */
    public function logout(Request $request)
    {
        // forget login session
        $request->session()->forget('name');
        $request->session()->forget('email');
        $request->session()->forget('user_id');
        $request->session()->forget('join_date');
        $request->session()->forget('last_login');
        $request->session()->forget('phone_number');
        $request->session()->forget('status');
        $request->session()->forget('role_name');
        $request->session()->forget('avatar');
        $request->session()->forget('position');
        $request->session()->forget('department');
        $request->session()->flush();
        Auth::logout();
        Toastr::success('Logout successfully :)','Success');
        return redirect('login');
    }
}
