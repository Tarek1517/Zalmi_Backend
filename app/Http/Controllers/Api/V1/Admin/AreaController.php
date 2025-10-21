<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AreaRequest;
use App\Http\Resources\AreaResource;
use App\Models\Area;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $cityId = $request->input('city_id');

        $areas = Area::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($cityId, function ($query, $cityId) {
                $query->where('city_id', $cityId);
            })
            ->select('id', 'name', 'delivery_charge', 'city_id')
            ->with('city')
            ->paginate(10);

        return AreaResource::collection($areas);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AreaRequest $request)
    {
        return AreaResource::make(Area::query()->create($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AreaRequest $request, string $id)
    {
        $area = Area::query()->find($id);
        $area->update($request->validated());

        return response()->noContent(); //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Area = Area::query()->findOrFail($id);
        $Area->delete();

        return response()->noContent();
    }
}
