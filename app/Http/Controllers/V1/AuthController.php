<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

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
            'password' => 'required',
            'role'     => 'required|in:Admin,Owner,Manager,Vendor'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return error("User with this email is not found!");
        }
        if ($request->role != $user->roles->name) {

            return error("Role does not match!");
        }
        if (!Hash::check($request->password, $user->password)) {
            return error("Incorrect Password!");
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
}
