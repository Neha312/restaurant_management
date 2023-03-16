<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    /**
     * API of List Restaurant
     *
     *@param  \Illuminate\Http\Request  $request
     *@return  $restaurant
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

        $query = Restaurant::query();

        if ($request->search) {
            $query = $query->where('name', 'like', "%$request->search%");
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
        $restaurant = $query->get();

        $data = [
            'count' => $count,
            'data'  =>  $restaurant
        ];

        return ok('Restaurant list', $data);
    }

    /**
     * API of Create Restaurant
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $user
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'user_id'             => 'required|integer|exists:users,id',
            'cousine_type_id'     => 'required|integer|exists:cousine_types,id',
            'name'                => 'required|string|max:30',
            'address1'            => 'required|string|max:50',
            'address2'            => 'nullable|string|max:50',
            'zip_code'            => 'required|integer|min:6',
            'phone'               => 'nullable|integer|min:10',
            'profile_picture'     => 'nullable|mimes:jpg,jpeg,png,bmp,tiff'
        ]);

        $imageName = str_replace(".", " ", (string)microtime(true)) . '.' . $request->profile_picture->getClientOriginalExtension();
        $request->profile_picture->storeAs("public/pictures", $imageName);

        $restaurant = Restaurant::create($request->only('user_id', 'cousine_type_id', 'name', 'address1', 'address2', 'phone', 'zip_code') + ['profile_picture' => $imageName]);
        $restaurant->users()->sync($request->user_id);

        return ok('Restaurant created successfully!',  $restaurant);
    }

    /**
     * API of Update Restaurant
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'user_id'             => 'nullable|integer|exists:users,id',
            'cousine_type_id'     => 'nullable|integer|exists:cousine_types,id',
            'name'                => 'nullable|string|max:30',
            'address1'            => 'nullable|string|max:50',
            'address2'            => 'nullable|string|max:50',
            'zip_code'            => 'nullable|integer|min:6',
            'phone'               => 'nullable|integer|min:10',
            'profile_picture'     => 'required|mimes:jpg,jpeg,png,bmp,tiff'
        ]);

        $restaurant = Restaurant::findOrFail($id);

        $imageName = str_replace(".", " ", (string)microtime(true)) . '.' . $request->profile_picture->getClientOriginalExtension();
        $request->profile_picture->storeAs("public/pictures", $imageName);

        $restaurant->update($request->only('user_id', 'cousine_type_id', 'name', 'address1', 'address2', 'phone', 'zip_code') + ['profile_picture' => $imageName]);
        $restaurant->users()->sync($request->user_id);

        return ok('Restaurant updated successfully!',  $restaurant);
    }

    /**
     * API of get perticuler restaurant details
     *
     * @param  $id
     * @return  $restaurant
     */
    public function get($id)
    {
        $restaurant = Restaurant::with(['users', 'cousines'])->findOrFail($id);

        return ok('Restaurant retrieved successfully',  $restaurant);
    }

    /**
     * API of Delete Restaurant
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        Restaurant::findOrFail($id)->delete();

        return ok('Restaurant deleted successfully');
    }
}
