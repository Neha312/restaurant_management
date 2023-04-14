<?php

namespace App\Http\Controllers\V1;


use PDF;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RestaurantBill;
use App\Models\RestaurantUser;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;

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
            'restaurant_id' => 'nullable|exists:restaurants,id',
            'vendor_id'     => 'nullable|exists:vendors,id',
            'total_amount'  => 'nullable',
            'due_date'      => 'nullable|exists:restaurant_bills,due_date',
        ]);

        $query = RestaurantBill::query();

        /*filter*/
        if ($request->total_amount) {
            $query->where('total_amount', '>', $request->total_amount)
                ->orWhere('due_date', $request->due_date);
        }
        if ($request->restaurant_id) {
            $query->whereHas('restaurant', function ($query) use ($request) {
                $query->where('id', $request->restaurant_id);
            });
        }
        if ($request->vendor_id) {
            $query->whereHas('vendor', function ($query) use ($request) {
                $query->where('id', $request->vendor_id);
            });
        }

        /*search*/
        if ($request->search) {
            $query = $query->where('bill_number', 'like', "%$request->search%");
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
        $bill = $query->get();

        $data = [
            'count' => $count,
            'bill'  => $bill
        ];

        return ok('Restaurant bill list', $data);
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
    public function Invoice($id, $oid)
    {
        $bill           = RestaurantBill::findOrFail($id);
        $order_item     = OrderItem::findOrFail($oid);
        $owner          = RestaurantUser::where('restaurant_id', $bill->restaurant->id)->where('is_owner', true)->first();
        $user           = User::where('id', $owner->user_id)->first();
        $pdf = PDF::loadView('invoicePdf', array('bill' => $bill, 'user' => $user, 'order_item' => $order_item));

        return $pdf->download('invoice' . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
