<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FollowUp;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class FollowUpController extends Controller
{
    use DataTable;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:follow-up-create', only: ['create']),
            new Middleware('permission:follow-up-view', only: ['index', "getList", "calendarEvent"]),
            new Middleware('permission:follow-up-edit', only: ['edit', "update"]),
            new Middleware('permission:follow-up-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::all();
        return view("Order::follow-up", compact("customers"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function calendarEvent(Request $request)
    {
        if ($request->ajax()) {
            return FollowUp::with('customer:id,name as customer_name')->whereDate('date', '>=', $request->start)
                ->whereDate('date', '<=', $request->end)
                ->get([
                    'date as start',
                    'remark as title',
                    'status',
                    'customer_id',
                    'id as backgroundColor',
                    'id as borderColor',
                ])
                ->map(function ($item) {
                    $item->title = $item->customer->customer_name . " - " . $item->status;
                    if ($item->status == "pending") {
                        $item->backgroundColor = '#f39c12';
                        $item->borderColor = '#f39c12';
                    } else if ($item->status == "completed") {
                        $item->backgroundColor = '#00a65a';
                        $item->borderColor = '#00a65a';
                    } else if ($item->status == "cancelled") {
                        $item->backgroundColor = '#dd4b39';
                        $item->borderColor = '#dd4b39';
                    } else if ($item->status == "hold") {
                        $item->backgroundColor = '#00c0ef';
                        $item->borderColor = '#00c0ef';
                    } else if ($item->status == "inprogress") {
                        $item->backgroundColor = '#0073b7';
                        $item->borderColor = '#0073b7';
                    } else if ($item->status == "deferred") {
                        $item->backgroundColor = '#ff69b4';
                        $item->borderColor = '#ff69b4';
                    } else {
                        $item->backgroundColor = '#007bff';
                        $item->borderColor = '#00c0ef';
                    }
                    return $item;
                })
                ->toArray();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "customer_id" => "required",
            "date" => "required|date",
        ]);

        FollowUp::create([
            "customer_id" => $request->customer_id,
            "date" => $request->date,
            "remark" => $request->remark,
            "status" => $request->status,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("New follow up saved successfully");
        }
        return $this->withSuccess("New follow up saved successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FollowUp $followUp)
    {
        $request->validate([
            "customer_id" => "required",
            "date" => "required|date",
        ]);

        $followUp->update([
            "customer_id" => $request->customer_id,
            "date" => $request->date,
            "status" => $request->status,
            "remark" => $request->remark,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Country Updated successfully");
        }
        return $this->withSuccess("Country Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FollowUp $followUp)
    {
        $followUp->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Follow up Deleted successfully");
        }
        return $this->withSuccess("Follow up Deleted successfully")->back();
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
        $this->model(model: FollowUp::class, with: ["createdBy:id,name", "customer", "customer.city", "customer.partyType"]);

        /* Add Filter here */
        $this->filter([
            // "status" => $request->status,
        ]);

        $editPermission = $this->hasPermission("follow-up-edit");
        $deletePermission = $this->hasPermission("follow-up-delete");

        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("follow-up.delete", ['followUp' => $row->id]);
            $action = "";
            if ($editPermission) {
                $action = " <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                data-id='{$row->id}'
                                data-customer_id='{$row->customer_id}'
                                data-customer='{$row->customer?->name}'
                                data-status='{$row->status}'
                                data-date='{$row->date}'
                                data-remark='{$row->remark}'
                                data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                                <i class='far fa-edit' aria-hidden='true'></i>
                            </a>";
            }

            if ($deletePermission) {
                $action .= " <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-bs-toggle='tooltip'
                                data-bs-placement='top' data-bs-original-title='Delete'
                                data-ajax='true'
                                href='{$delete}'>
                                <i class='fa-solid fa-trash'></i>
                            </a>";
            }
            return [
                "id" => $row->id,
                "customer_id" => $row->customer?->name . ' - (' . @$row->customer->city->name . ' - ' . @$row->customer->partyType->name . ')',
                "date" => $row->date,
                "remark" => $row->remark,
                "status" => $row->status,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
                "action" => $action,
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
