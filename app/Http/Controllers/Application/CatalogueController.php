<?php

namespace App\Http\Controllers\Application;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Catalogue;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CatalogueController extends Controller implements HasMiddleware
{
    use DataTable;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:catalogue-create', only: ['create']),
            new Middleware('permission:catalogue-view', only: ['index', "getList"]),
            new Middleware('permission:catalogue-edit', only: ['edit', "update"]),
            new Middleware('permission:catalogue-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Application::catalogue.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'item_group_id' => 'required',
            'country_id' => 'required',
            'name' => 'required',
            'order' => 'required',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // other validation rules
        ]);

        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $validatedData['image'] = FileUpload::upload($file, 'catalogue', app("storage"));
        }
        $validatedData['created_by'] = auth()->user()->id;
        // Save other form data
        Catalogue::create($validatedData);

        if ($request->ajax()) {
            return $this->withSuccess("Catalogue created successfully");
        }
        return $this->withSuccess("Catalogue created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Catalogue $catalogue)
    {
        $validatedData = $request->validate([
            'item_group_id' => 'required',
            'country_id' => 'required',
            'name' => 'required',
            'order' => 'required',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $validatedData['image'] = FileUpload::upload($file, 'catalogue', app("storage"));
        }

        // Update other form data
        $catalogue->update($validatedData);

        if ($request->ajax()) {
            return $this->withSuccess("Catalogue Updated successfully");
        }
        return $this->withSuccess("Catalogue Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Catalogue $catalogue)
    {
        $catalogue->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Catalogue delete successfully");
        }
        return $this->withSuccess("Catalogue delete successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
            "country:name",
        ];

        $this->model(model: Catalogue::class, with: ["country", "createdBy"]);

        // $this->filter([ "status" => $request->status ]);

        $editPermission   = $this->hasPermission("catalogue-edit");
        $deletePermission = $this->hasPermission("catalogue-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("application.catalogue.delete", ['catalogue' => $row->id]);
            $action = "";
 
            $action .= "
                        <a class='btn edit-btn  btn-action bg-success text-white me-2'
                        data-id='{$row->id}'
                        data-name='{$row->name}'
                        data-country_id='{$row->country->id}'
                        data-country_name='{$row->country->name}'
                        data-status='{$row->status}'
                        data-group_name='{$row->itemGroup->group_name}'
                        data-group_id='{$row->itemGroup->id}'
                        data-order='{$row->order}'
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
                "order"       => $row->order,
                "group"       => $row->itemGroup->group_name,
                "status"       => $row->status(),
                "country_id"   => $row->country->name,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
