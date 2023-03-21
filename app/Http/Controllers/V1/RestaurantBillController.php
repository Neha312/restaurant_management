<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantBill;
use App\Models\RestaurantBillTrail;

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

        $query = RestaurantBill::query()->with('trail');

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
            'status'             => 'nullable|in:PN,P',

        ]);
        $total_amount = $request->total_amount + $request->tax;
        $bill = RestaurantBill::create($request->only('restaurant_id', 'vendor_id', 'stock_type_id', 'tax', 'due_date') + ['total_amount' => $total_amount]);
        $trail = RestaurantBillTrail::create($request->only('status'));
        $bill->trail()->save($trail);
        return ok('Restaurant bill created successfully!', $bill->load('trail'));
    }
    /**
     * API of get perticuler Restaurant bill details
     *
     * @param  $id
     * @return $bill
     */
    public function get($id)
    {
        $bill = RestaurantBill::with(['restaurants', 'stocks', 'vendors', 'trail'])->findOrFail($id);

        return ok('Restaurant bill retrieved successfully', $bill);
    }
    /**
     * API of Restaurant bill status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $request
     * @return $bill
     */
    public function status(Request $request)
    {
        $this->validate($request, [
            'restaurant_bill_id' => 'required|integer|exists:restaurant_bills,id',
            'status'             => 'required|in:PN,P',
        ]);

        $bill = RestaurantBillTrail::create($request->only('status', 'restaurant_bill_id'));
        return ok('Bill status updated Successfully', $bill);
    }
}
