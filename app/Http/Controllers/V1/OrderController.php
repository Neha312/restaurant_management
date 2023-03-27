<?php

namespace App\Http\Controllers\V1;

use App\Models\Order;
use App\Models\Vendor;
use App\Mail\OrderMail;
use Illuminate\Http\Request;
use App\Models\RestaurantBillTrail;
use App\Http\Controllers\Controller;
use App\Notifications\BillPaidSuccess;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Support\Facades\Mail;

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
        if (auth()->user()->role->name == "Owner" || auth()->user()->role->name == "Manager" || auth()->user()->role->name == "Vendor") {
            $query->whereHas('vendor.user', function ($query) {
                $query->where('id', auth()->id());
            });
        }
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
     *@return json $order
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'restaurant_id'      => 'required|integer|exists:restaurants,id',
            'vendor_id'          => 'required|integer|exists:vendors,id',
            'service_type_id'    => 'required|integer|exists:service_types,id',
            'quantity'           => 'required|numeric'
        ]);
        $vendor = Vendor::where('id', $request->vendor_id)->first();
        $user = $vendor->user()->first();
        // dd($user->email);
        if ($vendor->status == 'A') {
            $order = Order::create($request->only('restaurant_id', 'vendor_id', 'service_type_id', 'quantity'));
            // dd($order->id);
            Mail::to($user->email)->send(new OrderMail($order));
            return ok('Order created successfully!', $order);
        } else {
            return 'This Vendor is In-active';
        }
    }
    /**
     * API of get perticuler Order
     *
     * @param  $id
     * @return json $order
     */
    public function get($id)
    {
        $order = Order::with(['restaurant', 'service', 'vendor'])->findOrFail($id);

        return ok('Order retrieved successfully', $order);
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
    /**
     * API of Order status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return json $order
     */
    public function status($id, Request $request)
    {
        $this->validate($request, [
            'status'   => 'required|in:DP,D',
        ]);
        $order = Order::findOrFail($id);
        $order->update($request->only('status'));
        $bill = $order->bill;
        $trail = $bill->id;
        if ($request->status == "D") {
            $trails = RestaurantBillTrail::create(['status' => "P"] + ['restaurant_bill_id' => $trail]);
            $bill->vendor->user->notify(new BillPaidSuccess($trails, $order, $bill));
        }
        $bill->restaurant->users->first()->notify(new OrderStatusUpdated($order, $bill));

        return ok('Order status updated Successfully', ['Order' => $order, 'Trail' => $trails]);
    }
}
