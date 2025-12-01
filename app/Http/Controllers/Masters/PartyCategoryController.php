<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\PartyCategory;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PartyCategoryController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:party_category-create', only: ['create']),
            new Middleware('permission:party_category-view', only: ['index', "getList"]),
            new Middleware('permission:party_category-edit', only: ['edit', "update"]),
            new Middleware('permission:party_category-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Master::party_category.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|unique:party_categorys,name",
        ]);

        PartyCategory::create([
            "name"       => $request->name,
            "color"       => $request->color,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Party Category created successfully");
        }
        return $this->withSuccess("Party Category created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PartyCategory $partyCategory)
    {
        $request->validate([
            "name" => "required|unique:party_categorys,name," . $partyCategory->id,
        ]);

        $partyCategory->update([ "name" => $request->name, "color" => $request->color ]);

        if ($request->ajax()) {
            return $this->withSuccess("Party Category Updated successfully");
        }
        return $this->withSuccess("Party Category Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PartyCategory $partyCategory)
    {
        $partyCategory->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Party Category delete successfully");
        }
        return $this->withSuccess("Party Category delete successfully")->back();
    }

    public function getList(Request $request)
    {
        /* Define Searchable */
        $searchableColumns = [
            'id',
            'name',
            'color',
            'createdBy:name',
        ];

        /* Add Model here with relation */
        $this->model(model: PartyCategory::class, with: [ "createdBy" ]);

        /* Add Filter here */
        $this->filter([
            // "status" => $request->status,
        ]);

        $editPermission   = $this->hasPermission("party_category-edit");
        $deletePermission = $this->hasPermission("party_category-delete");
        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use($editPermission, $deletePermission) {
            $delete = route("master.party-category.delete", [ 'party_category' => $row->id ]);
            $action = "";
            if ($editPermission) {
                $action = " <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                data-id='{$row->id}'
                                data-name='{$row->name}'
                                data-color='{$row->color}'
                                data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                                <i class='far fa-edit' aria-hidden='true'></i>
                            </a>
                        ";
            }

            if ($deletePermission) {
                $action .= " <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-bs-toggle='tooltip'
                            data-bs-placement='top' data-bs-original-title='Delete'
                            href='{$delete}'>
                            <i class='fa-solid fa-trash'></i>
                        </a>
                        ";
            }
            return [
                "id"         => $row->id,
                "name"       => $row->name,
                "color"       => $row->color,
                "action"     => $action,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
