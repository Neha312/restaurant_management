<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * API of List Order
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

        $query = Order::query();

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
        $order = $query->get();

        $data = [
            'count'  => $count,
            'order'  => $order
        ];

        return ok('Order list', $data);
    }

    /**
     * API of Create Order
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $order
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'restaurant_id'      => 'required|integer|exists:restaurants,id',
            'vendor_id'          => 'required|integer|exists:vendors,id',
            'service_type_id'    => 'required|integer|exists:service_types,id',
            'quantity'           => 'required|numeric',
            'name'               => 'required|alpha|max:20',

        ]);
        $order = Order::create($request->only('restaurant_id', 'vendor_id', 'service_type_id', 'name', 'quantity'));

        return ok('Order created successfully!', $order);
    }
    /**
     * API of get perticuler Order
     *
     * @param  $id
     * @return $order
     */
    public function get($id)
    {
        $order = Order::with(['restaurants', 'services', 'vendors'])->findOrFail($id);

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
            'restaurant_id'      => 'nullable|integer|exists:restaurants,id',
            'vendor_id'          => 'nullable|integer|exists:vendors,id',
            'service_type_id'    => 'nullable|integer|exists:service_types,id',
            'quantity'           => 'required|numeric',
            'name'               => 'required|alpha|max:20',
        ]);

        $order = Order::findOrFail($id);
        $order->update($request->only('restaurant_id', 'vendor_id', 'service_type_id', 'name', 'quantity'));

        return ok('Order updated successfully!', $order);
    }


    /**
     * API of Delete Order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        Order::findOrFail($id)->delete();

        return ok('Order deleted successfully');
    }
}
