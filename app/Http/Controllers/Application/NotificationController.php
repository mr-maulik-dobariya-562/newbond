<?php

namespace App\Http\Controllers\Application;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PartyGroup;
use App\Traits\DataTable;
use Google\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class NotificationController extends Controller implements HasMiddleware
{
    use DataTable;
    protected $projectId;
    protected $messagingUrl;
    protected $credentialsFilePath;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:notification-create', only: ['create']),
            new Middleware('permission:notification-view', only: ['index', "getList"]),
            new Middleware('permission:notification-edit', only: ['edit', "update"]),
            new Middleware('permission:notification-delete', only: ['destroy']),
        ];
    }

    public function __construct()
    {
        // Set your project ID and the path to your service account JSON file
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $this->credentialsFilePath = storage_path('app/public/firebase-adminsdk.json'); // Adjust the path to your JSON file
        $this->messagingUrl = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = PartyGroup::all();
        return view("Application::notification.index", compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'message'           => 'required',
            'party_group_id' => 'required',
            'title'          => 'required',
        ]);

        $validatedData['created_by'] = auth()->id();
        if ($request->hasFile('image')) {

            $file                   = $request->file('image');
            $validatedData['image'] = FileUpload::upload($file, 'notification', app("storage"));
        }

        // Save other form data
        Notification::create($validatedData);

        if ($request->ajax()) {
            return $this->withSuccess("Notification created successfully");
        }
        return $this->withSuccess("Notification created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        $validatedData = $request->validate([
            'message'           => 'required',
            'party_group_id' => 'required',
            'title'          => 'required',
        ]);

        $this->sendNotification($request);

        if ($request->hasFile('image')) {

            $file                   = $request->file('image');
            $validatedData['image'] = FileUpload::upload($file, 'notification', app("storage"));
        }

        // Update other form data
        $notification->update($validatedData);

        if ($request->ajax()) {
            return $this->withSuccess("Notification Updated successfully");
        }
        return $this->withSuccess("Notification Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Notification delete successfully");
        }
        return $this->withSuccess("Notification delete successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'title',
            "body",
        ];

        $this->model(model: Notification::class, with: ["createdBy"]);

        // $this->filter([ "status" => $request->status ]);

        $editPermission   = $this->hasPermission("notification-edit");
        $deletePermission = $this->hasPermission("notification-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("application.notification.delete", ['notification' => $row->id]);
            $action = "";
            $action = "
                <a class='btn edit-btn  btn-action bg-success text-white me-2'
                    data-id='{$row->id}'
                    data-party_group_name='{$row->partyGroup?->name}'
                    data-party_group_id='{$row->partyGroup?->id}'
                    data-message='{$row->message}'
                    data-title='{$row->title}'
                    data-permissions='{$editPermission}'
                    data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                    <i class='far fa-edit' aria-hidden='true'></i>
                </a>
            ";

            if ($deletePermission) {
                $action .= "
                    <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-bs-toggle='tooltip'
                        data-bs-placement='top' data-bs-original-title='Delete'
                        href='{$delete}'>
                        <i class='fa-solid fa-trash'></i>
                    </a>
                ";
            }
            return [
                "id"          => $row->id,
                "action"      => $action,
                "title"       => $row->title,
                "message"     => $row->message,
                "party_group" => $row->partyGroup?->name,
                "created_by"  => $row->createdBy?->displayName(),
                "created_at"  => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at"  => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }

    /**
     * Write code on Method
     * @return response()
     */

    public function sendNotification(Request $request)
    {
        // Replace 'your_topic_name' with the actual topic name
        $topic = 'newarrival';
        $accessToken = $this->getAccessToken();

        // Build the notification payload
        $data = [
            "message" => [
                "topic" => $topic,  // Send to the topic
                "notification" => [
                    "title" => 'Group Notification',  // Replace with dynamic title
                    "body" => 'This is a notification for all users in the group topic',  // Replace with dynamic body
                ],
                "data" => [  // Optional: Add custom data if needed
                    "key1" => "value1",
                    "key2" => "value2"
                ]
            ]
        ];

        $client = new GuzzleClient();

        // Send the notification using the HTTP v1 API
        $response = $client->post($this->messagingUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);
        return response()->json(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * Generate OAuth 2.0 access token using the Firebase service account JSON file
     */
    private function getAccessToken()
    {
        $client = new Client();
        $client->setAuthConfig($this->credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();

        return $client->getAccessToken()['access_token'];
    }
}
