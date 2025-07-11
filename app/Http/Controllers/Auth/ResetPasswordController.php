<?php
namespace App\Http\Controllers\Auth;

use DB;
use Hash;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;

class ResetPasswordController extends Controller
{
    /** page reset password */
    public function getPassword($token)
    {
       return view('auth.passwords.reset', ['token' => $token]);
    }

    /** update new password */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);
        try {
            $updatePassword = DB::table('password_reset_tokens')->where(['email' => $request->email, 'token' => $request->token])->first();
            if (!$updatePassword) {
                Toastr::error('Invalid token! :)','Error');
                return back();
            } else { 
                User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
                DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();
                Toastr::success('Your password has been changed! :)','Success');
                return redirect('/login');
            }
        } catch(\Exception $e) {
            \Log::info($e);
            DB::rollback();
            Toastr::error('Chage new password :)','Fail');
            return redirect()->back();
        }
    }
}
