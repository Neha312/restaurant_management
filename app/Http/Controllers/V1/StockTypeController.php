<?php

namespace App\Http\Controllers\V1;

use App\Models\StockType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockTypeController extends Controller
{
    /**
     * API of List Stock type
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

        $query = StockType::query();

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

        return ok('Stock type list', $data);
    }

    /**
     * API of Create Stock type
     *
     *@param  \Illuminate\Http\Request  $request
     *@return json $stock
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required|string|max:30',
        ]);

        $stock = StockType::create($request->only('name'));

        return ok('Stock type created successfully!', $stock);
    }

    /**
     * API of Update Stock type
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:30',
        ]);

        $stock = StockType::findOrFail($id);
        $stock->update($request->only('name'));

        return ok('Stock type updated successfully!', $stock);
    }

    /**
     * API of get perticuler Stock type details
     *
     * @param  $id
     * @return json $stock
     */
    public function get($id)
    {
        $stock = StockType::with('resStocks')->findOrFail($id);

        return ok('Stock type retrieved successfully', $stock);
    }

    /**
     * API of Delete Stock type
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        $stock = StockType::findOrFail($id);
        if ($stock->resStocks()->count() > 0) {
            $stock->resStocks()->delete();
        }
        $stock->delete();

        return ok('Stock type deleted successfully');
    }
}
