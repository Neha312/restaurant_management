<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantBill;

class RestaurantBillController extends Controller
{
    /**
     * API of List Restaurant bill
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $bill
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

        $query = RestaurantBill::query();

        if ($request->search) {
            $query = $query->where('status', 'like', "%$request->search%");
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
        $bill = $query->get();

        $data = [
            'count' => $count,
            'data'  => $bill
        ];

        return ok('Restaurant bill list', $data);
    }

    /**
     * API of Create Restaurant bill
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $bill
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'restaurant_id'      => 'required|integer|exists:restaurants,id',
            'vendor_id'          => 'required|integer|exists:vendors,id',
            'stock_type_id'      => 'required|integer|exists:stock_types,id',
            'total_amount'       => 'required|numeric',
            'due_date'           => 'required|date',
            'tax'                => 'required|numeric',
            'status'             => 'required|in:PN,P',
        ]);

        $bill = RestaurantBill::create($request->only('restaurant_id', 'vendor_id', 'stock_type_id', 'total_amount', 'tax', 'status', 'due_date'));

        return ok('Restaurant bill created successfully!', $bill);
    }

    /**
     * API of Update Restaurant bill
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'restaurant_id'      => 'nullable|integer|exists:restaurants,id',
            'vendor_id'          => 'nullable|integer|exists:vendors,id',
            'stock_type_id'      => 'nullable|integer|exists:stock_types,id',
            'total_amount'       => 'nullable|numeric',
            'due_date'           => 'required|date',
            'tax'                => 'required|numeric',
            'status'             => 'required|in:PN,P',
        ]);

        $bill = RestaurantBill::findOrFail($id);
        $bill->update($request->only('restaurant_id', 'vendor_id', 'stock_type_id', 'total_amount', 'tax', 'status', 'due_date'));

        return ok('Restaurant bill updated successfully!', $bill);
    }

    /**
     * API of get perticuler Restaurant bill details
     *
     * @param  $id
     * @return $bill
     */
    public function get($id)
    {
        $bill = RestaurantBill::with(['restaurants', 'stocks', 'vendors'])->findOrFail($id);

        return ok('Restaurant bill retrieved successfully', $bill);
    }

    /**
     * API of Delete restaurant bill
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        RestaurantBill::findOrFail($id)->delete();

        return ok('Restaurant bill deleted successfully');
    }
}
