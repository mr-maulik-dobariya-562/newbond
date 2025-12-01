<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Transport;
use App\Models\TransportDetail;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TransportController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:transport-create', only: ['create']),
            new Middleware('permission:transport-view', only: ['index', "getList"]),
            new Middleware('permission:transport-edit', only: ['edit', "update"]),
            new Middleware('permission:transport-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Master::transport.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"       => "required|unique:transports,name",
            'is_waybill' => "nullable"
        ]);

        $transport = Transport::create([
            "name"       => $request->name,
            "remark"     => $request->remark,
            "is_waybill" => $request->input("is_waybill", '0'),
            "created_by" => auth()->id(),
        ]);
        $lastInsertId = $transport->id;

        foreach ($request->branch ?? [] as $key => $value) {
            $transport->branches()->create([
                'transport_id'  => $lastInsertId,
                "branch"        => $value,
                "contact_no"    => $request->contact_no[$key],
            ]);
        }

        if ($request->ajax()) {
            return $this->withSuccess("Transport created successfully");
        }
        return $this->withSuccess("Transport created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transport $transport)
    {
        $request->validate([
            "name"       => "required|unique:transports,name," . $transport->id,
            "is_waybill" => "nullable"
        ]);

        $transport->update([
            "name"       => $request->name,
            "remark"     => $request->remark,
            "is_waybill" => $request->input("is_waybill") ?? 0
        ]);

        $transport->branches()->delete();
        foreach ($request->branch ?? [] as $key => $value) {
            $transport->branches()->create([
                'transport_id'  => $transport->id,
                "branch"        => $value,
                "contact_no"    => $request->contact_no[$key],
            ]);
        }

        if ($request->ajax()) {
            return $this->withSuccess("Transport Updated successfully");
        }
        return $this->withSuccess("Transport Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transport $transport)
    {
        $transport->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Transport delete successfully");
        }
        return $this->withSuccess("Transport delete successfully")->back();
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
        $this->model(model: Transport::class, with: [ "createdBy" ]);

        /* Add Filter here */
        $this->filter([
            // "status" => $request->status,
        ]);


        $editPermission   = $this->hasPermission("transport-edit");
        $deletePermission = $this->hasPermission("transport-delete");

        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.transport.delete", [ 'transport' => $row->id ]);
            $action = "";
            if ($editPermission) {
                $branches = json_encode(TransportDetail::where("transport_id", $row->id)->pluck("branch")->all());
                $contact = json_encode(TransportDetail::where("transport_id", $row->id)->pluck("contact_no")->all());
                $action .= " <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                    data-id='{$row->id}'
                                    data-name='{$row->name}'
                                    data-remark='{$row->remark}'
                                    data-is_waybill='{$row->is_waybill}'
                                    data-branches='{$branches}'
                                    data-contact_nos='{$contact}'
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
                "id"         => $row->id,
                "action"     => $action,
                "name"       => $row->name,
                "branch"     => $row->branch,
                "contact_no" => $row->contact_no,
                "is_waybill" => $row->wayBillLabel(),
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
