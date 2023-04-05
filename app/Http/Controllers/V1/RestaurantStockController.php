<?php

namespace App\Http\Controllers\V1;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\RestaurantStock;
use App\Http\Controllers\Controller;

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
            'page'              => 'nullable|integer',
            'perPage'           => 'nullable|integer',
            'search'            => 'nullable',
            'sort_field'        => 'nullable',
            'sort_order'        => 'nullable|in:asc,desc',
            'stock_type_id.*'   => 'nullable|exists:stock_types,id'

        ]);

        $query = RestaurantStock::query();
        /*filter*/
        if ($request->stock_type_id && count($request->stock_type_id) > 0) {
            $query->whereHas('stock', function ($query) use ($request) {
                $query->whereIn('id', $request->stock_type_id);
            });
        }

        /*search*/
        if ($request->search) {
            $query = $query->where('name', 'like', "%$request->search%");
        }

        /*sorting*/
        if ($request->sort_field && $request->sort_order) {
            $query = $query->orderBy($request->sort_field, $request->sort_order);
        }

        /* Pagination */
        $count = $query->count();
        if ($request->page && $request->perPage) {
            $page    = $request->page;
            $perPage = $request->perPage;
            $query   = $query->skip($perPage * ($page - 1))->take($perPage);
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
     * API of Update Restaurant stock
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return json $stock
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'stocks.*'                      => 'required|array',
            'stocks.*.stock_type_id'        => 'required|integer|exists:stock_types,id',
            'stocks.*.name'                 => 'required|string|max:20',
            'stocks.*.available_quantity'   => 'required|numeric',
            'stocks.*.minimum_quantity'     => 'required|numeric',
            'remove_id.*'                   => 'nullable|integer|exists:restaurant_stocks,id',
        ]);

        //update multiple restaurant stock
        $restaurant      = Restaurant::findOrFail($id);
        /* delete stock */
        if ($request->remove_id) {
            $stock_ids = RestaurantStock::findOrFail($request->remove_id);
            foreach ($stock_ids as $stock) {
                if ($stock->count() > 0) {
                    $stock->delete();
                }
            }
        }

        //update mutliple create
        foreach ($request['stocks'] as $stocks) {
            RestaurantStock::updateOrCreate(
                [
                    'restaurant_id' => $restaurant->id,
                    'stock_type_id' => $stocks['stock_type_id']
                ],
                [
                    'name'               => $stocks['name'],
                    'available_quantity' => $stocks['available_quantity'],
                    'minimum_quantity'   => $stocks['minimum_quantity'],

                ]
            );
        }
        return ok('Restaurant Stock Updated');
    }

    /**
     * API of get perticuler Restaurant stock details
     *
     * @param  $id
     * @return json $stock
     */
    public function get($id)
    {
        $stock = RestaurantStock::with('stock')->findOrFail($id);

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
