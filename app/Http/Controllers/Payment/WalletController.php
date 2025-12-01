<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WalletController as ControllersWalletController;
use App\Models\Customer;
use App\Models\TxnType;
use App\Models\Wallet;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use function Pest\Laravel\json;

class WalletController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:wallet-create', only: ['create']),
            new Middleware('permission:wallet-view', only: ['index', "getList"]),
            new Middleware('permission:wallet-edit', only: ['edit', "update"]),
            new Middleware('permission:wallet-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::get();
        $txnTypes = TxnType::get();
        return view("wallet.index", compact('customers', 'txnTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "amount" => "required|numeric",
            "type" => "required|in:DEBIT,CREDIT",
            "remark" => "nullable",
            "date" => "required|date",
            "customer_id" => "required",
        ]);

        $wallet = new ControllersWalletController();
        if ($request->type == 'DEBIT') {
            $data = $wallet->debit(
                $request->customer_id,
                $request->amount,
                1,
                1,
                'Manually Debit By Admin',
                date('Y-m-d')
            );
        } else {
            $data = $wallet->credit(
                $request->customer_id,
                $request->amount,
                1,
                1,
                'Manually Credit By Admin',
                date('Y-m-d')
            );
        }
        if ($data['status'] == true) {
            return $this->withSuccess("Payment created successfully");
        } else {
            return $this->withError($data['message']);
        }
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'amount',
            'type',
            'remark',
            'date',
        ];

        $this->model(model: Wallet::class, with: ["customer", "customer.city", "customer.partyType"]);

        $this->filter([
            'user_id' => $request->customer_id,
            'type' => $request->type,
            'txn_type_id' => $request->txn_type,
        ]);

        $this->enableDateFilters('date');

        $editPermission = $this->hasPermission("payment-edit");
        $deletePermission = $this->hasPermission("payment-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {

            return [
                "id" => $row->id,
                "date" => $row->date,
                "amount" => $row->amount,
                "balance" => $row->balance,
                "txn_type" => $row->txnType?->name,
                "type" => $row->type,
                "remark" => $row->remark ?? '',
                "created_by" => $row->createdBy?->name . ' - (' . @$row->customer->city->name . ' - ' . @$row->customer->partyType->name . ')',
                "created_at" => $row->created_at ? $row->created_at : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
