<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Offers;
use App\Models\PartyType;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OffersController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:offers-create', only: ['create']),
            new Middleware('permission:offers-view', only: ['index', "getList"]),
            new Middleware('permission:offers-edit', only: ['edit', "update"]),
            new Middleware('permission:offers-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = PartyType::all();
        return view("Master::offers.index", compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"          => "required",
            "party_type_id" => "required",
            "value"         => "required",
            "value_type"    => "required",
        ]);

        Offers::create([
            "text"          => $request->name,
            "party_type_id" => $request->party_type_id,
            "value"         => $request->value,
            "value_type"    => $request->value_type,
            "created_by"    => auth()->id(),
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Offers created successfully");
        }
        return $this->withSuccess("Offers created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offers $offer)
    {
        $request->validate([
            "name"          => "required",
            "party_type_id" => "required",
            "value"         => "required",
            "value_type"    => "required",
        ]);

        $offer->update([
            "text"          => $request->name,
            "party_type_id" => $request->party_type_id,
            "value"         => $request->value,
            "value_type"    => $request->value_type,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Offers Updated successfully");
        }
        return $this->withSuccess("Offers Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offers $offer)
    {
        $offer->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Offers delete successfully");
        }
        return $this->withSuccess("Offers delete successfully")->back();
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
        $this->model(model: Offers::class, with: [ "createdBy", 'partyType']);

        /* Add Filter here */
        $this->filter([
            // "status" => $request->status,
        ]);


        $editPermission   = $this->hasPermission("offers-edit");
        $deletePermission = $this->hasPermission("offers-delete");

        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.offers.delete", [ 'offer' => $row->id ]);
            $action = "";
            if ($editPermission) {
                $action .= " <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                data-id='{$row->id}'
                                data-name='{$row->text}'
                                data-party_type_id='{$row->party_type_id}'
                                data-value='{$row->value}'
                                data-value_type='{$row->value_type}'
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
                "value"         => $row->value,
                "value_type"    => $row->value_type,
                "created_by"    => $row->createdBy?->displayName(),
                "created_at"    => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at"    => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
