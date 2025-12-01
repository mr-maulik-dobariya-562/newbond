<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\InvoiceType;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class InvoiceTypeController extends Controller implements HasMiddleware
{
    use DataTable;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:invoice-type-create', only: ['create']),
            new Middleware('permission:invoice-type-view', only: ['index', "getList"]),
            new Middleware('permission:invoice-type-edit', only: ['edit', "update"]),
            new Middleware('permission:invoice-type-delete', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Master::invoice_type.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|unique:invoice_types,name",
        ]);

        InvoiceType::create([
            "name"       => $request->name,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Invoice Type created successfully");
        }
        return $this->withSuccess("Invoice Type created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvoiceType $invoiceType)
    {
        $request->validate([
            "name" => "required|unique:invoice_types,name," . $invoiceType->id,
        ]);

        $invoiceType->update([ "name" => $request->name ]);

        if ($request->ajax()) {
            return $this->withSuccess("Invoice Type Updated successfully");
        }
        return $this->withSuccess("Invoice Type Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvoiceType $invoiceType)
    {
        $invoiceType->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Invoice Type delete successfully");
        }
        return $this->withSuccess("Invoice Type delete successfully")->back();
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
        $this->model(model: InvoiceType::class, with: [ "createdBy" ]);

        /* Add Filter here */
        $this->filter([
            // "status" => $request->status,
        ]);

        $editPermission   = $this->hasPermission("invoice-type-edit");
        $deletePermission = $this->hasPermission("invoice-type-delete");
        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.invoice-type.delete", [ 'invoiceType' => $row->id ]);
            
            $action = "";
            if ($editPermission) {
                $action .= " <a class='btn edit-btn  btn-action bg-success text-white me-2' 
                                data-id='{$row->id}'
                                data-name='{$row->name}'
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
                "name"       => $row->name,
                "action"     => $action,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
