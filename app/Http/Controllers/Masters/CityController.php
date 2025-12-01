<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CityController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:city-create', only: ['create']),
            new Middleware('permission:city-view', only: ['index', "getList"]),
            new Middleware('permission:city-edit', only: ['edit', "update"]),
            new Middleware('permission:city-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = Country::all();
        return view('city.index', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"       => "required|unique:cities,name",
            "country_id" => "required",
            "state_id"   => "required",
        ]);

        City::create([
            "name"       => $request->name,
            "state_id"   => $request->state_id,
            "country_id" => $request->country_id,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("City created successfully");
        }
        return $this->withSuccess("City created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        $request->validate([
            "name"       => "required|unique:cities,name," . $city->id,
            "country_id" => "required",
            "state_id"   => "required",
        ]);

        $city->update([
            "name"       => $request->name,
            "state_id"   => $request->state_id,
            "country_id" => $request->country_id,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("City Updated successfully");
        }
        return $this->withSuccess("City Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();
        if (request()->ajax()) {
            return $this->withSuccess("City Deleted successfully");
        }
        return $this->withSuccess("City Deleted successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
            "state:name",
        ];

        $this->model(model: City::class, with: [ "state", "createdBy" ]);


        $editPermission   = $this->hasPermission("city-edit");
        $deletePermission = $this->hasPermission("city-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.city.delete", [ 'city' => $row->id ]);
            $action = "";

            if ($editPermission) {
                $action .= "
                            <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                data-id='{$row->id}'
                                data-name='{$row->name}'
                                data-city_id='{$row->city_id}'
                                data-country_id='{$row->country_id}'
                                data-state_name='{$row?->state?->name}'
                                data-state_id='{$row->state_id}'
                                data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                                <i class='far fa-edit' aria-hidden='true'></i>
                            </a>
                        ";
            }
            if ($deletePermission) {
                $action .= "
                            <a class='btn btn-action bg-danger text-white me-2 btn-delete'
                                data-id='{$row->id}'
                                data-bs-toggle='tooltip'
                                data-bs-placement='top' data-bs-original-title='Delete'
                                href='{$delete}'>
                                <i class='fa-solid fa-trash'></i>
                            </a>
                        ";
            }

            return [
                "id"         => $row->id,
                "name"       => $row->name,
                "country_id" => $row?->country?->name,
                "action"     => $action,
                "state_id"   => $row?->state?->name,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
