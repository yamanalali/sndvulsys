<?php

namespace App\Http\Controllers\Auth;

use DB;
use Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /** send email forgot password */
    public function getEmail()
    {
        return view('auth.passwords.email');
    }

    /** post email */
    public function postEmail(Request $request)
    {
        $request->validate([
            'email'       => 'required|string',
        ]);

        try {
            $token = Str::random(60);
            $email = $request->email;
            $passwordReset = [
                'email'      => $email,
                'token'      => $token,
                'created_at' => Carbon::now(),
            ];
            DB::table('password_reset_tokens')->insert($passwordReset);

            Mail::send('auth.verify',
                ['token' => $token],
                function($message) use ($request,$email) {
                $message->from($request->email);
                $message->to($email); /** input your email to send */
                $message->subject('Reset Password');
            });
            Toastr::success('We have e-mailed your password reset link! :)','Success');
            return redirect()->back();
        } catch(\Exception $e) {
            \Log::info($e);
            DB::rollback();
            Toastr::error('Send Email :)','Fail');
            return redirect()->back();
        }
    }
}

