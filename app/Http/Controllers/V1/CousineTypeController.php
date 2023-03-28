<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CousineType;

class CousineTypeController extends Controller
{
    /**
     * API of List Cousine type
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

        $query = CousineType::query();

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
        $cousine = $query->get();

        $data = [
            'count'    => $count,
            'cousine'  => $cousine
        ];

        return ok('Cousine type list', $data);
    }

    /**
     * API of Create Cousine type
     *
     *@param  \Illuminate\Http\Request  $request
     *@return json $cousine
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required|string|max:30',
        ]);

        $cousine = CousineType::create($request->only('name'));

        return ok('Cousine type created successfully!', $cousine);
    }

    /**
     * API of Update Cousine
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'    => 'required|string|max:30',
        ]);

        $cousine = CousineType::findOrFail($id);
        $cousine->update($request->only('name'));

        return ok('Cousine type updated successfully!', $cousine);
    }

    /**
     * API of get perticuler Cousine type details
     *
     * @param  $id
     * @return json $cousine
     */
    public function get($id)
    {
        $cousine = CousineType::with('restaurants')->findOrFail($id);

        return ok('Cousine type retrieved successfully', $cousine);
    }

    /**
     * API of Delete Cousine type
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        $cousine = CousineType::findOrFail($id);
        if ($cousine->restaurants()->count() > 0) {
            $cousine->restaurants()->detach();
        }
        $cousine->delete();

        return ok('Cousine type deleted successfully');
    }
}
