<?php

namespace App\Http\Controllers\User;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FeedbackController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:feedback-create', only: ['create']),
            new Middleware('permission:feedback-view', only: ['index', "getList"]),
            new Middleware('permission:feedback-edit', only: ['edit', "update"]),
            new Middleware('permission:feedback-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('User::feedback.index');
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
        ];

        $this->model(Feedback::class);

        $this->formateArray(function ($row, $index) {

            if($row->image){
                $url = FileUpload::url($row->image, "feedback");
                $image = $url ? "<img src='{$url}' style='width:70px; height:50px;'/>" : "<img src='https://via.placeholder.com/50' style='width:70px; height:50px;'/>";
            }else{
                $image = "";
            }

            return [
                "id"            => $row->id,
                "title"         => $row->title,
                "message"       => $row->message,
                "image"         => $image,
                "created_by"    => $row->createdBy?->displayName(),
                "created_at"    => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at"    => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
