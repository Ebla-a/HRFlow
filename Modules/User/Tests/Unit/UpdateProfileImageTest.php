<?php

namespace Modules\User\Tests\Unit;


use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role; 
use Tests\TestCase;

class UpdateProfileImageTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_authenticated_admin_can_update_user_profile_image()
    {
        Storage::fake('public');
        $role=Role::create(['name' => 'Hr_admin']);
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');
        $user = User::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->create('avatar.jpg', 500, 'image/jpeg');
        Sanctum::actingAs($admin);
        $response = $this->postJson(route('updateProfileImage'), [
            'id'    => $user->id,
            'avatar_url' => $file,
        ]);

        $user->refresh();

        $response->assertStatus(200)
            ->assertJson([
                'status'  => true,
                'message' => 'profile Image updated successfully',
                'data'    => [
                    'id'    => $user->id,
                    'avatar_url' =>  Storage::url($user->avatar_url),
                ],
            ])

            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'email',
                    'is_active',
                    'avatar_url' ,
                    'created_at',
                    'updated_at',
                ],
            ]);

        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'avatar_url' =>$user->avatar_url,
        ])
        ;
    }




    public function test_cannot_upload_image_with_disallowed_mime_type()
    {
        Storage::fake('public');

        $role = Role::create(['name' => 'Hr_admin']);
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');
        $user = User::factory()->create();

        
        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        Sanctum::actingAs($admin);

        $response = $this->postJson(route('updateProfileImage'), [
            'id'         => $user->id,
            'avatar_url' => $file,
        ]);

        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avatar_url']);

        
        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'avatar_url' => $user->avatar_url,
        ]);
    }

    
}
