<?php

declare(strict_types=1);

namespace Modules\User\Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\User\App\DTOs\UpdateAvatarData;
use Modules\User\Entities\User;
use Modules\User\Services\v1\UploadService;
use Tests\TestCase;

class UploadServiceTest extends TestCase
{
    public function test_it_stores_avatar_and_updates_user_record(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $dto = new UpdateAvatarData(
            avatar: $file
        );

        $service = new UploadService();

        $updatedUser = $service->updateProfileImage(
            $user,
            $dto
        );

        $this->assertNotNull($updatedUser->avatar_url);

        Storage::disk('local')->assertExists(
            $updatedUser->avatar_url
        );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'avatar_url' => $updatedUser->avatar_url,
        ]);
    }
}