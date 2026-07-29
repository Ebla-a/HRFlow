<?php

namespace Modules\User\Http\Controllers\v1;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Http\Requests\UpdateEmailRequest;
use Modules\User\Services\v1\UploadService;
use Modules\User\Services\v1\UserService;
use Modules\User\Transformers\UserResource;
use Modules\User\Transformers\UsersResource;

class UserController extends Controller
{

    public function getAllUsers(UserService $userService)
    {
        $users=$userService->allUsers();
        return UsersResource::collection($users);
    }

    public function getUserById($id,UserService $userService)
    {
        $user=$userService->userById(['id' => $id]);
        return new UserResource($user);
    }
    

    public function updateEmail(UpdateEmailRequest $request,UserService $userService)
    {
        $user = $userService->updateEmail($request->validated()); 
        return new UserResource($user,'Email updated successfully');
    }

    public function updateProfileImage(Request $request,UploadService $userService)
    {
        $user = $userService->updateProfileImage($request->validated()); 
        return new UserResource($user,'profile Image updated successfully');
    }

    public function disActiveUserAccount($id,UserService $userService)
    {
        $userService->disActiveUserAccount(['id' => $id]);
        return response()->Json([
            'message'=>"User disactivated successfully"
        ]);
    }

    public function activeUserAccount($id,UserService $userService)
    {
        $userService->activeUserAccount(['id' => $id]);
        return response()->Json([
            'message'=>"User activated successfully"
        ]);
    }


}
