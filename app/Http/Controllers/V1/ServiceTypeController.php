<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ServiceType;

class ServiceTypeController extends Controller
{
    /**
     * API of List Service type
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

        $query = ServiceType::query();

        /*search*/
        if ($request->search) {
            $query = $query->where('name', 'like', "%$request->search%");
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
        $service = $query->get();

        $data = [
            'count'    => $count,
            'service'  => $service
        ];

        return ok('Service type list', $data);
    }

    /**
     * API of Create Service type
     *
     *@param  \Illuminate\Http\Request  $request
     *@return json $service
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required|string|max:30',
        ]);

        $service = ServiceType::create($request->only('name'));

        return ok('Service type created successfully!', $service);
    }

    /**
     * API of Update Service
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return json $service
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'   => 'required|string|max:30',
        ]);

        $service = ServiceType::findOrFail($id);
        $service->update($request->only('name'));

        return ok('Service type updated successfully!', $service);
    }

    /**
     * API of get perticuler Service type details
     *
     * @param  $id
     * @return json $service
     */
    public function get($id)
    {
        $service = ServiceType::with('vendors')->findOrFail($id);

        return ok('Service type retrieved successfully', $service);
    }

    /**
     * API of Delete Service type
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        $service = ServiceType::findOrFail($id);
        if ($service->vendors()->count() > 0) {
            $service->vendors()->detach();
        }
        $service->delete();

        return ok('Service type deleted successfully');
    }
}
