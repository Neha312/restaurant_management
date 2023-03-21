<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantStock;

class RestaurantStockController extends Controller
{
    /**
     * API of List Restaurant stock
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

        $query = RestaurantStock::query()->with('restaurants');

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
        $stock = $query->get();

        $data = [
            'count'  => $count,
            'stock'  => $stock
        ];

        return ok('Restaurant Stock list', $data);
    }

    /**
     * API of Create Restaurant stock
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $stock
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'restaurant_id'      => 'required|integer|exists:restaurants,id',
            'stock_type_id'      => 'required|integer|exists:stock_types,id',
            'name'               => 'required|alpha|max:20',
            'available_quantity' => 'required|numeric',
            'minimum_quantity'   => 'nullable|numeric',
        ]);

        $stock = RestaurantStock::create($request->only('restaurant_id', 'stock_type_id', 'name', 'available_quantity', 'minimum_quantity'));

        return ok('Restaurant stock created successfully!', $stock);
    }

    /**
     * API of Update Restaurant stock
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'restaurant_id'      => 'nullable|integer|exists:restaurants,id',
            'stock_type_id'      => 'nullable|integer|exists:stock_types,id',
            'name'               => 'required|alpha|max:20',
            'available_quantity' => 'nullable|numeric',
            'minimum_quantity'   => 'nullable|numeric',
        ]);

        $stock = RestaurantStock::findOrFail($id);
        $stock->update($request->only('restaurant_id', 'stock_type_id', 'name', 'available_quantity', 'minimum_quantity'));

        return ok('Restaurant stock updated successfully!', $stock);
    }

    /**
     * API of get perticuler Restaurant stock details
     *
     * @param  $id
     * @return $stock
     */
    public function get($id)
    {
        $stock = RestaurantStock::with(['restaurants', 'stocks'])->findOrFail($id);

        return ok('Restaurant stock retrieved successfully', $stock);
    }

    /**
     * API of Delete restaurant stock
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        RestaurantStock::findOrFail($id)->delete();

        return ok('Restaurant stock deleted successfully');
    }
}
