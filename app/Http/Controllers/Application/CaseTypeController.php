<?php

namespace App\Http\Controllers\Application;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\CaseType;
use App\Models\ItemGroup;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CaseTypeController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:case-type-create', only: ['create']),
            new Middleware('permission:case-type-view', only: ['index', "getList"]),
            new Middleware('permission:case-type-edit', only: ['edit', "update"]),
            new Middleware('permission:case-type-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $itemGroups = ItemGroup::all();
        return view("Application::case-type.index", compact("itemGroups"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "title"             => "required",
            "image"             => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
            "sequence_number"   => "required|integer",
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = FileUpload::upload($file, 'case_type', app("storage"));
        }

        CaseType::create([
            "title"               => $request->title,
            "sequence_number"     => $request->sequence_number,
            "is_active"           => $request->is_active ? true : false,
            "image"               => $image ?? null,
            "created_by"          => auth()->id(),
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Case Type created successfully");
        }
        return $this->withSuccess("Case Type created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CaseType $caseType)
    {
        $request->validate([
            "title"             => "required",
            "image"             => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
            "sequence_number"   => "required|integer",
        ]);

        if (!empty($request->hasFile('image')) && $request->hasFile('image')) {
            if ($caseType->image) {
                FileUpload::delete($caseType->image, 'case_type', app("storage"));
            }
            $file = $request->file('image');
            $image = FileUpload::upload($file, 'case_type', app("storage"));
        }

        $caseType->update([
            "title"               => $request->title,
            "sequence_number"     => $request->sequence_number,
            "is_active"           => $request->is_active ? true : false,
            "image"               => $image ?? $caseType->image,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Case Type Updated successfully");
        }
        return $this->withSuccess("Case Type Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CaseType $caseType)
    {
        $caseType->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Case Type delete successfully");
        }
        return $this->withSuccess("Case Type delete successfully")->back();
    }

    public function getList(Request $request)
    {
        /* Define Searchable */
        $searchableColumns = [
            'id',
            'title',
            'createdBy:name',
        ];

        $this->model(model: CaseType::class, with: ["createdBy"]);

        $editPermission   = $this->hasPermission("case-type-edit");
        $deletePermission = $this->hasPermission("case-type-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("application.case-type.delete", ['caseType' => $row->id]);
            $action = "";
            $itemGroups = ItemGroup::whereIn("id", explode(",", $row->item_group_id))->pluck("group_name")->toArray();
            if ($editPermission) {
                $action .= "<a class='btn edit-btn  btn-action bg-success text-white me-2'
                                data-id='{$row->id}'
                                data-title='{$row->title}'
                                data-sequence_number='{$row->sequence_number}'
                                data-is_active='{$row->is_active}'
                                data-image='{$row->viewUrl}'
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
                "id"              => $row->id,
                "action"          => $action,
                "title"           => $row->title,
                "sequence_number" => $row->sequence_number,
                "is_active"       => $row->is_active ? "<span class='badge bg-success text-white'>Active</span>" : "<span class='badge bg-danger text-white'>Inactive</span>",
                "created_by"      => $row->createdBy?->displayName(),
                "created_at"      => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at"      => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
