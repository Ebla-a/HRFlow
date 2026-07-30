<?php
namespace Modules\User\Services\v1;

use Illuminate\Support\Facades\Storage;
use Modules\User\Entities\User;
use Modules\User\Events\UserCreated;
use Modules\User\Exceptions\NotFoundException;
use Modules\User\Exceptions\UserNotFoundException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class HrService
{

    public $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function createRole(array $data)
    {
        $role=$data['role'];
        Role::create(['name'=>$role]);
    }

    public function deleteRole(array $data)
    {
        $id=$data['id'];
        $role = Role::findById($id);
        if(!$role)
        {
            throw new NotFoundException();
        }
        $role->delete();
    }

    public function createPermission(array $data)
    {
        $permition=$data['permition'];
        Permission::create(['name'=>$permition]);
    }

    public function deletePermission(array $data)
    {
        $id=$data['id'];
        $permission = Permission::findById($id);
        if(!$permission){
            throw new NotFoundException();
        }
        $permission->delete();
    }
  
    public function GrantRole(array $data)
    {
        $id = $data['id'];
        $role=$data['role'];
        $user=$this->service->userById($id);
        $user->assignRole($role);
        $user->save();
    }

    public function revokeRole(array $data)
    {
        $id = $data['id'];
        $role=$data['role'];
        $user=$this->service->userById($id);
        $user->removeRole($role);
        $user->save();
    }

    public function GrantPermission(array $data)
    {
        $id = $data['id'];
        $permission = $data['permission'];
        $user=$this->service->userById($id);
        $user->givePermissionTo($permission);
        $user->save();
    }

    public function revokePermission(array $data)
    {
        $id = $data['id'];
        $permission = $data['permission'];
        $user=$this->service->userById($id);
        $user->revokePermissionTo($permission);
        $user->save();
    }
    

}