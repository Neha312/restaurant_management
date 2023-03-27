<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Carbon;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * API of User login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $data
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return error("User with this email is not found!");
        }
        if (!Hash::check($request->password, $user->password)) {
            return error("Incorrect Password!");
        }
        $role = array("Admin", "Owner", "Manager", "Vendor");
        if (!in_array($user->role->name, $role)) {
            return error("Role does not match!");
        }
        $token = $user->createToken($request->email)->plainTextToken;

        $data = [
            'token' => $token,
            'user'  => $user
        ];
        return ok('User Logged in Succesfully', $data);
    }

    /**
     * API of User Logout
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return ok("Logged out successfully!");
    }
    //forget password function
    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return error('Email does not exists');
        }
        $token = Str::random(40);
        PasswordReset::create([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
        Mail::to($user->email)->send(new ForgotPasswordMail($token));
        return ok('Please check your mail to reset your password');
    }
    //reset password function
    public function resetPassword(Request $request, $token)
    {
        $psw = Carbon::now()->subMinutes(1)->toDateTimeString();
        PasswordReset::where('created_at', $psw)->delete();
        $request->validate([
            'password' => 'required|confirmed|max:8'
        ]);
        $resetPassword = PasswordReset::where('token', $token)->first();
        if (!$resetPassword) {
            return error('Token is invalid or expired');
        }
        $user = User::where('email', $resetPassword->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        PasswordReset::where('email', $user->email)->delete();
        return ok('password Reset succesfully');
    }

    //Change Password Function
    public function changePassword(Request $request)
    {
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return  error("Your current password does not matches with the password.");
        }

        if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
            // Current password and new password same
            return error("New Password cannot be same as your current password.");
        }

        $request->validate([
            'current-password' => 'required',
            'new-password'     => 'required|min:8',
        ]);

        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();
        return ok("Password successfully changed!");
    }
}
