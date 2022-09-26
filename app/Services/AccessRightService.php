<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\RolesPermissions;
use App\Repositories\Interfaces\AccessRightRepositoryInterface;
use App\Models\UsersPermissions;
use Illuminate\Support\Facades\DB;

class AccessRightService
{
    protected $repository;
    protected $userService;

    public function __construct(
        AccessRightRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }
    public function getUsers()
    {
        $users = $this->repository->getUsers();
        $data = collect();
        foreach ($users as $user) {
            $data->push([
                'id' => $user->id,
                'name' => $user->name,
            ]);
        }

        return $data;
    }
    public function getPermissions()
    {
        $permissions = Permission::where('active', '1')->orderBy('description', 'asc')->get();
        $data = collect();
        foreach ($permissions as $permission) {
            $data->push([
                'id' => $permission->id,
                'description' => $permission->description,
                'module_type' => $permission->module_type,
            ]);
        }

        return $data;
    }
    public function getModule()
    {

        $modules = DB::table('modules')->orderBy('description', 'asc')->get();
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
        $userrights = false;
        
        UsersPermissions::where('user_id', '=', $fields->user_id)->delete();
        $listOfPermissions = explode(',', $fields->userPermissions);
        foreach ($listOfPermissions as $permission) {

            $permissionarray = explode("_", $permission);
            if (count($permissionarray) == 3) {
                $data = [
                    'user_id' => $fields->user_id,
                    'permission_id' => $permissionarray[0],
                    'module_id' => $permissionarray[1],
                    'action' => $permissionarray[2],
                ];
                $userrights = $this->repository->createUser($data);
            }
        }

        return redirect()->back()->with('success', 'User Access Right has been saved successfully!');
    }

    public function destroy($id)
    {
        $user = $this->repository->destroy($id);

        if ($user) {
            return redirect()->back()->with('success', 'User has been removed successfully!');
        } else {
            return redirect()->back()->with('success', 'Failed removing user!');
        }
    }
    public function getById($userid)
    {
        $userpermissions = $this->repository->getById($userid);

        $data = collect();
        foreach ($userpermissions as $userpermission) {
            $data->push([
                'user_id' => $userpermission->user_id,
                'permission_id' => $userpermission->permission_id,
                'module_id' => $userpermission->module_id,
                'action' => $userpermission->action,
            ]);
        }
        return $data;
    }
    public function hasPermissions($description)
    {
        $roles_permissions = $this->repository->hasPermissions($description);
        $data = collect();
        $access = false;
        $role = auth()->user()->role;
        if ($role == "ADMIN" || $role == "admin") {
            $access = true;
        }
        $create = $access;
        $edit = $access;
        $delete = $access;
        $view = $access;
        $print = $access;
        $search = $access;
        $upload = $access;
        $pagination = $access;
        foreach ($roles_permissions as $roles_permission) {
            if ($roles_permission['action'] == "create") {
                $create = true;
            } elseif ($roles_permission['action'] == "edit") {
                $edit = true;
            } elseif ($roles_permission['action'] == "delete") {
                $delete = true;
            } elseif ($roles_permission['action'] == "view") {
                $view = true;
            } elseif ($roles_permission['action'] == "print") {
                $print = true;
            } elseif ($roles_permission['action'] == "search") {
                $search = true;
            } elseif ($roles_permission['action'] == "upload") {
                $upload = true;
            } elseif ($roles_permission['action'] == "pagination") {
                $pagination = true;
            }
        }

        $data->push([
            'create' => $create,
            'edit' => $edit,
            'delete' => $delete,
            'view' => $view,
            'print' => $print,
            'search' => $search,
            'upload' => $upload,
            'pagination' => $pagination,
        ]);

        return $data->first();
    }
    public function createRole($fields)
    {
        $rolerights = false;
        RolesPermissions::where('role_id', '=', $fields->role_id)->delete();
        $listOfPermissions = explode(',', $fields->rolePermissions);

        foreach ($listOfPermissions as $permission) {

            $permissionarray = explode("_", $permission);
            if (count($permissionarray) == 3) {
                $data = [
                    'role_id' => $fields->role_id,
                    'permission_id' => $permissionarray[0],
                    'module_id' => $permissionarray[1],
                    'action' => $permissionarray[2],
                ];
                $rolerights = $this->repository->createRole($data);
            }
        }
        return redirect()->back()->with('success', 'Role Access Right has been saved successfully!');
    }
    public function getByRole($roleid)
    {        
        $rolepermissions = $this->repository->getByRole($roleid);

        $data = collect();
        foreach ($rolepermissions as $rolepermission) {
            $data->push([
                'role_id' => $rolepermission->role_id,
                'application_id' => $rolepermission->app_id,
                'permission_id' => $rolepermission->permission_id,
                'module_id' => $rolepermission->module_id,
                'action' => $rolepermission->action,
            ]);
        }
        return $data;
    }
}
