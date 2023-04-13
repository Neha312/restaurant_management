<?php

namespace App\Http\Controllers\V1;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Mail\BillMail;
use App\Models\Vendor;
use App\Mail\OrderMail;
use App\Mail\OrderCancel;
use App\Models\OrderItem;
use App\Models\Restaurant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RestaurantBill;
use App\Models\RestaurantUser;
use App\Models\RestaurantBillTrail;
use App\Http\Controllers\Controller;
use App\Models\Stock;
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
            $query->whereHas('orderItem.vendor', function ($query) use ($request) {
                $query->where('id', $request->vendor_id);
            });
        }
        if ($request->restaurant_id) {
            $query->whereHas('orderItem.restaurant', function ($query) use ($request) {
                $query->where('id', $request->restaurant_id);
            });
        }

        /*search*/
        if ($request->search) {
            $query = $query->where('order_number', 'like', "%$request->search%")
                ->orWhere('status', 'like', "%$request->search%");
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
        $total_amount = 0;
        $this->validate($request, [
            'orders'                    => 'required|array',
            'orders.*.restaurant_id'    => 'required|integer|exists:restaurants,id',
            'orders.*.vendor_id'        => 'required|integer|exists:vendors,id',
            'orders.*.service_type_id'  => 'required|integer|exists:service_types,id',
            'orders.*.stock_id'         => 'required|integer|exists:stocks,id',
            'orders.*.quantity'         => 'required|numeric',
            'order_number'              => 'string|unique:orders,order_number'
        ]);
        $user = auth()->user()->id;
        $orders = $request->orders;
        // foreach ($orders as $rest) {
        // }

        foreach ($orders as $key => $order) {
            $restaurant = Restaurant::findOrFail($order['restaurant_id']);
            $check_user = $restaurant->users()->where('id', $user)->first();
            if (!$check_user) {
                return ok("This restaurant not belongs to authenticated user..!");
            }
            $vendor = Vendor::where('id', $order['vendor_id'])->where('status', 'A')->first();
            if (!$vendor) {
                return ok('This Vendor is In-active');
            }
            //create order
            $stock_id = $order['stock_id'];
            $stock = Stock::findOrFail($stock_id);
            //chech quanitty
            if ($stock->quantity == 0) {
                return ok("Product " . $stock->name . " " . "out of stock");
            } elseif ($order['quantity'] > $stock->quantity) {
                return ok("Not enough product for stock" . " " . $order['stock_id']);
            }
            $orders[$key]['price'] = Stock::select('price')->where('id', $stock->id)->first()->price;
            $order['price'] = $orders[$key]['price'];
            // calculate tax & total amount
            $tax = ($order['price'] * $stock->tax) / 100;
            $total_amount += ($order['price'] + $tax) * $order['quantity'];
            //manage quantity
            $quantity = $stock->quantity - $order['quantity'];
            $stock->update(['quantity' => $quantity]);
        }
        $order_create = Order::create(['order_number' => Str::random(6)]);
        $items = $order_create->orderItem()->createMany($orders);
        foreach ($items as $item) {
            //send mail
            $user = User::findOrFail($item->stock->created_by);
            Mail::to($user->email)->send(new OrderMail($order_create, $item, $user));
        }
        $data = [
            'orders'       => $order_create->load('orderItem'),
            'total_amount' => $total_amount
        ];
        return ok('Order created successfully!', $data);
    }
    /**
     * API of order Approve
     *
     * @param  $id
     * @return json $order
     */
    public function approve($id)
    {
        $order_item    = OrderItem::findOrFail($id);
        $stock        = $order_item->stock;
        $stockType    = $stock->stockType;
        $restaurant   =  $order_item->restaurant;
        $due_date     = Carbon::now()->addDays(6)->format('Y-m-d');
        $tax = ($stock->price * $stock->tax) / 100;
        $total_amount = ($stock->price + $tax) * $order_item->quantity;
        if ($order_item->status == 'R') {
            return ok('This Order is already reject');
        }
        if ($order_item->status == 'A') {
            return ok('This Order is already accepted');
        } else {
            $order_item->update(['status' => 'A']);
        }
        //generate bill
        $bill  = RestaurantBill::create([
            'order_id'              => $order_item->order->id,
            'restaurant_id'         => $restaurant->id,
            'vendor_id'             => $order_item->vendor->id,
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
        $user          = User::findOrFail($owner->user_id);
        Mail::to($user->email)->send(new BillMail($bill, $order_item));

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
        $order_item = OrderItem::findOrFail($id);

        if ($order_item->status == 'A') {
            return ok('This Order is already Placed');
        }
        if ($order_item->status == 'R') {
            return ok('This Order is already rejected');
        } else {
            //update status
            $order_item->update(['status' => 'R']);
            //send mail
            $restaurant_id = $order_item->restaurant->id;
            $owner         = RestaurantUser::where('restaurant_id', $restaurant_id)->where('is_owner', true)->first();
            $user          = User::findOrFail($owner->user_id);
            Mail::to($user->email)->send(new OrderCancel($order_item, $user));
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
        $order = Order::with(['orderItem', 'bill'])->findOrFail($id);

        return ok('Order retrieved successfully', $order);
    }
    /**
     * API of Order Item status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return json $order_item
     */
    public function status($id, Request $request)
    {
        $this->validate($request, [
            'status'   => 'required|in:A,R',
        ]);
        $order_item = OrderItem::findOrFail($id);
        $order_item->update($request->only('status'));

        //when order status is accepted
        if ($order_item->status == "A") {
            $due_date     = Carbon::now()->addDays(6)->format('Y-m-d');
            $tax = ($order_item->stock->price * $order_item->stock->tax) / 100;
            $total_amount = ($order_item->stock->price + $tax) * $order_item->quantity;
            //generate bill
            $bill  = RestaurantBill::create([
                'order_id'              => $order_item->order->id,
                'restaurant_id'         => $order_item->restaurant->id,
                'vendor_id'             => $order_item->vendor->id,
                'stock_type_id'         => $order_item->stock->stockType->id,
                'tax'                   => $order_item->stock->tax,
                'due_date'              => $due_date,
                'total_amount'          => $total_amount
            ] +
                ['bill_number' => Str::random(6)]);
            //create bill trail
            $bill->trails()->create(['status' => "PN"]);
            //send mail
            $restaurant_id = $bill->restaurant->id;
            $owner         = RestaurantUser::where('restaurant_id', $restaurant_id)->where('is_owner', true)->first();
            $user          = User::findOrFail($owner->user_id);
            Mail::to($user->email)->send(new BillMail($bill, $order_item));
            return ok('Bill Generated Successfully.!');
        }
        //when order status is reject
        {
            //send mail
            $restaurant_id = $order_item->restaurant->id;
            $owner         = RestaurantUser::where('restaurant_id', $restaurant_id)->where('is_owner', true)->first();
            $user          = User::findOrFail($owner->user_id);
            Mail::to($user->email)->send(new OrderCancel($order_item, $user));
            return ok('This order is rejected.');
        }
    }
    /**
     * API of Order status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return json $order
     */
    public function OrderStatus($id, Request $request)
    {
        $this->validate($request, [
            'status'   => 'required|in:D,DP',
        ]);
        $order = Order::findOrFail($id);
        $order->update($request->only('status'));
        //when order status is Delivered
        if ($order->status == "D") {
            //create bill trailc
            $id     = $order->bill->id;
            $trails = RestaurantBillTrail::create(['status' => "P"] + ['restaurant_bill_id' => $id]);
            //send notification
            $order->bill->vendor->user->notify(new BillPaidSuccess($trails, $order, $order->bill));
            $order->bill->restaurant->users->first()->notify(new OrderStatusUpdated($order, $order->bill));
            return ok('Order Delivered Successfully', ['order' => $order]);
        }
        //when status is order dispatch
        else {
            return ok('This order is Dispatch.');
        }
    }
}
