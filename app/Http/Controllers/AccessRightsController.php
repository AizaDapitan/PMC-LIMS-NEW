<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AccessRightService;
use Exception;

class AccessRightsController extends Controller
{
    protected $accessRightService;

    public function __construct(AccessRightService $accessRightService)
    {
        $this->accessRightService = $accessRightService;
    }
    public function user()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("User Access Rights");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('accessrights.user');
    }
    public function storeUser(Request $request)
    {
        $request->validate([
            // 'userPermissions' => 'required',
            'user_id' => 'required',
        ]);
        try {
            $this->accessRightService->createUser($request);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function getUsers()
    {
        $users = User::where([['isActive', 1], ['username', '<>', 'ADMIN']])->orderBy('username')->get();
        return $users;
    }
    public function getModules()
    {
        $modules = Module::orderBy('description')->get();
        return $modules;
    }
    public function getUserAccessRights(Request $request)
    {
        $users_permissions = $this->accessRightService->getById($request->userid);
        return $users_permissions;
    }
    public function role()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Role Access Rights");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('accessrights.role');
    }
    public function storeRole(Request $request)
    {
        $request->validate([
            // 'userPermissions' => 'required',
            'role_id' => 'required',
        ]);
        try {
            $this->accessRightService->createRole($request);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function getRoleAccessRights(Request $request)
    {
        $roles_permissions = $this->accessRightService->getByRole($request->roleid);
        return $roles_permissions;
    }
}
