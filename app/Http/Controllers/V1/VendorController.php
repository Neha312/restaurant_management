<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Vendor;

class VendorController extends Controller
{
    /**
     * API of List Vendor
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

        $query = Vendor::query()->with('staff');

        if ($request->search) {
            $query = $query->where('legal_name', 'like', "%$request->search%");
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
        $vendor = $query->get();

        $data = [
            'count'   => $count,
            'vendor'  => $vendor
        ];

        return ok('Vendor list', $data);
    }

    /**
     * API of Create Vendor
     *
     *@param  \Illuminate\Http\Request  $request
     *@return json $vendor
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'service_type_id.*'   => 'required|integer|exists:service_types,id',
            'legal_name'          => 'required|alpha|max:20',
            'address1'            => 'required|string|max:50',
            'address2'            => 'nullable|string|max:50',
            'phone'               => 'nullable|integer|min:10',
            'zip_code'            => 'nullable|integer|min:6',
        ]);

        $vendor = Vendor::create($request->only('legal_name', 'address1', 'address2', 'phone', 'zip_code'));
        $vendor->services()->sync($request->service_type_id);

        return ok('Vendor created successfully!', $vendor->load('services'));
    }

    /**
     * API of Update Vendor
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return json $vendor
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'service_type_id.*'   => 'nullable|integer|exists:service_types,id',
            'legal_name'          => 'required|alpha|max:20',
            'address1'            => 'required|string|max:50',
            'address2'            => 'nullable|string|max:50',
            'phone'               => 'nullable|integer',
            'zip_code'            => 'nullable|integer|min:6',

        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->only('legal_name', 'address1', 'address2', 'phone'));
        $vendor->services()->sync($request->service_type_id);

        return ok('Vendor updated successfully!', $vendor->load('services'));
    }

    /**
     * API of get perticuler Vendor details
     *
     * @param  $id
     * @return json $vendor
     */
    public function get($id)
    {
        $vendor = Vendor::with(['services', 'staff'])->findOrFail($id);

        return ok('Vendor retrieved successfully', $vendor);
    }

    /**
     * API of Delete Vendor
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        $vendor = Vendor::findOrFail($id);
        if ($vendor->services()->count() > 0) {
            $vendor->services()->detach();
        }
        $vendor->delete();

        return ok('Vendor deleted successfully');
    }
    /**
     * API of Vendor status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return json $vendor
     */
    public function status($id, Request $request)
    {
        $this->validate($request, [
            'status'   => 'required|in:A,In',
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->only('status'));

        return ok('Vendor status updated Successfully', $vendor);
    }
}
