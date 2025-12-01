<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\City;
use App\Models\Country;
use App\Models\Customer;
use App\Models\ItemGroup;
use App\Models\PartyType;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{
    public function getCountrySelect2(Request $request)
    {
        $data = Country::where(function ($query) use ($request) {
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            if ($request->has('id')) {
                $query->where('id', $request->id);
            }
        })
            ->get(['id', 'name as text']);
        return response()->json($data);
    }
    public function getCitySelect2(Request $request)
    {
        if (!$request->has('prevent') && !$request->has('state_id')) {
            return response()->json([]);
        }
        $cities = City::where(function ($query) use ($request) {

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->has('state_id')) {
                $query->where('state_id', $request->state_id);
            }
        })
            ->get(['id', 'name as text']);
        return response()->json($cities);
    }

    public function getStateSelect2(Request $request)
    {

        if (!$request->has('prevent') &&  !$request->has('country_id')) {
            // return response()->json([]);
        }
        $Provinces = State::where(function ($query) use ($request) {

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->has('country_id')) {
                $query->where('country_id', $request->country_id);
            }
        })
            ->get(['id', 'name as text']);
        return response()->json($Provinces);
    }

    public function getGroupSelect2(Request $request)
    {

        $data = ItemGroup::where(function ($query) use ($request) {
            if ($request->has('search')) {
                $query->where('group_name', 'like', '%' . $request->search . '%');
            }
            if ($request->has('id')) {
                $query->where('id', $request->id);
            }
        })
            ->get(['id', 'group_name as text']);
        return response()->json($data);
    }

    public function getPartyTypeSelect2(Request $request)
    {

        $data = PartyType::where(function ($query) use ($request) {
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            if ($request->has('id')) {
                $query->where('id', $request->id);
            }
        })
            ->get(['id', 'name as text']);
        return response()->json($data);
    }

    public function getRedeemCoin(Request $request)
    {
        return response()->json(Customer::with('partyType')->where('id', $request->customer_id)->first());
    }

    public function testing()
    {
        $wallet = new \App\Http\Controllers\WalletController();
        $data = $wallet->credit(1, 200, 1, 1, 'test', date('Y-m-d'));
        $data = $wallet->debit(1, 100, 1, 1, 'test', date('Y-m-d'));
        pre($data);
    }

    public function partyData(Request $request)
	{
		$data = DB::table('customer')
        ->join('cities', 'customer.city_id', '=', 'cities.id')
        ->join('party_types', 'customer.party_type_id', '=', 'party_types.id')
        ->join('party_groups', 'customer.party_group_id', '=', 'party_groups.id')
        ->join('party_categorys', 'customer.bill_type', '=', 'party_categorys.id')
        ->where(function ($query) use ($request) {
            if ($request->has('search')) {
                $query->where('customer.name', 'like', "{$request->search}%");
            }
            if ($request->has('q')) {
                $query->where('customer.name', 'like', "{$request->q}%");
            }
            if ($request->has('id')) {
                $query->where('customer.id', $request->id);
            }
        })
        ->where('customer.party_type_id', '!=', '4')
        ->where('customer.status', '!=', 'INACTIVE')
        ->where('customer.branch_id', '=', session('branch_id'))
        ->when($request->page, function ($query) use ($request) {
            $query->offset(($request->page - 1) * 10)->limit(20);
            return $query;
        })
        ->orderBy('customer.id', 'desc')
        ->limit(20)
        ->get([
            'customer.id', // Specify the table for the id
            DB::raw("CONCAT(customer.name, ' -  (', cities.name, ' - ', party_types.name,' ) ') as text"),
            'customer.discount',
            'customer.party_type_id',
            'customer.balance',
            'party_groups.name as group_name',
            'party_categorys.name as category_name',
            'party_types.color',
        ]);
		return response()->json([ "item"=>$data, "total_count" => Customer::count()]);
	}

    public function partyDataAll(Request $request)
	{
		$data = DB::table('customer')
        ->join('cities', 'customer.city_id', '=', 'cities.id')
        ->join('party_types', 'customer.party_type_id', '=', 'party_types.id')
        ->join('party_groups', 'customer.party_group_id', '=', 'party_groups.id')
        ->join('party_categorys', 'customer.bill_type', '=', 'party_categorys.id')
        ->where(function ($query) use ($request) {
            if ($request->has('search')) {
                $query->where('customer.name', 'like', "{$request->search}%");
            }
            if ($request->has('q')) {
                $query->where('customer.name', 'like', "{$request->q}%");
            }
            if ($request->has('id')) {
                $query->where('customer.id', $request->id);
            }
        })
        ->where('customer.party_type_id', '!=', '4')
        ->where('customer.branch_id', '=', session('branch_id'))
        ->when($request->page, function ($query) use ($request) {
            $query->offset(($request->page - 1) * 10)->limit(20);
            return $query;
        })
        ->orderBy('customer.id', 'desc')
        ->limit(20)
        ->get([
            'customer.id', // Specify the table for the id
            DB::raw("CONCAT(customer.name, ' -  (', cities.name, ' - ', party_types.name,' ) ') as text"),
            'customer.discount',
            'customer.party_type_id',
            'party_groups.name as group_name',
            'party_categorys.name as category_name',
            'customer.balance',
            'party_types.color',
        ]);
		return response()->json([ "item"=>$data, "total_count" => Customer::count()]);
	}

    public function branchData(Request $request)
    {
        $branch = Branch::find($request->branch);
        session(['branch_id' => $branch->id]);
        return true;
    }
}
