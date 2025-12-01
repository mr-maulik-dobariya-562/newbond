<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\PartyType;
use App\Models\TermsCondition;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Role;

class TermsConditionController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:terms-condition-create', only: ['create']),
            new Middleware('permission:terms-condition-view', only: ['index', "getList"]),
            new Middleware('permission:terms-condition-edit', only: ['edit', "update"]),
            new Middleware('permission:terms-condition-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = PartyType::all();
        return view("Master::terms_condition.index", compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"    => "required",
            'party_type_id' => "required",
        ]);

        TermsCondition::create([
            "text"          => $request->name,
            "party_type_id" => $request->party_type_id,
            "created_by"    => auth()->id(),
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Terms & Condition created successfully");
        }
        return $this->withSuccess("Terms & Condition created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TermsCondition $termsCondition)
    {
        $request->validate([
            "name"          => "required",
            'party_type_id' => "required",
        ]);

        $termsCondition->update([
            "text"          => $request->name,
            "party_type_id" => $request->party_type_id,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Terms & Condition Updated successfully");
        }
        return $this->withSuccess("Terms & Condition Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TermsCondition $termsCondition)
    {
        $termsCondition->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Terms & Condition delete successfully");
        }
        return $this->withSuccess("Terms & Condition delete successfully")->back();
    }

    public function getList(Request $request)
    {
        /* Define Searchable */
        $searchableColumns = [
            'id',
            'name',
            'createdBy:name',
        ];

        /* Add Model here with relation */
        $this->model(model: TermsCondition::class, with: [ "createdBy", 'partyType']);

        /* Add Filter here */
        $this->filter([
            // "status" => $request->status,
        ]);


        $editPermission   = $this->hasPermission("terms-condition-edit");
        $deletePermission = $this->hasPermission("terms-condition-delete");

        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.terms-condition.delete", [ 'termsCondition' => $row->id ]);
            $action = "";
            if ($editPermission) {
                $action .= " <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                data-id='{$row->id}'
                                data-name='{$row->text}'
                                data-party_type_id='{$row->party_type_id}'
                                data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                                <i class='far fa-edit' aria-hidden='true'></i>
                            </a>";
            }

            if ($deletePermission) {
                $action .= " <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-bs-toggle='tooltip'
                                data-bs-placement='top' data-bs-original-title='Delete'
                                href='{$delete}'>
                                <i class='fa-solid fa-trash'></i>
                            </a>";
            }
            return [
                "id"            => $row->id,
                "action"        => $action,
                "name"          => $row->text,
                "party_type"    => $row->partyType?->name,
                "created_by"    => $row->createdBy?->displayName(),
                "created_at"    => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at"    => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
