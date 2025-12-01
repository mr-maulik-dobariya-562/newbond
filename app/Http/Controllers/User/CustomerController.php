<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\PartyType;
use App\Models\Transport;
use App\Models\Area;
use App\Models\BillAddress;
use App\Models\BillGroup;
use App\Models\City;
use App\Models\PartyCategory;
use App\Models\PartyGroup;
use App\Models\State;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller implements HasMiddleware
{
    use DataTable;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:customer-create', only: ['create']),
            new Middleware('permission:customer-view', only: ['index', "getList"]),
            new Middleware('permission:customer-edit', only: ['edit', "update"]),
            new Middleware('permission:customer-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partyTypes = PartyType::all();
        $partyGroups = PartyGroup::all();
        $partyNames = Customer::select('name', 'id')->get();
        $partyCategorys = PartyCategory::all();
        $states = State::all();
        $citys = City::all();
        $discount = Customer::where('discount', '>', 0)->select('discount')->distinct()->pluck('discount');
        return view("User::customer.index", compact('discount', 'partyTypes', 'partyGroups', 'partyCategorys', 'partyNames', 'states', 'citys'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $partyTypes = PartyType::all();
        $partyGroups = PartyGroup::all();
        $transport = Transport::all();
        $couriers = Courier::all();
        $areas = Area::all();
        $billGroups = BillGroup::all();
        $parentUser = Customer::all();
        $partyCategorys = PartyCategory::all();
        return view("User::customer.create", compact('partyTypes', 'transport', 'couriers', 'areas', 'partyGroups', 'billGroups', 'parentUser', 'partyCategorys'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'mobile' => ['required', 'unique:customer,mobile', 'numeric', 'digits:10'],
            "status" => ["required", "in:ACTIVE,INACTIVE"],
            "country_id" => ["required"],
            "state_id" => ["required"],
            "city_id" => ["required"],
            "address" => ["nullable"],
            "contact_person" => ["nullable"],
            "area" => ["nullable"],
            "pincode" => ["required"],
            "pay_terms" => ["nullable"],
            "party_type_id" => ["required"],
            'gst' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $pattern = '/^([0]{1}[1-9]{1}|[1-2]{1}[0-9]{1}|[3]{1}[0-7]{1})[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/';
                        if (!preg_match($pattern, $value)) {
                            $fail('The ' . $attribute . ' is not in the correct format.');
                        }
                    }
                }
            ],
            "pan_no" => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $pattern = '/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/';
                        if (!preg_match($pattern, $value)) {
                            $fail('The ' . $attribute . ' is not in the correct format.');
                        }
                    }
                }
            ],
            "price" => ["nullable"],
            "discount" => ["nullable"],
            /* Other details */
            "other_sample" => ['nullable'],
            "other_courier_id" => ['nullable'],
            "other_transport_id" => ['nullable'],
            "bill_group_id" => ['nullable'],
            "other_reason_remark" => ['nullable'],
            "reference" => ['nullable'],
            "parent_id" => ['nullable'],
        ]);

        DB::beginTransaction();

        try {
            $validated['password'] = $request->password;
            $validated['email'] = $request->email;
            $validated['created_by'] = auth()->id();
            if ($request->filled("city_id"))
                $validated['city_id'] = findOrCreate(City::class, "name", $request->input("city_id"));
            if ($request->filled("transport_id"))
                $validated['transport_id'] = findOrCreate(Transport::class, "name", $request->input("transport_id"));
            if ($request->filled("courier_id"))
                $validated['courier_id'] = findOrCreate(Courier::class, "name", $request->input("courier_id"));
            if ($request->filled("party_group_id"))
                $validated['party_group_id'] = findOrCreate(PartyGroup::class, "name", $request->input("party_group_id"));
            if ($request->filled("bill_group_id"))
                $validated['bill_group_id'] = findOrCreate(BillGroup::class, "name", $request->input("bill_group_id"));

            $customer = Customer::create($validated);

            foreach ($request->firm_name as $key => $value) {
                if (!empty($value)) {
                    BillAddress::Create(["firm_name" => $value, "customer_id" => $customer->id, "gst_no" => $request->gst_no[$key], "pan_no" => $request->panNo[$key], "address" => $request->address1[$key]]);
                }
            }

            if ($request->party_type_id == 2) {
                $wallet = new \App\Http\Controllers\WalletController();
                $wallet->credit($customer->id, 600, $customer->id, 2, 'New Customer Added', date('Y-m-d'));
            }

            DB::commit();

            if ($request->ajax()) {
                return $this->withSuccess("Customer added successfully");
            }
            return $this->withSuccess("Customer added successfully")->back();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $partyTypes = PartyType::all();
        $partyGroups = PartyGroup::all();
        $transport = Transport::all();
        $couriers = Courier::all();
        $areas = Area::all();
        $customerDetails = BillAddress::where('customer_id', $customer->id)->get();
        $billGroups = BillGroup::all();
        $parentUser = Customer::all();
        $partyCategorys = PartyCategory::all();
        return view("User::customer.create", compact('partyTypes', 'transport', 'couriers', 'areas', 'customer', 'partyGroups', 'customerDetails', 'billGroups', 'parentUser', 'partyCategorys'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'mobile' => ['required', 'unique:customer,mobile,' . $customer->id, 'numeric', 'digits:10'],
            "status" => ["required", "in:ACTIVE,INACTIVE"],
            "country_id" => ["required"],
            "state_id" => ["required"],
            "city_id" => ["required"],
            "address" => ["nullable"],
            "contact_person" => ["nullable"],
            "area" => ["nullable"],
            "pincode" => ["required"],
            "pay_terms" => ["nullable"],
            "bill_type" => ["nullable"],
            "party_type_id" => ["required"],
            "gst" => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $pattern = '/^([0]{1}[1-9]{1}|[1-2]{1}[0-9]{1}|[3]{1}[0-7]{1})[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/';
                        if (!preg_match($pattern, $value)) {
                            $fail('The ' . $attribute . ' is not in the correct format.');
                        }
                    }
                }
            ],
            "pan_no" => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $pattern = '/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/';
                        if (!preg_match($pattern, $value)) {
                            $fail('The ' . $attribute . ' is not in the correct format.');
                        }
                    }
                }
            ],
            "price" => ["nullable"],
            "discount" => ["nullable"],
            /* Other details */
            "other_sample" => ['nullable', 'in:Yes,No'],
            "other_courier_id" => ['nullable'],
            "other_transport_id" => ['nullable'],
            "bill_group_id" => ['nullable'],
            "other_reason_remark" => ['nullable'],
            "reference" => ['nullable'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['password'] = $request->password;
        $validated['email'] = $request->email;
        $validated['parent_id'] = $request->parent_id ?? NULL;
        if ($request->filled("city_id"))
            $validated['city_id'] = findOrCreate(City::class, "name", $request->input("city_id"));
        if ($request->filled("transport_id"))
            $validated['transport_id'] = findOrCreate(Transport::class, "name", $request->input("transport_id"));
        if ($request->filled("courier_id"))
            $validated['courier_id'] = findOrCreate(Courier::class, "name", $request->input("courier_id"));
        if ($request->filled("party_group_id"))
            $validated['party_group_id'] = findOrCreate(PartyGroup::class, "name", $request->input("party_group_id"));
        if ($request->filled("bill_group_id"))
            $validated['bill_group_id'] = findOrCreate(BillGroup::class, "name", $request->input("bill_group_id"));
        // dd($validated);
        $customer->update($validated);

        $deleteQ = BillAddress::where('customer_id', $customer->id)->get();

        foreach ($deleteQ as $deleteR) {
            if (!in_array($deleteR['id'], $request->customer_detail_id)) {
                $report = BillAddress::where(['id' => $deleteR['id'], 'customer_id' => $customer->id])->first();
                if ($report) {
                    $report->delete();
                }
            }
        }

        foreach ($request->firm_name as $key => $value) {
            if (!empty($value)) {
                if (isset($request->customer_detail_id[$key])) {
                    BillAddress::where(["customer_id" => $customer->id, "id" => $request->customer_detail_id[$key]])
                        ->update(["firm_name" => $value, "customer_id" => $customer->id, "gst_no" => $request->gst_no[$key], "pan_no" => $request->panNo[$key], "address" => $request->address1[$key]]);
                } else {
                    BillAddress::Create(["firm_name" => $value, "customer_id" => $customer->id, "gst_no" => $request->gst_no[$key], "pan_no" => $request->panNo[$key], "address" => $request->address1[$key]]);
                }
            }
        }

        if ($request->ajax()) {
            return $this->withSuccess("Customer Updated successfully");
        }
        return $this->withSuccess("Customer Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Customer Deleted successfully");
        }
        return $this->withSuccess("Customer Deleted successfully")->back();
    }

    // public function getList()
    // {
    //     $searchableColumns = [
    //         'id',
    //         'name',
    //         "mobile",
    //         "email",
    //     ];

    //     $this->model(model: Customer::class, with: ["createdBy", 'partyType','city']);


    //     $editPermission   = $this->hasPermission("customer-edit");
    //     $deletePermission = $this->hasPermission("customer-delete");

    //     $this->formateArray(function (Customer $row, $index) use ($editPermission, $deletePermission) {
    //         $delete = route("customer.delete", ['customer' => $row->id]);
    //         $edit   = route("customer.edit", ['customer' => $row->id]);
    //         $action = "";
    //         $checkbox = "";
    //         if ($editPermission) {
    //             $action .= "<a class='btn edit-btn  btn-action bg-success text-white m-1'
    //                             data-bs-toggle='tooltip'
    //                             data-bs-placement='top'
    //                             data-bs-original-title='Edit'
    //                             href='$edit'>
    //                             <i class='far fa-edit' aria-hidden='true'></i>
    //                         </a>";
    //         }

    //         if ($deletePermission) {
    //             $action .= "<a
    //                         class='btn btn-action bg-danger text-white m-1 btn-delete'
    //                         data-bs-toggle='tooltip'
    //                         data-bs-placement='top'
    //                         data-bs-original-title='Delete'
    //                         href='{$delete}'>
    //                         <i class='fa-solid fa-trash'></i>
    //                     </a>";
    //         }
    //         $checkbox = '<td><input type="checkbox" class="print-check" name="id[]" value="{{ $row["id"] }}"></td>';
    //         return [
    //             "checkbox"      => $checkbox,
    //             "id"            => $row->id,
    //             "action"        => $action,
    //             "name"          => $row->name,
    //             "city"          => $row?->city?->name,
    //             "mobile"        => $row->mobile,
    //             "address"       => $row->address,
    //             "party_type_id" => $row?->partyType?->name,
    //             "status"        => $row->status(),
    //             "email"         => $row->email,
    //             "created_by"    => $row->createdBy?->displayName(),
    //             "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
    //             "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
    //         ];
    //     });
    //     return $this->getListAjax($searchableColumns);
    // }

    public function getList(Request $request)
    {
        $data = Customer::with(['city', 'state', 'partyGroup', 'courier', 'transport', 'parent', 'country', 'partyType', 'createdBy'])->select('customer.*');

        if ($request->party_group) {
            $data->where('customer.party_group_id', $request->party_group);
        }

        if ($request->party_type) {
            $data->where('customer.party_type_id', $request->party_type);
        }

        if ($request->createdBy) {
            $data->where('customer.created_by', $request->createdBy);
        }

        if ($request->status) {
            $data->where('customer.status', $request->status);
        }

        if ($request->bill_type) {
            $data->where('customer.bill_type', $request->bill_type);
        }

        if ($request->sample) {
            $data->where('customer.other_sample', $request->sample);
        }

        if ($request->partyId) {
            $data->where('customer.id', $request->partyId);
        }

        if ($request->city) {
            $data->whereIn('customer.city_id', $request->city);
        }

        if ($request->state) {
            $data->whereIn('customer.state_id', $request->state);
        }

        if (!empty($request->fromDate)) {
            $data->whereDate('customer.created_at', '>=', $request->fromDate);
        }
        if (!empty($request->toDate)) {
            $data->whereDate('customer.created_at', '<=', $request->toDate);
        }
        if (!empty($request->discount)) {
            $data->where('customer.discount', $request->discount);
        }

        $editPermission = $this->hasPermission("customer-edit");
        $deletePermission = $this->hasPermission("customer-delete");

        return DataTables::of($data)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="print-check" name="id[]" value="' . $row->id . '">';
            })
            ->addColumn('city', function ($row) {
                return $row->city ? $row?->city?->name : 'N/A'; // Assuming 'name' is a field in the City model
            })
            ->addColumn('state', function ($row) {
                return $row->state ? $row?->state?->name : 'N/A'; // Assuming 'name' is a field in the City model
            })
            ->addColumn('sample', function ($row) {
                return $row->sample ? $row?->sample : 'N/A'; // Assuming 'name' is a field in the City model
            })
            ->addColumn('country', function ($row) {
                return $row->country ? $row?->country?->name : 'N/A'; // Assuming 'name' is a field in the City model
            })
            ->addColumn('courier', function ($row) {
                return $row->courier ? $row?->courier?->name : 'N/A'; // Assuming 'name' is a field in the City model
            })
            ->addColumn('transport', function ($row) {
                return $row->transport ? $row?->transport?->name : 'N/A'; // Assuming 'name' is a field in the City model
            })
            ->addColumn('partyGroup', function ($row) {
                return $row->partyGroup ? $row?->partyGroup?->name : 'N/A'; // Assuming 'name' is a field in the City model
            })
            ->addColumn('courier', function ($row) {
                return $row->courier ? $row?->courier?->name : 'N/A'; // Assuming 'name' is a field in the City model
            })
            ->addColumn('party_type_id', function ($row) {
                return $row->party_type_id ? $row?->partyType?->name : 'N/A'; // Assuming 'name' is a field in the City model
            })
            ->addColumn('partyCategory', function ($row) {
                return $row->partyCategory ? $row?->partyCategory?->name : 'N/A'; // Assuming 'name' is a field in the City model
            })
            ->addColumn('created_by', function ($row) {
                return $row->created_by ? $row?->createdBy?->name : 'N/A'; // Assuming 'name' is a field in the City model
            })
            ->addColumn('parent', function ($row) {
                return $row->created_by ? $row?->parent?->name : 'N/A'; // Assuming 'name' is a field in the City model
            })
            ->addColumn('action', function ($row) use ($editPermission, $deletePermission) {
                $delete = route("customer.delete", ['customer' => $row->id]);
                $edit = route("customer.edit", ['customer' => $row->id]);
                $action = "";
                if ($editPermission) {
                    $action .= "<a class='btn edit-btn btn-sm btn-action bg-success text-white'
                                    data-bs-toggle='tooltip'
                                    data-bs-placement='top'
                                    data-bs-original-title='Edit'
                                    href='$edit'>
                                    <i class='far fa-edit' aria-hidden='true'></i>
                                </a>";
                }

                if ($deletePermission) {
                    $action .= "<a
                                class='btn btn-action bg-danger btn-sm text-white btn-delete m-1'
                                data-bs-toggle='tooltip'
                                data-bs-placement='top'
                                data-bs-original-title='Delete'
                                href='{$delete}'>
                                <i class='fa-solid fa-trash'></i>
                            </a>";
                }
                return $action;
            })
            ->rawColumns(['checkbox', 'action'])
            ->make(true);
    }

    public function coverPrint(Request $request)
    {
        if ($request->id != '') {
            $customer = Customer::with('city', 'state')->whereIn('id', $request->id)->get();
            return view('User::customer.cover_print', compact('customer'));
        } else {
            if (request()->ajax()) {
                return $this->withSuccess("Please Select Customer");
            }
            return $this->withSuccess("Please Select Customer")->back();
        }
    }
}
