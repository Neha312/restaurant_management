<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * API of List User
     *
     *@param  \Illuminate\Http\Request  $request
     *@return json $data
     */
    public function list(Request $request)
    {
        $this->validate($request, [
            'page'          => 'nullable|integer',
            'perPage'       => 'nullable|integer',
            'search'        => 'nullable',
            'sort_field'    => 'nullable',
            'sort_order'    => 'nullable|in:asc,desc',
        ]);

        $query = User::query()->with('roles');

        if ($request->search) {
            $query = $query->where('first_name', 'like', "%$request->search%");
        }
        if ($request->sort_field || $request->sort_order) {
            $query = $query->orderBy($request->sort_field, $request->sort_order);
        }

        /* Pagination */
        $count = $query->count();
        if ($request->page && $request->perPage) {
            $page = $request->page;
            $perPage = $request->perPage;
            $query = $query->skip($perPage * ($page - 1))->take($perPage);
        }

        /* Get records */
        $user = $query->get();

        $data = [
            'count' => $count,
            'user'  => $user
        ];

        return ok('User list', $data);
    }

    /**
     * API of Create User
     *
     *@param  \Illuminate\Http\Request  $request
     *@return json $user
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'role_id'                   => 'required|integer|exists:roles,id',
            'first_name'                => 'required|alpha|max:20',
            'last_name'                 => 'required|alpha|max:20',
            'email'                     => 'required|email|unique:users,email',
            'joining_date'              => 'required|date',
            'ending_date'               => 'nullable|date|after:joining_date',
            'password'                  => 'required|string|max:8',
            'address1'                  => 'required|string|max:50',
            'address2'                  => 'nullable|string|max:50',
            'zip_code'                  => 'required|integer|min:6',
            'phone'                     => 'nullable|integer|min:10',
            'total_leave'               => 'required|numeric',
            'used_leave'                => 'nullable|numeric',
        ]);

        $user = User::create($request->only('role_id', 'first_name', 'last_name', 'email', 'joining_date', 'ending_date', 'address1', 'address2', 'phone', 'total_leave', 'used_leave', 'zip_code') + ['password' => Hash::make($request->password)]);
        return ok('User created successfully!', $user);
    }

    /**
     * API of Update User
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return json $user
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'role_id'                   => 'nullable|integer|exists:roles,id',
            'first_name'                => 'required|alpha|max:20',
            'last_name'                 => 'required|alpha|max:20',
            'email'                     => 'nullable|email|unique:users,email',
            'joining_date'              => 'nullable|date',
            'ending_date'               => 'nullable|date|after:joining_date',
            'password'                  => 'nullable|max:8',
            'address1'                  => 'nullable|string|max:50',
            'address2'                  => 'nullable|string|max:50',
            'zip_code'                  => 'nullable|integer|min:6',
            'phone'                     => 'nullable|integer|min:10',
            'total_leave'               => 'nullable|numeric',
            'used_leave'                => 'nullable|numeric',
            'restaurants.*'             => 'required|array',

        ]);

        $user = User::findOrFail($id);
        $user->update($request->only('role_id', 'first_name', 'last_name', 'email', 'joining_date', 'ending_date', 'address1', 'address2', 'phone', 'total_leave', 'used_leave', 'zip_code') + ['password' => Hash::make($request->password)]);

        return ok('User updated successfully!', $user);
    }

    /**
     * API of get perticuler User details
     *
     * @param  $id
     * @return $user
     */
    public function get($id)
    {
        $user = User::with(['roles', 'restaurants'])->findOrFail($id);

        return ok('User retrieved successfully', $user);
    }

    /**
     * API of Delete User
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        User::findOrFail($id)->delete();

        return ok('User deleted successfully');
    }
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
