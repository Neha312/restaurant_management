<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantPicture;
use Illuminate\Support\Facades\Storage;

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
            'user_id'             => 'required|integer|exists:users,id',
            'cousine_type_id.*'   => 'required|integer|exists:cousine_types,id',
            'name'                => 'required|alpha|max:20',
            'address1'            => 'required|string|max:50',
            'address2'            => 'nullable|string|max:50',
            'zip_code'            => 'required|integer|min:6',
            'phone'               => 'required|integer|min:10',
            'logo'                => 'required|mimes:jpg,jpeg,png,bmp,tiff',
            'type'                => 'required|in:M,O',
            'picture.*'           => 'required|mimes:jpg,jpeg,png,bmp,tiff'

        ]);
        $imageName = str_replace(".", " ", (string)microtime(true)) . '.' . $request->logo->getClientOriginalExtension();
        $request->logo->storeAs("public/pictures", $imageName);

        $user = User::findOrFail($request->user_id);

        $image = array();
        if ($request->hasFile('picture')) {
            foreach ($request->picture as $file) {
                $image_name =  str_replace(".", "", (string)microtime(true)) . '.' . $file->getClientOriginalExtension();
                $upload_path =  'public/pictures';
                $file->storeAs($upload_path, $image_name);
                $image[] = [
                    'restaurant_id' => $request->id,
                    'picture' => $image_name,
                    'type' => $request->type
                ];
            }
        }
        if ($user->role_id == 2) {
            $restaurant = Restaurant::create($request->only('name', 'address1', 'address2', 'phone', 'zip_code') + ['logo' => $imageName]);
            $restaurant->users()->syncWithoutDetaching([$request->user_id => ['is_owner' => true]]);
            $restaurant->cousines()->syncWithoutDetaching($request->cousine_type_id);
            $restaurant->pictures()->createMany($image);
            return ok('Restaurant created successfully!',  $restaurant->load('users', 'pictures', 'cousines'));
        }
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
            'cousine_type_id.*'   => 'nullable|integer|exists:cousine_types,id',
            'name'                => 'required|alpha|max:20',
            'address1'            => 'nullable|string|max:50',
            'address2'            => 'nullable|string|max:50',
            'zip_code'            => 'nullable|integer|min:6',
            'phone'               => 'nullable|integer|min:10',
            'logo'                => 'required|mimes:jpg,jpeg,png,bmp,tiff',
            'type'                => 'nullable|in:M,O',
            'picture.*'           => 'nullable|mimes:jpg,jpeg,png,bmp,tiff'
        ]);

        $restaurant = Restaurant::findOrFail($id);
        if ($restaurant->logo) {
            Storage::delete("public/pictures/" . $restaurant->logo);
            $imageName = str_replace(".", " ", (string)microtime(true)) . '.' . $request->logo->getClientOriginalExtension();
            $request->logo->storeAs("public/pictures", $imageName);
        }
        $restaurant->update($request->only('name', 'address1', 'address2', 'phone', 'zip_code') + ['logo' => $imageName]);
        $restaurant->cousines()->sync($request->cousine_type_id);

        $picture = RestaurantPicture::findOrFail($id);
        if ($picture->picture) {
            $image = array();
            if ($request->hasFile('picture')) {
                Storage::delete("public/pictures/" . $picture->picture);
                $restaurant->pictures()->delete();
                foreach ($request->picture as $file) {
                    $image_name =  str_replace(".", "", (string)microtime(true)) . '.' . $file->getClientOriginalExtension();
                    $upload_path =  'public/pictures';
                    $file->storeAs($upload_path, $image_name);
                    $image[] = [
                        'restaurant_id' => $request->id,
                        'picture' => $image_name,
                        'type' => $request->type
                    ];
                }
            }
        }

        $restaurant->pictures()->createMany($image);
        return ok('Restaurant updated successfully!', $restaurant->load('cousines', 'pictures'));
    }

    /**
     * API of get perticuler restaurant details
     *
     * @param  $id
     * @return  json $restaurant
     */
    public function get($id)
    {
        $restaurant = Restaurant::with(['users', 'cousines', 'pictures', 'orders'])->findOrFail($id);

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
        if ($restaurant->users()->count() > 0 && $restaurant->pictures()->count() > 0 && $restaurant->cousines()->count() > 0) {
            $restaurant->users()->detach();
            $restaurant->cousines()->detach();
            $restaurant->pictures()->delete();
        }
        $restaurant->delete();
        return ok('Restaurant deleted successfully');
    }
}
