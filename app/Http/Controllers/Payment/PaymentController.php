<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\Payment;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class PaymentController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:payment-create', only: ['create']),
            new Middleware('permission:payment-view', only: ['index', "getList"]),
            new Middleware('permission:payment-edit', only: ['edit', "update"]),
            new Middleware('permission:payment-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::get();
        $banks = Bank::get();
        return view("payment.index", compact('customers', 'banks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "amount" => "required|numeric",
            "payment_type" => [
                "required",
                Rule::in(['CASH', 'CREDIT_CARD', 'DEBIT_CARD', 'VOUCHER', 'CHEQUE']),
            ],
            "type" => "required|in:DEBIT,CREDIT",
            "remark" => "nullable",
            "number" => in_array($request->payment_type, ['CHEQUE', 'CREDIT_CARD', 'DEBIT_CARD', 'VOUCHER']) ? 'required' : 'nullable',
            "date" => "required|date",
            "customer_id" => "required",
            "bank_id" => 'required',
        ]);

        Payment::create([
            "customer_id" => $request->customer_id,
            "bank_id" => $request->bank_id,
            "amount" => $request->amount,
            "payment_type" => $request->payment_type,
            "type" => $request->type,
            "remark" => $request->remark,
            "number" => $request->number,
            "date" => $request->date,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Payment created successfully");
        }
        return $this->withSuccess("Payment created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            "amount" => "required|numeric",
            "payment_type" => [
                "required",
                Rule::in(['CASH', 'CREDIT_CARD', 'DEBIT_CARD', 'VOUCHER', 'CHEQUE']),
            ],
            "type" => "required|in:DEBIT,CREDIT",
            "remark" => "nullable",
            "number" => in_array($request->payment_type, ['CHEQUE', 'CREDIT_CARD', 'DEBIT_CARD', 'VOUCHER']) ? 'required' : 'nullable',
            "date" => "required|date",
            "customer_id" => "required",
            "bank_id" => 'required',
        ]);

        $payment->update([
            "amount" => $request->amount,
            "payment_type" => $request->payment_type,
            "type" => $request->type,
            "remark" => $request->remark,
            "bank_id" => $request->bank_id,
            "customer_id" => $request->customer_id,
            "date" => $request->date,
            'number' => $request->number,
            "customer_id" => $request->customer_id
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Payment Updated successfully");
        }
        return $this->withSuccess("Payment Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Payment Deleted successfully");
        }
        return $this->withSuccess("Payment Deleted successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'amount',
            'payment_type',
            'type',
            'remark',
            'date',
            'number',
            'customer_id',
        ];

        $this->model(model: Payment::class);


        $editPermission = $this->hasPermission("payment-edit");
        $deletePermission = $this->hasPermission("payment-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("payment.delete", ['payment' => $row->id]);
            $action = "";

            if ($editPermission) {
                $action .= "
                            <a class='btn edit-btn  btn-action bg-success text-white me-2' 
                                data-id='{$row->id}'
                                data-amount='{$row->amount}'
                                data-payment_type='{$row->payment_type}'
                                data-type='{$row->type}'
                                data-date='{$row->date}'
                                data-remark='{$row->remark}'
                                data-number='{$row->number}'
                                data-customer_id='{$row->customer_id}'
                                data-customer_name='{$row->customer?->name}'
                                data-number='{$row->number}'
                                data-bank_id='{$row->bank_id}'
                                data-bank_name='{$row->bank?->name}'
                                data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                                <i class='far fa-edit' aria-hidden='true'></i>
                            </a>
                        ";
            }
            if ($deletePermission) {
                $action .= "
                            <a class='btn btn-action bg-danger text-white me-2 btn-delete'
                                data-id='{$row->id}'
                                data-bs-toggle='tooltip'
                                data-bs-placement='top' data-bs-original-title='Delete'
                                href='{$delete}'>
                                <i class='fa-solid fa-trash'></i>
                            </a>
                        ";
            }

            return [
                "id" => $row->id,
                "amount" => $row->amount,
                "date" => $row->date,
                "customer_id" => $row->customer?->name . ' - (' . $row->customer?->city?->name . ' - ' . $row->customer?->partyType?->name . ')',
                "bank_id" => $row->bank?->name ?? '',
                "payment_type" => $row->payment_type,
                "type" => $row->type,
                "remark" => $row->remark ?? '',
                "number" => $row->number ?? '',
                "action" => $action,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
