<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Models\RestaurantBillTrail;
use App\Http\Controllers\Controller;

class RestaurantBillTrailController extends Controller
{
    /**
     * API of List Restaurant bill trail
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $trail
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

        $query = RestaurantBillTrail::query();

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
        $trail = $query->get();

        $data = [
            'count' => $count,
            'data'  => $trail
        ];

        return ok('Restaurant bill trail list', $data);
    }

    /**
     * API of Create Restaurant bill trail
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $trail
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'restaurant_bill_id' => 'required|integer|exists:restaurants,id',
            'status'             => 'required|in:PN,P',
        ]);
        $trail = RestaurantBillTrail::create($request->only('restaurant_bill_id', 'status'));

        return ok('Restaurant bill trail created successfully!', $trail);
    }
    /**
     * API of get perticuler Order
     *
     * @param  $id
     * @return $order
     */
    public function get($id)
    {
        $order = RestaurantBillTrail::with(['restaurants', 'services', 'vendors'])->findOrFail($id);

        return ok('Order retrieved successfully', $order);
    }
    /**
     * API of Update Order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'restaurant_bill_id' => 'required|integer|exists:restaurants,id',
            'status'             => 'required|in:PN,P',
        ]);

        $trail = RestaurantBillTrail::findOrFail($id);
        $trail->update($request->only('restaurant_bill_id', 'status'));

        return ok('Restaurant bill updated successfully!', $trail);
    }


    /**
     * API of Delete Restaurant bill trail
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        RestaurantBillTrail::findOrFail($id)->delete();

        return ok('Restaurant bill trail deleted successfully');
    }
    /**
     * API of Restaurant bill trail status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return $trail
     */
    public function status($id, Request $request)
    {
        $this->validate($request, [
            'status'  => 'required|in:PN,P',
        ]);

        $trail = RestaurantBillTrail::findOrFail($id);
        $trail->update($request->only('status'));

        return ok('Bill status updated Successfully', $trail);
    }
}
