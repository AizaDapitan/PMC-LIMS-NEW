<?php

namespace App\Repositories\Interfaces;

interface AccessRightRepositoryInterface
{
    public function getUsers();
    public function getPermissions();
    public function getModule();
    public function createUser($fields);
    public function createRole($fields);
    public function destroy($user_id);
    public function getById($id);
    public function hasPermissions($description);
    public function getByRole($role_id);

}
