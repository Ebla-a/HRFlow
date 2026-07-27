<?php
namespace Modules\User\Services;

use Modules\User\Entities\User;
use Modules\User\Events\UserCreated;

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

}