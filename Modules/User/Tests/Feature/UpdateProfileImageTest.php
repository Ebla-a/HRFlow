<?php

declare(strict_types=1);

namespace Modules\User\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Tests\TestCase;

class UpdateProfileImageTest extends TestCase
{
    public function test_authenticated_admin_can_update_user_profile_image(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create();

        $admin->assignRole('Hr_admin');

        $user = User::factory()->create();

        Sanctum::actingAs($admin);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson(
            "/api/v1/users/{$user->id}/avatar",
            [
                'avatar' => $file,
            ]
        );

        $response->assertStatus(200);

        $user->refresh();

        $this->assertNotNull($user->avatar_url);

        Storage::disk('local')->assertExists(
            $user->avatar_url
        );
    }

    public function test_cannot_upload_image_with_disallowed_mime_type(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create();

        $admin->assignRole('Hr_admin');

        $user = User::factory()->create();

        Sanctum::actingAs($admin);

        $file = UploadedFile::fake()->create(
            'document.pdf',
            500,
            'application/pdf'
        );

        $response = $this->postJson(
            "/api/v1/users/{$user->id}/avatar",
            [
                'avatar' => $file,
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'avatar',
            ]);
    }


    public function test_authorized_user_can_download_profile_image(): void
{
    Storage::fake('local');

    $admin = User::factory()->create();

    $admin->assignRole('Hr_admin');

    $user = User::factory()->create();

    $path = 'profiles/test-avatar.jpg';

    Storage::disk('local')->put(
        $path,
        'fake image content'
    );

    $user->update([
        'avatar_url' => $path,
    ]);

    Sanctum::actingAs($admin);

    $response = $this->get(
        "/api/v1/users/{$user->id}/avatar"
    );

    $response->assertStatus(200);
}





public function test_user_cannot_download_another_user_avatar(): void
{
    Storage::fake('local');

    $user = User::factory()->create();

    $otherUser = User::factory()->create();

    $otherUser->update([
        'avatar_url' => 'profiles/private-avatar.jpg',
    ]);

    Storage::disk('local')->put(
        'profiles/private-avatar.jpg',
        'fake image content'
    );

    Sanctum::actingAs($user);

    $response = $this->get(
        "/api/v1/users/{$otherUser->id}/avatar"
    );

    $response->assertStatus(403);
}
}