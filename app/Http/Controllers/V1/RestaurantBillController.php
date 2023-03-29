<?php

namespace App\Http\Controllers\V1;


use Illuminate\Http\Request;
use App\Models\RestaurantBill;
use App\Http\Controllers\Controller;

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
        ]);

        $query = RestaurantBill::query();
        if (auth()->user()->role->name == "Owner" || auth()->user()->role->name == "Manager" || auth()->user()->role->name == "Vendor") {
            $query->whereHas('restaurant.users', function ($query) {
                $query->where('id', auth()->id());
            });
        }

        /*filter*/
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
}
