<?php

namespace Modules\User\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\User\App\DTOs\UpdateAvatarData;
use Modules\User\App\DTOs\UpdateEmailData;


use Modules\User\Entities\User;
use Modules\User\Http\Requests\UpdateEmailRequest as RequestsUpdateEmailRequest;
use Modules\User\Http\Requests\UpdateProfileImageRequest;
use Modules\User\Services\v1\UploadService;
use Modules\User\Services\v1\UserService;
use Modules\User\Transformers\UserResource;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected UploadService $uploadService
    ) {}

    /**
     * Display a paginated list of users.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);
        $users = $this->userService->allUsers();

        return $this->success(
            UserResource::collection($users)->response()->getData(true),
            'Users retrieved successfully.'
        );
    }

    /**
     * Display the specified user details.
     */
    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);
        return $this->success(
            new UserResource($user),
            'User retrieved successfully.'
        );
    }

    /**
     * Update the email address of a user.
     */
    public function updateEmail(RequestsUpdateEmailRequest $request, User $user): JsonResponse
    {
        $this->authorize('updateEmail', $user);
        $dto = UpdateEmailData::fromArray([
            'userId' => $user->id,
            'email' => $request->validated()['email'],
        ]);

        $updatedUser = $this->userService->updateEmail($user, $dto);

        return $this->success(
            new UserResource($updatedUser),
            'Email updated successfully.'
        );
    }

    /**
     * Upload and update the profile avatar image for a user.
     */
    public function updateProfileImage(UpdateProfileImageRequest $request, User $user): JsonResponse
    {
        $this->authorize('updateAvatar', $user);
        $dto = UpdateAvatarData::fromArray($request->validated());

        $updatedUser = $this->uploadService->updateProfileImage($user, $dto);

        return $this->success(
            new UserResource($updatedUser),
            'Profile image updated successfully.'
        );
    }

    /**
     * Deactivate a user account.
     */
    public function deactivateUserAccount(User $user): JsonResponse
    {
        $this->authorize('deactivate', $user);
        $updatedUser = $this->userService->deactivateUserAccount($user);

        return $this->success(
            new UserResource($updatedUser),
            'User deactivated successfully.'
        );
    }

    /**
     * Activate a user account.
     */
    public function activateUserAccount(User $user): JsonResponse
    {
        $this->authorize('activate', $user);
        $updatedUser = $this->userService->activateUserAccount($user);

        return $this->success(
            new UserResource($updatedUser),
            'User activated successfully.'
        );
    }
}
