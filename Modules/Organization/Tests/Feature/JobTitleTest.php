<?php

declare(strict_types=1);

namespace Modules\Organization\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;
use Modules\User\Entities\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class JobTitleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
{
   parent::setUp();

   Cache::spy();


    app(PermissionRegistrar::class)->forgetCachedPermissions();    app(PermissionRegistrar::class)
        ->forgetCachedPermissions();

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */

    $permissions = [
        'jobtitles.view.all',
        'jobtitle.create',
        'jobtitle.update',
        'jobtitle.delete',
        'jobtitle.restore',
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate([
            'name' => $permission,
            'guard_name' => 'sanctum',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HR Admin Role
    |--------------------------------------------------------------------------
    */

    $hrRole = Role::firstOrCreate([
        'name' => 'Hr_admin',
        'guard_name' => 'sanctum',
    ]);

    $hrRole->syncPermissions($permissions);

    /*
    |--------------------------------------------------------------------------
    | Other Roles
    |--------------------------------------------------------------------------
    */

    Role::firstOrCreate([
        'name' => 'Manager',
        'guard_name' => 'sanctum',
    ]);

    Role::firstOrCreate([
        'name' => 'Employee',
        'guard_name' => 'sanctum',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Clear permission cache again
    |--------------------------------------------------------------------------
    */

    app(PermissionRegistrar::class)
        ->forgetCachedPermissions();
}
    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function denies_access_to_job_titles_if_user_is_not_authenticated(): void
    {
        JobTitle::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(
            '/api/v1/job-titles'
        );

        $response->assertStatus(401);
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function fetches_a_list_of_all_job_titles_for_authorized_user(): void
    {
        JobTitle::factory()
            ->count(5)
            ->create();

        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Hr_admin', 'sanctum')
        );

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/job-titles');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
                'meta',
            ])
            ->assertJsonCount(JobTitle::count(), 'data');

    }

    #[Test]
    public function denies_access_to_job_titles_if_user_lacks_permission(): void
    {
        JobTitle::factory()
            ->count(5)
            ->create();

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/job-titles');

        $response->assertStatus(403);
    }

    #[Test]
    public function denies_manager_from_viewing_all_job_titles(): void
    {
        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Manager', 'sanctum')
        );

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/job-titles');

        $response->assertStatus(403);
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function creates_a_new_job_title_with_valid_data(): void
    {
        $department = Department::factory()->create();

        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Hr_admin', 'sanctum')
        );

        $payload = [
            'title' => 'Software Engineer',
            'description' => 'Responsible for developing backend features.',
            'is_active' => true,
            'department_id' => $department->id,
            'grade' => 'junior',
        ];

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                '/api/v1/job-titles',
                $payload
            );

        $response->assertStatus(201);

        $this->assertDatabaseHas('job_titles', [
            'title->en' => 'Software Engineer',
            'department_id' => $department->id,
        ]);
    }

    #[Test]
    public function denies_creating_job_title_if_user_lacks_permission(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/v1/job-titles', [
                'title' => 'Software Engineer',
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function denies_manager_from_creating_job_title(): void
    {
        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Manager', 'sanctum')
        );

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/v1/job-titles', [
                'title' => 'Software Engineer',
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function validates_required_fields_when_creating_a_job_title(): void
    {
        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Hr_admin', 'sanctum')
        );

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                '/api/v1/job-titles',
                []
            );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
            ]);
    }

    #[Test]
    public function validates_department_id_when_creating_a_job_title(): void
    {
        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Hr_admin', 'sanctum')
        );

        $payload = [
            'title' => 'Software Engineer',
            'department_id' => 999999,
        ];

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                '/api/v1/job-titles',
                $payload
            );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'department_id',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function updates_an_existing_job_title_successfully(): void
    {
        $department = Department::factory()->create();

        $jobTitle = JobTitle::factory()->create([
            'title' => 'Old Title',
            'department_id' => $department->id,
        ]);

        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Hr_admin', 'sanctum')
        );

        $payload = [
            'title' => 'Updated Senior Engineer',
        ];

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson(
                "/api/v1/job-titles/{$jobTitle->id}",
                $payload
            );

        $response->assertStatus(200);

        $this->assertDatabaseHas('job_titles', [
            'id' => $jobTitle->id,
            'title->en' => 'Updated Senior Engineer',
            'department_id' => $department->id,
        ]);
    }

    #[Test]
    public function denies_update_job_title_if_user_lacks_permission(): void
    {
        $department = Department::factory()->create();

        $jobTitle = JobTitle::factory()->create([
            'department_id' => $department->id,
        ]);

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson(
                "/api/v1/job-titles/{$jobTitle->id}",
                [
                    'title' => 'Updated Title',
                ]
            );

        $response->assertStatus(403);
    }

    #[Test]
    public function denies_manager_from_updating_job_title(): void
    {
        $department = Department::factory()->create();

        $jobTitle = JobTitle::factory()->create([
            'department_id' => $department->id,
        ]);

        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Manager', 'sanctum')
        );

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson(
                "/api/v1/job-titles/{$jobTitle->id}",
                [
                    'title' => 'Updated Title',
                ]
            );

        $response->assertStatus(403);
    }

    #[Test]
    public function returns_404_when_updating_non_existing_job_title(): void
    {
        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Hr_admin', 'sanctum')
        );

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson(
                '/api/v1/job-titles/999999',
                [
                    'title' => 'Updated Title',
                ]
            );

        $response->assertStatus(404);
    }

    #[Test]
    public function validates_update_data(): void
    {
        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Hr_admin', 'sanctum')
        );

        $department = Department::factory()->create();

        JobTitle::factory()->create([
            'title' => 'Back End',
            'department_id' => $department->id,
        ]);

        $jobTitle2 = JobTitle::factory()->create([
            'title' => 'Front End',
            'department_id' => $department->id,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson(
                "/api/v1/job-titles/{$jobTitle2->id}",
                [
                    'title' => 'back end',
                ]
            );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function soft_deletes_a_job_title(): void
    {
        $jobTitle = JobTitle::factory()->create();

        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Hr_admin', 'sanctum')
        );

        $response = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson(
                "/api/v1/job-titles/{$jobTitle->id}"
            );

        $response->assertStatus(200);

        $this->assertSoftDeleted('job_titles', [
            'id' => $jobTitle->id,
        ]);
    }

    #[Test]
    public function denies_removing_a_job_title_if_user_lacks_permission(): void
    {
        $jobTitle = JobTitle::factory()->create();

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson(
                "/api/v1/job-titles/{$jobTitle->id}"
            );

        $response->assertStatus(403);
    }

    #[Test]
    public function denies_manager_from_deleting_job_title(): void
    {
        $jobTitle = JobTitle::factory()->create();

        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Manager', 'sanctum')
        );

        $response = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson(
                "/api/v1/job-titles/{$jobTitle->id}"
            );

        $response->assertStatus(403);
    }

    #[Test]
    public function returns_404_when_deleting_non_existing_job_title(): void
    {
        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Hr_admin', 'sanctum')
        );

        $response = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson(
                '/api/v1/job-titles/999999'
            );

        $response->assertStatus(404);
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function restores_a_soft_deleted_job_title(): void
    {
        $jobTitle = JobTitle::factory()->create();

        $jobTitle->delete();

        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Hr_admin', 'sanctum')
        );

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/v1/job-titles/{$jobTitle->id}/restore"
            );

        $response->assertStatus(200);

        $this->assertNotSoftDeleted('job_titles', [
            'id' => $jobTitle->id,
        ]);
    }

    #[Test]
    public function denies_restoring_job_title_if_user_lacks_permission(): void
    {
        $jobTitle = JobTitle::factory()->create();

        $jobTitle->delete();

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/v1/job-titles/{$jobTitle->id}/restore"
            );

        $response->assertStatus(403);
    }

    #[Test]
    public function denies_manager_from_restoring_job_title(): void
    {
        $jobTitle = JobTitle::factory()->create();

        $jobTitle->delete();

        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Manager', 'sanctum')
        );

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/v1/job-titles/{$jobTitle->id}/restore"
            );

        $response->assertStatus(403);
    }

    #[Test]
    public function returns_404_when_restoring_non_existing_job_title(): void
    {
        $user = User::factory()->create();

        $user->assignRole(
            Role::findByName('Hr_admin', 'sanctum')
        );

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                '/api/v1/job-titles/999999/restore'
            );

        $response->assertStatus(404);
    }
}