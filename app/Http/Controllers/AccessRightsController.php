<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AccessRightService;
use Exception;

class AccessRightsController extends Controller
{
    protected $accessrightService;

    public function __construct(AccessRightService $accessrightService)
    {
        $this->accessrightService = $accessrightService;
    }
    public function user()
    {
        // $rolesPermissions = $this->userrightService->hasPermissions("Roles Access Rights");
        // if (!$rolesPermissions['view']) {
        //     abort(401);
        // }
        return view('accessrights.user');
    }
    public function storeUser(Request $request)
    {
        $request->validate([
            'rolePermissions' => 'required',
            'user_id' => 'required',
        ]);
        try {
            $this->accessrightService->createUser($request);
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
}
