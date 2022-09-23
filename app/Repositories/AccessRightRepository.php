<?php

namespace App\Repositories;

use App\Repositories\Interfaces\AccessRightRepositoryInterface;
use App\Models\Permission;
use App\Models\RolesPermissions;
use App\Models\User;
use App\Models\UsersPermissions;

use Illuminate\Support\Facades\DB;

class AccessRightRepository implements AccessRightRepositoryInterface
{
    protected $user;
    protected $action;
    protected $userspermissions;

    public function __construct(
        UsersPermissions $usersPermissions
    ) {
        $this->userspermissions = $usersPermissions;
    }

    public function getById($userid)
    {
        $userPermissions = UsersPermissions::where('user_id', $userid)->get();

        if (count($userPermissions) > 0) {
            return UsersPermissions::where('user_id', $userid)->get();
        } else {
            $user = User::where('id', $userid)->first();
            $roleid = $user['role_id'];
            return RolesPermissions::where('role_id', $roleid)->get();
        }
    }

    public function getUsers()
    {
        return User::where('active', '1')->get();
    }
    public function getPermissions()
    {
        $permissions = Permission::where('active', '1')->get();

        $data = collect();
        foreach ($permissions as $permission) {
            $data->push([
                'id' => $permission->id,
                'name' => $permission->name,
            ]);
        }

        return $data;
    }
    public function getModule()
    {
        $modules = DB::table('modules')->get();
        // dd($module);
        $data = collect();
        foreach ($modules as $module) {
            $data->push([
                'id' => $module->id,
                'description' => $module->description,
            ]);
        }

        return $data;
    }
    public function createUser($fields)
    {

        return $this->userspermissions->create($fields);
    }

    public function destroy($id)
    {
        return $this->userspermissions->find($id)->delete();
    }

}
