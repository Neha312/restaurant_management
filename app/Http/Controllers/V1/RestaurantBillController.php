<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Mail\BillMail;
use Illuminate\Http\Request;
use App\Models\RestaurantBill;
use App\Models\RestaurantBillTrail;
use App\Http\Controllers\Controller;
use App\Models\RestaurantUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RestaurantBillController extends Controller
{
    /**
     * API of List Restaurant bill
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

        $query = RestaurantBill::query();
        if (auth()->user()->role->name == "Owner" || auth()->user()->role->name == "Manager" || auth()->user()->role->name == "Vendor") {
            $query->whereHas('restaurant.users', function ($query) {
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
        $bill = $query->get();

        $data = [
            'count' => $count,
            'bill'  => $bill
        ];

        return ok('Restaurant bill list', $data);
    }

    /**
     * API of Create Restaurant bill
     *
     *@param  \Illuminate\Http\Request  $request
     *@return json $bill
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'restaurant_id'      => 'required|integer|exists:restaurants,id',
            'order_id'           => 'required|integer|exists:orders,id',
            'vendor_id'          => 'required|integer|exists:vendors,id',
            'stock_type_id'      => 'required|integer|exists:stock_types,id',
            'total_amount'       => 'required|numeric',
            'due_date'           => 'required|date',
            'tax'                => 'required|numeric',
            'status'             => 'nullable|in:PN,P',

        ]);
        $total_amount = $request->total_amount + $request->tax;
        $bill  = RestaurantBill::create($request->only('restaurant_id', 'vendor_id', 'order_id', 'stock_type_id', 'tax', 'due_date') + ['total_amount' => $total_amount]);
        $trail = RestaurantBillTrail::create($request->only('status'));
        $bill->trails()->save($trail);
        //send mail
        $restaurant = $bill->restaurant;
        $owner = RestaurantUser::where('restaurant_id', $restaurant->id)->where('is_owner', true)->first();
        $user = User::where('id', $owner->user_id)->first();
        Mail::to($user->email)->send(new BillMail($bill));

        return ok('Restaurant bill created successfully!', $bill->load('trails'));
    }
    /**
     * API of get perticuler Restaurant bill details
     *
     * @param  $id
     * @return json $bill
     */
    public function get($id)
    {
        $bill = RestaurantBill::with(['restaurant', 'stock', 'vendor', 'trails'])->findOrFail($id);

        return ok('Restaurant bill retrieved successfully', $bill);
    }
}
