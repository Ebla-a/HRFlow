<?php
namespace Modules\User\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\User\App\DTOs\UpdateAvatarData;
use Modules\User\Entities\User;
use Modules\User\Services\v1\UploadService;
use Tests\TestCase;

class UploadServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_avatar_and_updates_user_record(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');
        $dto = UpdateAvatarData::fromArray(['avatar' => $file]);

        $service = app(UploadService::class);
        $updatedUser = $service->updateProfileImage($user, $dto);

        $this->assertNotNull($updatedUser->avatar_url);
        Storage::disk('public')->assertExists($updatedUser->avatar_url);
    }
}