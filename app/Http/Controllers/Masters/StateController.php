<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StateController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:state-create', only: ['create']),
            new Middleware('permission:state-view', only: ['index', "getList"]),
            new Middleware('permission:state-edit', only: ['edit', "update"]),
            new Middleware('permission:state-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = Country::all();
        return view('state.index', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"       => "required|unique:states,name",
            "country_id" => "required|unique:states,name",
        ]);

        State::create([
            "name"       => $request->name,
            "country_id" => $request->country_id,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("State created successfully");
        }
        return $this->withSuccess("State created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        $request->validate([
            "name"       => "required|unique:countries,name," . $state->id,
            "country_id" => "required"
        ]);

        $state->update([
            "name"       => $request->name,
            "country_id" => $request->country_id
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("State Updated successfully");
        }
        return $this->withSuccess("State Updated successfully")->back();
    }

    public function destroy(State $state)
    {
        $state->delete();
        if (request()->ajax()) {
            return $this->withSuccess("state delete successfully");
        }
        return $this->withSuccess("state delete successfully")->back();
    }

    public function getList(Request $request)
    {
        /* Define Searchable */
        $searchableColumns = [
            'id',
            'name',
            'country:name',
            'createdBy:name',
        ];

        /* Add Model here with relation */
        $this->model(model: State::class, with: [ "country", "createdBy" ]);

        /* Add Filter here */
        $this->filter([
            // "status" => $request->status,
        ]);

        $editPermission   = $this->hasPermission("state-edit");
        $deletePermission = $this->hasPermission("state-delete");
        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.state.delete", [ 'state' => $row->id ]);
            $action = "";
            if ($editPermission) {
                $action = " <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                data-id='{$row->id}'
                                data-name='{$row->name}'
                                data-country_id='{$row->country_id}'
                                data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                                <i class='far fa-edit' aria-hidden='true'></i>
                            </a>";
            }

            if ($deletePermission) {
                $action .= "<a class='btn btn-action bg-danger text-white me-2 btn-delete' data-bs-toggle='tooltip'
                                data-bs-placement='top' data-bs-original-title='Delete'
                                href='{$delete}'>
                                <i class='fa-solid fa-trash'></i>
                            </a>";
            }
            return [
                "id"         => $row->id,
                "name"       => $row->name,
                "country_id" => $row->country->name,
                "action"     => $action,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
