<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\PartyType;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PartyTypeController extends Controller implements HasMiddleware
{
	use DataTable;

	public static function middleware(): array
	{
		return [
			new Middleware('permission:party_type-create', only: ['create']),
			new Middleware('permission:party_type-view', only: ['index', "getList"]),
			new Middleware('permission:party_type-edit', only: ['edit', "update"]),
			new Middleware('permission:party_type-delete', only: ['destroy']),
		];
	}

	/**
		* Display a listing of the resource.
		*/
	public function index()
	{
		return view("Master::party_type.index");
	}

	/**
		* Store a newly created resource in storage.
		*/
	public function store(Request $request)
	{
		// $request->validate([
		// "name" => "required|unique:party_types,name",
		// ]);

		// PartyType::create([
		// "name" => $request->name,
		// "created_by" => auth()->id()
		// ]);

		// if ($request->ajax()) {
		// return $this->withSuccess("Party type created successfully");
		// }
		// return $this->withSuccess("Party type created successfully")->back();
	}

	/**
		* Update the specified resource in storage.
		*/
	public function update(Request $request, PartyType $partyType)
	{
		$request->validate([
		    "color" => "required",
		]);

		$partyType->update(["color" => $request->color]);

		if ($request->ajax()) {
		return $this->withSuccess("Party Type Updated successfully");
		}
		return $this->withSuccess("Party Type Updated successfully")->back();
	}

	/**
		* Remove the specified resource from storage.
		*/
	public function destroy(PartyType $partyType)
	{
		// $partyType->delete();
		// if (request()->ajax()) {
		// return $this->withSuccess("party type delete successfully");
		// }
		// return $this->withSuccess("party type delete successfully")->back();
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
		$this->model(model: PartyType::class, with: ["createdBy"]);

		/* Add Filter here */
		$this->filter([
			// "status" => $request->status,
		]);


		$editPermission = $this->hasPermission("party_type-edit");
		$deletePermission = $this->hasPermission("party_type-delete");

		/* Add Formatting here */
		$this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
			$delete = route("master.party-type.delete", ['party_type' => $row->id]);
			$action = "";
			if ($editPermission) {
				$action .= 
				" <a class='btn edit-btn  btn-action bg-success text-white me-2'
                            data-id='{$row->id}'
                            data-color='{$row->color}'
                            data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                            <i class='far fa-edit' aria-hidden='true'></i>
                        </a>";
			}
			if ($deletePermission) {
				$action .= "";
				// " <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-bs-toggle='tooltip'
                //     data-bs-placement='top' data-bs-original-title='Delete'
                //     href='{$delete}'>
                //     <i class='fa-solid fa-trash'></i>
                // </a>";
			}
			$checked = $row->item_discount == 1 ? 'checked' : '';
			$item = '<label class="form-check form-switch">
                        <input class="form-check-input check-item" data-party_id="' . $row->id . '" type="checkbox" ' . $checked . '>
                    </label>';
			$itemPrice = '<select class="form-select itemPrice" data-party_id="' . $row->id . '">
                            <option value="Dealer" '.($row->item_price == 'Dealer' ? 'selected' : '').'>Dealer</option>
                            <option value="Retailer" '.($row->item_price == 'Retailer' ? 'selected' : '').'>Retailer</option>
                            <option value="USD" '.($row->item_price == 'USD' ? 'selected' : '').'>USD</option>
                        </select>';
			$ExtraPrice = '<select class="form-select extraPrice" data-party_id="' . $row->id . '">
                            <option value="INR" '.($row->extra_price == 'INR' ? 'selected' : '').'>INR</option>
                            <option value="USD" '.($row->extra_price == 'USD' ? 'selected' : '').'>USD</option>
                            <option value="NON" '.($row->extra_price == 'NON' ? 'selected' : '').'>NON</option>
                        </select>';
			return [
				"id" => $row->id,
				"name" => $row->name,
				"item" => $item,
                "item_price" => $itemPrice,
                "extra_price" => $ExtraPrice,
                "color" => $row->color,
				"action" => $action,
				"created_by" => $row->createdBy?->displayName(),
				"created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
				"updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
			];
		});
		return $this->getListAjax($searchableColumns);
	}

	public function checkItem(Request $request)
	{
		if ($request->id && $request->item == 'true') {
			$party = PartyType::where("id", $request->id)->update(['item_discount' => 1]);
		}else{
			$party = PartyType::where("id", $request->id)->update(['item_discount' => 0]);
		}
		return $this->withSuccess("Party Type Updated successfully");
	}

	public function itemPrice(Request $request)
	{
		if ($request->id && $request->item) {
			PartyType::where("id", $request->id)->update(['item_price' => $request->item]);
		}
		return $this->withSuccess("Item Price Updated successfully");
	}

	public function extraPrice(Request $request)
	{
		if ($request->id && $request->item) {
			PartyType::where("id", $request->id)->update(['extra_price' => $request->item]);
		}
		return $this->withSuccess("Extra Price Updated successfully");
	}
}
