<?php

namespace App\Http\Controllers\Application;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\PriceList;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PriceListController extends Controller implements HasMiddleware
{
    use DataTable;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:pricelist-create', only: ['create']),
            new Middleware('permission:pricelist-view', only: ['index', "getList"]),
            new Middleware('permission:pricelist-edit', only: ['edit', "update"]),
            new Middleware('permission:pricelist-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Application::pricelist.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'party_type_id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // other validation rules
        ]);

        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $validatedData['image'] = FileUpload::upload($file, 'pricelist', app("storage"));
        }
        $validatedData['created_by'] = auth()->user()->id;
        // Save other form data
        PriceList::create($validatedData);

        if ($request->ajax()) {
            return $this->withSuccess("Price List created successfully");
        }
        return $this->withSuccess("Price List created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PriceList $pricelist)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'party_type_id' => 'required',
        ]);

        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $validatedData['image'] = FileUpload::upload($file, 'pricelist', app("storage"));
        }

        // Update other form data
        $pricelist->update($validatedData);

        if ($request->ajax()) {
            return $this->withSuccess("Price List Updated successfully");
        }
        return $this->withSuccess("Price List Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PriceList $pricelist)
    {
        $pricelist->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Price List delete successfully");
        }
        return $this->withSuccess("Price List delete successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
            "partyType:name",
        ];

        $this->model(model: PriceList::class, with: ["partyType", "createdBy"]);

        // $this->filter([ "status" => $request->status ]);

        $editPermission   = $this->hasPermission("pricelist-edit");
        $deletePermission = $this->hasPermission("pricelist-delete");
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("application.pricelist.delete", ['pricelist' => $row->id]);
            $action = "";
            $action = "
                <a class='btn edit-btn  btn-action bg-success text-white me-2'
                    data-id='{$row->id}'
                    data-title='{$row->title}'
                    data-party_id='{$row->partyType->id}'
                    data-party_name='{$row->partyType->name}'
                    data-image='{$row->viewUrl}'
                    data-permissions='{$editPermission}'
                    data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                    <i class='far fa-edit' aria-hidden='true'></i>
                </a>";
            if ($deletePermission){
                $action .= "
                    <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-bs-toggle='tooltip'
                        data-bs-placement='top' data-bs-original-title='Delete'
                        href='{$delete}'>
                        <i class='fa-solid fa-trash'></i>
                    </a>";
            }
            return [
                "id"         => $row->id,
                "action"     => $action,
                "title"       => $row->title,
                "party_type_id"   => $row->partyType->name,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
