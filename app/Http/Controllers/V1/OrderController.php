<?php

namespace App\Http\Controllers\V1;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Mail\BillMail;
use App\Models\Vendor;
use App\Mail\OrderMail;
use App\Mail\OrderCancel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RestaurantBill;
use App\Models\RestaurantUser;
use App\Models\RestaurantBillTrail;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
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
            $query = $query->where('order_number', 'like', "%$request->search%");
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
            'stock_id'           => 'required|integer|exists:stocks,id',
            'quantity'           => 'required|numeric',
            'order_number'       => 'string|unique:orders,order_number'
        ]);

        $user = auth()->user()->id;
        $restaurant = Restaurant::findOrFail($request->restaurant_id);
        $check_user = $restaurant->users()->where('id', $user)->first();
        if ($check_user) {
            $vendor = Vendor::where('id', $request->vendor_id)->first();
            if ($vendor->status == 'A') {
                //create order
                $order = Order::create($request->only('restaurant_id', 'vendor_id', 'service_type_id', 'stock_id', 'quantity') + ['order_number' => Str::random(6)]);
                $total_amount = ($order->stock->price + $order->stock->tax) * $request->quantity;
                $data = [
                    'order'        => $order,
                    'total_amount' => $total_amount
                ];
                //send mail
                Mail::to($vendor->user()->first()->email)->send(new OrderMail($order, $total_amount));
                return ok('Order created successfully!', $data);
            } else {
                return ok('This Vendor is In-active');
            }
        }
        return ok("This restaurant not belongs to authenticated user..!");
    }
    /**
     * API of order Approve
     *
     * @param  $id
     * @return json $order
     */
    public function approve($id)
    {
        $order        = Order::findOrFail($id);
        $stock        = $order->stock;
        $stockType    = $stock->stockType;
        $restaurant   = $order->restaurant;
        $due_date     = Carbon::now()->addDays(6)->format('Y-m-d');
        $total_amount = $stock->price * $order->quantity + $stock->tax;
        if ($order->status == 'R') {
            return ok('This Order is already reject');
        }
        if ($order->status == 'A') {
            return ok('This Order is already accepted');
        } else {
            $order->update(['status' => 'A']);
        }
        //generate bill
        $bill  = RestaurantBill::create([
            'order_id'              => $order->id,
            'restaurant_id'         => $restaurant->id,
            'vendor_id'             => $order->vendor->id,
            'order_id'              => $order->id,
            'stock_type_id'         => $stockType->id,
            'tax'                   => $stock->tax,
            'due_date'              => $due_date,
            'total_amount'          => $total_amount
        ] +
            ['bill_number' => Str::random(6)]);
        //create bill trail
        $bill->trails()->create(['status' => "PN"]);
        //send mail
        $restaurant_id = $bill->restaurant->id;
        $owner         = RestaurantUser::where('restaurant_id', $restaurant_id)->where('is_owner', true)->first();
        $user          = User::where('id', $owner->user_id)->first();
        Mail::to($user->email)->send(new BillMail($bill));

        return ok('Bill Generated Successfully.!');
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
            return ok('This Order is already Placed');
        }
        if ($order->status == 'R') {
            return ok('This Order is already rejected');
        } else {
            //update status
            $order->update(['status' => 'R']);
            $order->save();
            //send mail
            $restaurant_id = $order->restaurant->id;
            $owner         = RestaurantUser::where('restaurant_id', $restaurant_id)->where('is_owner', true)->first();
            $user          = User::where('id', $owner->user_id)->first();
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
     * API of Order status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return json $order
     */
    public function status($id, Request $request)
    {
        $this->validate($request, [
            'status'   => 'required|in:DP,D,A,R',
        ]);
        $order = Order::findOrFail($id);
        $order->update($request->only('status'));

        //when order status is accepted
        if ($order->status == "A") {
            $due_date     = Carbon::now()->addDays(6)->format('Y-m-d');
            $total_amount = $order->stock->price * $order->quantity + $order->stock->tax;
            //generate bill
            $bill  = RestaurantBill::create(
                [
                    'order_id'              => $order->id,
                    'restaurant_id'         => $order->restaurant->id,
                    'vendor_id'             => $order->vendor->id,
                    'order_id'              => $order->id,
                    'stock_type_id'         => $order->stock->stockType->id,
                    'tax'                   => $order->stock->tax,
                    'due_date'              => $due_date,
                    'total_amount'          => $total_amount
                ] +
                    ['bill_number' => Str::random(6)]
            );
            //create bill trail
            $bill->trails()->create(['status' => "PN"]);
            //send mail
            $restaurant_id = $bill->restaurant->id;
            $owner         = RestaurantUser::where('restaurant_id', $restaurant_id)->where('is_owner', true)->first();
            dd($owner);
            $user          = User::where('id', $owner->user_id)->first();
            Mail::to($user->email)->send(new BillMail($bill));
            return ok('Bill Generated Successfully.!');
        }
        //when order status is reject
        elseif ($order->status == "R") {
            //send mail
            $restaurant_id = $order->restaurant->id;
            $owner         = RestaurantUser::where('restaurant_id', $restaurant_id)->where('is_owner', true)->first();
            $user          = User::where('id', $owner->user_id)->first();
            Mail::to($user->email)->send(new OrderCancel($order, $user));
            return ok('This order is rejected.');
        }
        //when order status is Delivered
        elseif ($order->status == "D") {
            //create bill trail
            $id     = $order->bill->id;
            $trails = RestaurantBillTrail::create(['status' => "P"] + ['restaurant_bill_id' => $order->bill->id]);
            //send notification
            $order->bill->vendor->user->notify(new BillPaidSuccess($trails, $order, $order->bill));
            $order->bill->restaurant->users->first()->notify(new OrderStatusUpdated($order, $order->bill));
            return ok('Order Delivered Successfully', ['Order' => $order]);
        }
        //when status is order dispatch
        else {
            return ok('This order is Dispatch.');
        }
    }
}
