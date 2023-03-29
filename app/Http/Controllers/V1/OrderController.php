<?php

namespace App\Http\Controllers\V1;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Mail\BillMail;
use App\Models\Vendor;
use App\Mail\OrderMail;
use Illuminate\Http\Request;
use App\Models\RestaurantBill;
use App\Models\RestaurantUser;
use App\Models\RestaurantBillTrail;
use App\Http\Controllers\Controller;
use App\Mail\OrderCancel;
use Illuminate\Support\Facades\Mail;
use App\Notifications\BillPaidSuccess;
use App\Notifications\OrderStatusUpdated;

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
            'vendor_id'     => 'nullable|exists:vendors,id',
            'restaurant_id' => 'nullable|exists:restaurants,id'

        ]);

        $query = Order::query();
        if (auth()->user()->role->name == "Owner" || auth()->user()->role->name == "Manager" || auth()->user()->role->name == "Vendor") {
            $query->whereHas('vendor.user', function ($query) {
                $query->where('id', auth()->id());
            });
        }
        /*filter*/
        if ($request->vendor_id) {
            $query->whereHas('vendor', function ($query) use ($request) {
                $query->where('id', $request->vendor_id);
            });
        }
        if ($request->restaurant_id) {
            $query->whereHas('restaurant', function ($query) use ($request) {
                $query->where('id', $request->restaurant_id);
            });
        }

        /*search*/
        if ($request->search) {
            $query = $query->where('id', 'like', "%$request->search%");
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
        if ($vendor->status == 'A') {
            $order = Order::create($request->only('restaurant_id', 'vendor_id', 'service_type_id', 'quantity'));
            Mail::to($user->email)->send(new OrderMail($order));
            return ok('Order created successfully!', $order);
        } else {
            return 'This Vendor is In-active';
        }
    }
    /**
     * API of order Approve
     *
     * @param  $id
     * @return json $order
     */
    public function approve(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $stock = $order->vendor->stocks->first();
        $stockType = $stock->stockType;
        $restaurant = $order->restaurant;
        $due_date = Carbon::now()->addDays(6)->format('Y-m-d');
        $total_amount = $stock->first()->price * $order->quantity + $stock->first()->tax;
        if ($order->status == 'R') {
            return ('This Order is already reject');
        }
        if ($order->status == 'A') {
            return ('This Order is already accepted');
        } else {
            $order->update(['status' => 'A']);
        }
        $bill  = RestaurantBill::create([
            'order_id'              => $order->id,
            'restaurant_id'         => $restaurant->id,
            'vendor_id'             => $order->vendor->id,
            'order_id'              => $order->id,
            'stock_type_id'         => $stockType->id,
            'tax'                   => $stock->first()->tax,
            'due_date'              => $due_date,
            'total_amount'          => $total_amount
        ]);
        $trail = RestaurantBillTrail::create(['status' => "PN", 'restaurant_bill_id' => 70]);
        $bill->trails()->save($trail);
        //send mail
        $restaurant = $bill->restaurant;
        $owner = RestaurantUser::where('restaurant_id', $restaurant->id)->where('is_owner', true)->first();
        $user = User::where('id', $owner->user_id)->first();
        Mail::to($user->email)->send(new BillMail($bill));

        return ok('Bill Generatedn Successfully.!');
    }
    /**
     * API of order reject
     *
     * @param  $id
     * @return json $order
     */
    public function reject($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status == 'A') {
            return ('This Order is already Placed');
        }
        if ($order->status == 'R') {
            return ('This Order is already rejected');
        } else {
            $order->update(['status' => 'R']);
            $order->save();
            $restaurant = $order->restaurant;
            $owner = RestaurantUser::where('restaurant_id', $restaurant->id)->where('is_owner', true)->first();
            $user = User::where('id', $owner->user_id)->first();
            Mail::to($user->email)->send(new OrderCancel($order, $user));
            return ok('This order is rejected.');
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
