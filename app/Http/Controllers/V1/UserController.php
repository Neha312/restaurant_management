<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Role;
use App\Models\Vendor;
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
            'role_id'       => 'nullable|exists:roles,id',
        ]);

        $query = User::query();
        if (auth()->user()->role->name == "Owner" || auth()->user()->role->name == "Manager") {
            $query->where('id', auth()->id());
        }
        /*filter*/
        if ($request->role_id) {
            $query->whereHas('role', function ($query) use ($request) {
                $query->where('id', $request->role_id);
            });
        }
        /*search*/
        if ($request->search) {
            $query = $query->where('first_name', 'like', "%$request->search%");
        }

        /*sorting*/
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
            'first_name'                => 'required|string|max:20',
            'last_name'                 => 'required|string|max:20',
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
            'restaurant_id'             => 'required_if:role_id,3,4,5,6|integer|exists:restaurants,id',
            'service_type_id.*'         => 'required_if:role_id,7|integer|exists:service_types,id',

        ]);
        $role = Role::findOrFail($request->role_id);
        if ($role->name != "Admin") {
            if ($role->name == "Owner") {
                $user = User::create($request->only('role_id', 'first_name', 'last_name', 'email', 'joining_date', 'ending_date', 'address1', 'address2', 'phone', 'total_leave', 'used_leave', 'zip_code') + ['password' => Hash::make($request->password)]);
                return ok('Owner created successfully!', $user);
            } elseif ($role->name == 'Manager' || $role->name == 'Chef' || $role->name == "Waiter" || $role->name == "Cashier") {
                $user = User::create($request->only('role_id', 'first_name', 'last_name', 'email', 'joining_date', 'ending_date', 'address1', 'address2', 'phone', 'total_leave', 'used_leave', 'zip_code') + ['password' => Hash::make($request->password)]);
                $user->restaurantUsers()->attach([$request->restaurant_id => ['is_owner' => false]]);
                return ok('User created successfully!', $user->load('restaurantUsers'));
            } elseif ($role->name  == "Vendor") {
                $user = User::create($request->only('role_id', 'first_name', 'last_name', 'email', 'joining_date', 'ending_date', 'address1', 'address2', 'phone', 'total_leave', 'used_leave', 'zip_code') + ['password' => Hash::make($request->password)]);
                $vendor = Vendor::create($request->only(['user_id' => $user->id]));
                $user->vendors()->save($vendor);
                $vendor->services()->attach($request->service_type_id);

                return ok('Vendor created successfully!', $user->load('vendors'));
            } else
                return 'User can not created!';
        } else {
            return 'Admin Cannot be created !';
        }
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
            'first_name'                => 'required|string|max:20',
            'last_name'                 => 'required|string|max:20',
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
        $user = User::with(['role', 'restaurants'])->findOrFail($id);

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
        $user = User::findOrFail($id);
        if ($user->restaurantUsers()->count() > 0) {
            $user->restaurantUsers()->detach();
        }
        $user->delete();
        return ok('User deleted successfully');
    }
}
