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

        $query = Restaurant::query()->with(['users', 'cousines', 'orders']);

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
            'count'       => $count,
            'restaurant'  => $restaurant
        ];

        return ok('Restaurant list', $data);
    }

    /**
     * API of Create Restaurant
     *
     *@param  \Illuminate\Http\Request  $request
     *@return json $user
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'user_id.*'           => 'required|integer|exists:users,id',
            'cousine_type_id'     => 'required|integer|exists:cousine_types,id',
            'name'                => 'required|alpha|max:20',
            'address1'            => 'required|string|max:50',
            'address2'            => 'nullable|string|max:50',
            'zip_code'            => 'required|integer|min:6',
            'phone'               => 'required|integer|min:10',
            'profile_picture'     => 'required|mimes:jpg,jpeg,png,bmp,tiff'
        ]);

        $imageName = str_replace(".", " ", (string)microtime(true)) . '.' . $request->profile_picture->getClientOriginalExtension();
        $request->profile_picture->storeAs("public/pictures", $imageName);

        $restaurant = Restaurant::create($request->only('cousine_type_id', 'name', 'address1', 'address2', 'phone', 'zip_code') + ['profile_picture' => $imageName]);
        $restaurant->users()->syncWithoutDetaching($request->user_id);

        return ok('Restaurant created successfully!',  $restaurant->load('users'));
    }

    /**
     * API of Update Restaurant
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return json $restaurant
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'user_id.*'           => 'nullable|integer|exists:users,id',
            'cousine_type_id'     => 'nullable|integer|exists:cousine_types,id',
            'name'                => 'required|alpha|max:20',
            'address1'            => 'nullable|string|max:50',
            'address2'            => 'nullable|string|max:50',
            'zip_code'            => 'nullable|integer|min:6',
            'phone'               => 'nullable|integer|min:10',
            'profile_picture'     => 'required|mimes:jpg,jpeg,png,bmp,tiff'
        ]);

        $restaurant = Restaurant::findOrFail($id);

        $imageName = str_replace(".", " ", (string)microtime(true)) . '.' . $request->profile_picture->getClientOriginalExtension();
        $request->profile_picture->storeAs("public/pictures", $imageName);

        $restaurant->update($request->only('cousine_type_id', 'name', 'address1', 'address2', 'phone', 'zip_code') + ['profile_picture' => $imageName]);
        $restaurant->users()->sync($request->user_id);

        return ok('Restaurant updated successfully!',  $restaurant->load('users'));
    }

    /**
     * API of get perticuler restaurant details
     *
     * @param  $id
     * @return  json $restaurant
     */
    public function get($id)
    {
        $restaurant = Restaurant::with(['users', 'cousines', 'orders'])->findOrFail($id);

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
        $restaurant = Restaurant::findOrFail($id);
        if ($restaurant->users()->count() > 0) {
            $restaurant->users()->detach();
        }
        $restaurant->delete();
        return ok('Restaurant deleted successfully');
    }
}
