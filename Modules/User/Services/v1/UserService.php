<?php
namespace Modules\User\Services\v1;

use Illuminate\Support\Facades\Storage;
use Modules\User\Entities\User;
use Modules\User\Events\UserCreated;
use Modules\User\Exceptions\NotFoundException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserService
{

    public function createUser($email)
    {
        $password="12345678";
        $user=User::create([
            'email'=>$email,
            'password'=>$password,
        ]);
        UserCreated::dispatch($email,$password);
        return $user;
    }

    public function allUsers()
    {
        return  User::paginate(10);
    }


    public function userById($id)
    {
        $user = User::find($id);
        if (!$user) {
            throw new NotFoundException();
            
        }
        return $user;
    }

    public function updateEmail(array $data)
    {
        $id = $data['id'];
        $newEmail = $data['email'];
        $user = $this->userById($id);
        $user->email = $newEmail;
        $user->email_verified_at=null;
        $user->save();
        return $user;
    }

    public function disActiveUserAccount(array $data)
    {
        $id = $data['id'];
        $user = $this->userById($id);
        $user->is_active = false;
        $user->save();
    }

    public function activeUserAccount(array $data)
    {
        $id = $data['id'];
        $user = $this->userById($id);
        $user->is_active = true;
        $user->save();
    }


}