<?php

namespace App\Http\Controllers\V1;

use App\Models\Stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockController extends Controller
{
    /**
     * API of List Stock
     *
     *@param  \Illuminate\Http\Request  $request
     *@return json $data
     */
    public function list(Request $request)
    {
        $this->validate($request, [
            'page'                => 'nullable|integer',
            'perPage'             => 'nullable|integer',
            'search'              => 'nullable',
            'sort_field'          => 'nullable',
            'sort_order'          => 'nullable|in:asc,desc',
            'price'               => 'nullable|exists:stocks,price',
            'manufacture_date'    => 'nullable|exists:stocks,manufacture_date',
            'expired_date'        => 'nullable|exists:stocks,expired_date',
        ]);

        $query = Stock::query();

        /*filter*/
        if ($request->price) {
            $query->where('price', $request->price);
        }
        if ($request->manufacture_date) {
            $query->where('manufacture_date', $request->manufacture_date);
        }
        if ($request->expired_date) {
            $query->where('expired_date', $request->expired_date);
        }
        /*search*/
        if ($request->search) {
            $query = $query->where('name', 'like', "%$request->search%");
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
        $stock = $query->get();

        $data = [
            'count'  => $count,
            'stock'  => $stock
        ];

        return ok('Stock list', $data);
    }

    /**
     * API of Create Stock
     *
     *@param  \Illuminate\Http\Request  $request
     *@return json $stock
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name'              => 'required|string|max:10',
            'manufacture_date'  => 'required|date',
            'expired_date'      => 'required|date|after:manufacture_date',
            'price'             => 'required|integer',
            'quantity'          => 'required|integer',
            'is_available'      => 'required|boolean',
            'stock_type_id'     => 'required|integer|exists:stock_types,id',
        ]);

        $stock = Stock::create($request->only('name', 'quantity', 'price', 'is_available', 'manufacture_date', 'expired_date', 'stock_type_id'));

        return ok(null, $stock);
    }

    /**
     * API of Update Stock
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'              => 'required|string|max:10',
            'manufacture_date'  => 'required|date',
            'expired_date'      => 'required|date|after:manufacture_date',
            'price'             => 'required|integer',
            'quantity'          => 'required|integer',
            'is_available'      => 'required|boolean',
            'stock_type_id'     => 'required|integer|exists:stock_types,id',
        ]);

        $stock = Stock::findOrFail($id);
        $stock->update($request->only('name', 'quantity', 'price', 'is_available', 'manufacture_date', 'expired_date', 'stock_type_id'));

        return ok('Stock updated successfully!', $stock);
    }

    /**
     * API of get perticuler Stock type details
     *
     * @param  $id
     * @return json $stock
     */
    public function get($id)
    {
        $stock = Stock::findOrFail($id);

        return ok('Stock retrieved successfully', $stock);
    }

    /**
     * API of Delete Stock
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        Stock::findOrFail($id)->delete();
        return ok('Stock deleted successfully');
    }
}
