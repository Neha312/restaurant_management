<?php

namespace App\Http\Controllers\V1;

use App\Models\Vendor;
use App\Models\VendorStaff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class VendorStaffController extends Controller
{
    /**
     * API of List Vendor staff
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

        $query = VendorStaff::query();

        /*search*/
        if ($request->search) {
            $query = $query->where('first_name', 'like', "%$request->search%");
        }

        /*sorting*/
        if ($request->sort_field &&  $request->sort_order) {
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
        $vendor_staff = $query->get();

        $data = [
            'count'         => $count,
            'vendor_staff'  => $vendor_staff
        ];

        return ok('Vendor staff list', $data);
    }

    /**
     * API of Create Vendor staff
     *
     *@param  \Illuminate\Http\Request  $request
     *@return json $vendor_staff
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'vendor_id'          => 'required|integer|exists:vendors,id',
            'first_name'         => 'required|string|max:20',
            'last_name'          => 'required|string|max:20',
            'phone'              => 'nullable|integer|min:10',
        ]);
        $vendor_staff = VendorStaff::create($request->only('vendor_id', 'first_name', 'last_name', 'phone'));
        return ok('Vendor staff created successfully!', $vendor_staff);
    }

    /**
     * API of Update Vendor staff
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return json $vendor_staff
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'vendor_id'          => 'nullable|integer|exists:vendors,id',
            'first_name'         => 'required|string|max:20',
            'last_name'          => 'required|string|max:20',
            'phone'              => 'nullable|integer|min:10',
        ]);

        $vendor_staff = VendorStaff::findOrFail($id);
        $vendor_staff->update($request->only('vendor_id', 'first_name', 'last_name', 'phone'));
        return ok('Vendor staff updated successfully!', $vendor_staff);
    }

    /**
     * API of get perticuler Vendor staff details
     *
     * @param  $id
     * @return json $vendor_staff
     */
    public function get($id)
    {
        $vendor_staff = VendorStaff::findOrFail($id);

        return ok('Vendor staff retrieved successfully', $vendor_staff);
    }

    /**
     * API of Delete Vendor staff
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        VendorStaff::findOrFail($id)->delete();

        return ok('Vendor staff deleted successfully');
    }
}
