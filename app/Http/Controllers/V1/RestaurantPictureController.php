<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantPicture;
use Illuminate\Support\Facades\Storage;

class RestaurantPictureController extends Controller
{
    /**
     * API of List Restaurant picture
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

        $query = RestaurantPicture::query();

        if ($request->search) {
            $query = $query->where('id', 'like', "%$request->search%");
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
        $picture = $query->get();

        $data = [
            'count'    => $count,
            'picture'  => $picture
        ];

        return ok('Restaurant picture list', $data);
    }

    /**
     * API of Create Restaurant picture
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $picture
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'restaurant_id'      => 'required|integer|exists:restaurants,id',
            'type'               => 'nullable|in:M,O',
            'picture.*'          => 'required|mimes:jpg,jpeg,png,bmp,tiff',
        ]);
        $picture = RestaurantPicture::create($request->only('restaurant_id'));

        $image = array();
        if ($request->hasFile('picture')) {
            foreach ($request->picture as $file) {
                $image_name =  str_replace(".", "", (string)microtime(true)) . '.' . $file->getClientOriginalExtension();
                $upload_path =  'images/' . $picture->id;
                $file->storeAs($upload_path, $image_name);
                $image[] = [
                    'restaurant_picture_id' => $picture->id,
                    'picture' => $image_name,
                    'type' => $request->type
                ];
            }
        }
        $picture->images()->createMany($image);
        return ok('Restaurant picture created successfully!', $picture->load('images'));
    }

    /**
     * API of Update Restaurant picture
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'restaurant_id'      => 'nullable|integer|exists:restaurants,id',
            'type'               => 'nullable|in:M,O',
            'picture.*'          => 'required|mimes:jpg,jpeg,png,bmp,tiff'
        ]);

        $picture = RestaurantPicture::findOrFail($id);
        $picture->update($request->only('restaurant_id'));

        $image = array();
        if ($request->hasFile('picture')) {
            $upload_path =  'images/' . $picture->id;
            Storage::deleteDirectory($upload_path);
            $picture->images()->delete();
            foreach ($request->picture as $file) {
                $image_name =  str_replace(".", "", (string)microtime(true)) . '.' . $file->getClientOriginalExtension();
                $upload_path =  'images/' . $picture->id;
                $file->storeAs($upload_path, $image_name);
                $image[] = [
                    'restaurant_picture_id' => $picture->id,
                    'picture' => $image_name,
                    'type' => $request->type
                ];
            }
        }
        $picture->images()->createMany($image);
        return ok('Restaurant picture updated successfully!', $picture->load('images'));
    }

    /**
     * API of get perticuler Restaurant picture details
     *
     * @param  $id
     * @return $picture
     */
    public function get($id)
    {
        $picture = RestaurantPicture::with(['restaurants', 'images'])->findOrFail($id);

        return ok('Restaurant picture retrieved successfully', $picture);
    }

    /**
     * API of Delete Restaurant picture
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        $picture = RestaurantPicture::findOrFail($id);
        $picture->images()->delete();
        $picture->delete();
        return ok('Restaurant picture  deleted successfully');
    }
}
