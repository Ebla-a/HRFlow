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
use Modules\Core\App\Traits\ApiResponseTrait;
use Modules\User\Http\Requests\UpdateProfileImageRequest;

class UserController extends Controller
{
    use ApiResponseTrait;

    /**
     * Retrieve a paginated  of all users.
     *
     * @param UserService $userService
     * @return AnonymousResourceCollection
     */
    public function getAllUsers(UserService $userService)
    {
        $users=$userService->allUsers();
        return UserResource::collection($users);
    
    }

    /**
     * Retrieve  specific user by ID.
     *
     * @param int|string $id
     * @param UserService $userService
     * @return JsonResponse
     */
    public function getUserById($id,UserService $userService)
    {
        $user=$userService->userById($id);
        return $this->success(new UserResource($user), 'User found successfully', 200);
    }
    

    /**
     * Update the email address of a user.
     *
     * @param UpdateEmailRequest $request
     * @param UserService $userService
     * @return JsonResponse
     */
    public function updateEmail(UpdateEmailRequest $request,UserService $userService)
    {
        $user = $userService->updateEmail($request->validated()); 
        return $this->success(new UserResource($user), 'Email updated successfully', 200);
    }

    /**
     * Upload and update the profile avatar image for a user.
     *
     * @param UpdateProfileImageRequest $request
     * @param UploadService $userService
     * @return JsonResponse
     */
    public function updateProfileImage(UpdateProfileImageRequest $request,UploadService $userService)
    {
        $user = $userService->updateProfileImage($request->validated()); 
        return $this->success(new UserResource($user), 'profile Image updated successfully', 200);
    }

    /**
     * Deactivate a user account by ID.
     *
     * @param int|string $id
     * @param UserService $userService
     * @return JsonResponse
     */
    public function disActiveUserAccount($id,UserService $userService)
    {
        $userService->disActiveUserAccount(['id' => $id]);
        return $this->success(null, "User disactivated successfully", 200);
    }

    /**
     * Activate a user account by ID.
     *
     * @param int|string $id
     * @param UserService $userService
     * @return JsonResponse
     */
    public function activeUserAccount($id,UserService $userService)
    {
        $userService->activeUserAccount(['id' => $id]);
        return $this->success(null, "User activated successfully", 200);
    }


}
