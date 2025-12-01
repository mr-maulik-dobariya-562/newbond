<?php

namespace App\Http\Controllers\Application;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Arrival;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class ArrivalController extends Controller implements HasMiddleware
{
    use DataTable;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:arrival-create', only: [ 'create' ]),
            new Middleware('permission:arrival-view', only: [ 'index', "getList" ]),
            new Middleware('permission:arrival-edit', only: [ 'edit', "update" ]),
            new Middleware('permission:arrival-delete', only: [ 'destroy' ]),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Application::arrival.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'country_id' => 'required',
            'name'       => 'required',
            'sequence'   => 'required',
            'status'     => 'required|in:ACTIVE,INACTIVE',
            'image'      => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $validatedData[ 'created_by' ] = auth()->id();
        if ($request->hasFile('image')) {

            $file                   = $request->file('image');
            $validatedData[ 'image' ] = FileUpload::upload($file, 'arrival', app("storage"));
        }

        // Save other form data
        Arrival::create($validatedData);

        if ($request->ajax()) {
            return $this->withSuccess("Arrival created successfully");
        }
        return $this->withSuccess("Arrival created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Arrival $arrival)
    {
        $validatedData = $request->validate([
            'country_id' => 'required',
            'name'       => 'required',
            'sequence'   => 'required',
            'status'     => 'required|in:ACTIVE,INACTIVE',
        ]);

        if ($request->hasFile('image')) {

            $file                   = $request->file('image');
            $validatedData[ 'image' ] = FileUpload::upload($file, 'arrival', app("storage"));
        }

        // Update other form data
        $arrival->update($validatedData);

        if ($request->ajax()) {
            return $this->withSuccess("Arrival Updated successfully");
        }
        return $this->withSuccess("Arrival Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Arrival $arrival)
    {
        $arrival->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Arrival delete successfully");
        }
        return $this->withSuccess("Arrival delete successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
            "country:name",
        ];

        $this->model(model: Arrival::class, with: [ "country", "createdBy" ]);

        // $this->filter([ "status" => $request->status ]);

        $editPermission   = $this->hasPermission("arrival-edit");
        $deletePermission = $this->hasPermission("arrival-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("application.arrival.delete", [ 'arrival' => $row->id ]);
            $action = "";
            $action = "
                <a class='btn edit-btn  btn-action bg-success text-white me-2'
                    data-id='{$row->id}'
                    data-name='{$row->name}'
                    data-country_id='{$row->country->id}'
                    data-country_name='{$row->country->name}'
                    data-status='{$row->status}'
                    data-sequence='{$row->sequence}'
                    data-image='{$row->viewUrl}'
                    data-permissions='{$editPermission}'
                    data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                    <i class='far fa-edit' aria-hidden='true'></i>
                </a>
            ";

            if ($deletePermission) {
                $action .= "
                    <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-bs-toggle='tooltip'
                        data-bs-placement='top' data-bs-original-title='Delete'
                        href='{$delete}'>
                        <i class='fa-solid fa-trash'></i>
                    </a>
                ";
            }
            return [
                "id"         => $row->id,
                "action"     => $action,
                "name"       => $row->name,
                "sequence"   => $row->sequence,
                "status"     => $row->status(),
                "country_id" => $row->country->name,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
