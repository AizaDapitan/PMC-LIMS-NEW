<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Permission;
use App\Models\User;
use App\Models\Role;
use App\Models\UsersPermissions;
use App\Services\UserService;
use Carbon\Carbon;
use Exception;
use PDO;
use App\Services\AccessRightService;


class UserController extends Controller
{
    protected $userService;
    protected $accessRightService;

    public function __construct(
        AccessRightService $accessRightService,
        UserService $userService
    ) {
        $this->userService = $userService;
        $this->accessRightService = $accessRightService;
    }
    public function index()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Users Maintenance");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('user.index');
    }

    public function getUsers()
    {
        $users = User::where([['active', 1], ['username', '<>', 'ADMIN']])->orderBy('name', 'asc')->get();

        return $users;
    }

    public function getAllUsers()
    {
        $users = User::where([['username', '<>', 'ADMIN']])->orderBy('name', 'asc')->get();

        return $users;
    }

    public function create()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Users Maintenance");

        if (!$rolesPermissions['create']) {
            abort(401);
        }
        return view('user.create');
    }
    public static function employee_lookup()
    {
        $streamContext = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ]);
        $employees = file_get_contents(config('app.api_path') . "hris-api-2.php", false, $streamContext);
        //  $employees = file_get_contents("https://localhost/camm/api/hris-api-2.php", false, $streamContext);
        return $employees;
    }
    public static function userList()
    {

        $users = User::where('active', 1)->get();

        return json_encode($users);
    }
    public function store(UserRequest $userRequest, User $user)
    {
        try {
            $this->userService->create($userRequest);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function deactivate(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        try {
            $user = User::find($request->id);

            $data = [
                'isActive' => 0,
            ];
            $user->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function activate(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        try {
            $user = User::find($request->id);

            $data = [
                'isActive' => 1,
            ];
            $user->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function edit($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Users Maintenance");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $user = User::where('id', $id)->first();
        return view('user.edit', compact('user'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'dept' => 'string|max:150',
            'role_id'   => 'required',
            'assigned_module' => 'required'
        ]);
        try {

            $this->userService->update($request);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
}
