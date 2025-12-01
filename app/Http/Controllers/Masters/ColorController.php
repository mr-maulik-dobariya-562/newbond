<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ColorController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:color-create', only: ['create']),
            new Middleware('permission:color-view', only: ['index', "getList"]),
            new Middleware('permission:color-edit', only: ['edit', "update"]),
            new Middleware('permission:color-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Master::color.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "color" => "required|unique:colors,color",
            "font_color" => "required|unique:colors,font_color",
        ]);

        Color::create([
            "color"       => $request->color,
            "font_color"       => $request->font_color,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Color created successfully");
        }
        return $this->withSuccess("Color created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Color $color)
    {
        $request->validate([
            "color" => "required|unique:colors,color," . $color->id,
            "font_color" => "required|unique:colors,font_color," . $color->id,
        ]);

        $color->update([ "color" => $request->color, "font_color" => $request->font_color ]);

        if ($request->ajax()) {
            return $this->withSuccess("Color Updated successfully");
        }
        return $this->withSuccess("Color Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        $color->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Color delete successfully");
        }
        return $this->withSuccess("Color delete successfully")->back();
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
        $this->model(model: Color::class, with: [ "createdBy" ]);

        /* Add Filter here */
        $this->filter([
            // "status" => $request->status,
        ]);

        $editPermission   = $this->hasPermission("color-edit");
        $deletePermission = $this->hasPermission("color-delete");
        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use($editPermission, $deletePermission) {
            $delete = route("master.color.delete", [ 'color' => $row->id ]);
            $action = "";
            if ($editPermission) {
                $action = " <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                data-id='{$row->id}'
                                data-color='{$row->color}'
                                data-font_color='{$row->font_color}'
                                data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                                <i class='far fa-edit' aria-hidden='true'></i>
                            </a>
                        ";
            }

            if ($deletePermission) {
                $action .= " <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-bs-toggle='tooltip'
                            data-bs-placement='top' data-bs-original-title='Delete'
                            href='{$delete}'>
                            <i class='fa-solid fa-trash'></i>
                        </a>
                        ";
            }
            return [
                "id"         => $row->id,
                "color"      => $row->color,
                "font_color" => $row->font_color,
                "action"     => $action,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
