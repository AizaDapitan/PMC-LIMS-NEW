<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserService
{
    protected $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        $users = $this->repository->all();

        $data = collect();
        foreach ($users as $user) {
            $data->push([
                'id' => $user->id,
                'name' => $user->name,
                'domainAccount' => $user->domainAccount,
                'role' => $user->role,
                'isActive' => $user->isActive,
                'role_id' => $user->role_id,

            ]);
        }

        return $data;
    }

    public function create($fields)
    {
        $roles = $this->repository->GetRoleName($fields->role_id);
        $data = [
            'name' => $fields->name,
            'username' => strtoupper($fields->username),
            'password' => \Hash::make("password"),
            'remember_token' => Str::random(60),
            'role_id' => $fields->role_id,
            'role' => $roles->role_name,
            'dept' => $fields->dept,
            'isActive' => 1,
            'email' => $fields->email,
        ];
        $user = $this->repository->create($data);

        if ($user) {
            return redirect()->back()->with('success', 'User has been added successfully!');
        } else {
            return redirect()->back()->with('errors', 'Adding user failed.');
        }
    }

    public function update($fields)
    {
        
        $roles = $this->repository->GetRoleName($fields->role_id);
        $reset = $fields->reset;

            $data = [
                'name' => $fields->name,
                'username' => strtoupper($fields->username),
                'password' => \Hash::make("password"),
                'remember_token' => Str::random(60),
                'role_id' => $fields->role_id,
                'role' => $roles->role_name,
                'dept' => $fields->dept,
                // 'isActive' => $fields->status,
                'email' => $fields->email,
            ];
       
        $user = $this->repository->update($data, $fields->id);

        if ($user) {
            return redirect()->back()->with('success', 'User has been updated successfull!');
        } else {
            return redirect()->back()->with('errors', 'Updating user failed.');
        }
    }

    public function getById($id)
    {
        $user = $this->repository->getById($id);

        $data = [
            'id' => $user->id,
            'role' => $user->role,
            'username' => $user->username,
            'role_id' => $user->role_id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'active' => $user->active,
            'locked'  => $user->locked,
        ];

        return $data;
    }

    public function lock($fields, $id)
    {
        $data = [
            'locked' => $fields->status,
            'locked_at' => now(),
        ];

        $user = $this->repository->update($data, $id);

        if ($user) {
            return redirect()->back()->with('success', 'User lock has been updated successfully!');
        } else {
            return redirect()->back()->with('success', 'User lock update failed!');
        }
    }

    public function changeStatus($fields, $id)
    {
        $data = [
            'active' => $fields->status,
        ];

        $user = $this->repository->update($data, $id);

        if ($user) {
            return redirect()->back()->with('success', 'User status has been updated successfully!');
        } else {
            return redirect()->back()->with('success', 'User status update failed!');
        }
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
}
