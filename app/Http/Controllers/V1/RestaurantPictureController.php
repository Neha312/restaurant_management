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
     *@return $picture
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
            'count' => $count,
            'data'  => $picture
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
            // 'pictures.*'         => 'required|array',
            'picture.*' => 'required|mimes:jpg,jpeg,png,bmp,tiff'
        ]);

        $images = array();
        if ($files = $request->file('picture')) {
            foreach ($files as $file) {
                $name = str_replace(".", " ", (string)microtime(true)) . '.' . $file->getClientOriginalExtension();
                $file->storeAs("public/pictures", $name);
                $images[] = $name;
            }
        }
        $picture = RestaurantPicture::create($request->only('restaurant_id', 'type') + ['picture' =>  implode("|", $images)]);

        return ok('Restaurant picture created successfully!', $picture);
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
            'picture.*'          => 'nullable|mimes:jpg,jpeg,png,bmp,tiff'
        ]);

        $picture = RestaurantPicture::findOrFail($id);
        $images = array();
        if ($files = $request->file('picture')) {
            Storage::deleteDirectory("public/pictures");
            foreach ($files as $file) {
                $name = str_replace(".", " ", (string)microtime(true)) . '.' . $file->getClientOriginalExtension();
                $file->storeAs("public/pictures", $name);
                $images[] = $name;
            }
        }
        $picture->update($request->only('restaurant_id', 'type') + ['picture' =>  implode("|", $images)]);

        return ok('Restaurant picture updated successfully!', $picture);
    }

    /**
     * API of get perticuler Restaurant picture details
     *
     * @param  $id
     * @return $picture
     */
    public function get($id)
    {
        $picture = RestaurantPicture::with(['restaurants'])->findOrFail($id);

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
        RestaurantPicture::findOrFail($id)->delete();

        return ok('Restaurant picture  deleted successfully');
    }
}
