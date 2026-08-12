<?php

declare(strict_types=1);

namespace Modules\Organization\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Database\Seeders\OrganizationDatabaseSeeder;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;
use Modules\User\Entities\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        /*
         * Clear Spatie permission cache before each test.
         * This prevents permissions from previous tests from leaking
         * into the current test.
         */
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
         * Create the roles required by the Organization module tests.
         */
        $hrRole = Role::firstOrCreate([
            'name' => 'hr_admin',
            'guard_name' => 'sanctum',
        ]);

        Role::firstOrCreate([
            'name' => 'manager',
            'guard_name' => 'sanctum',
        ]);

        /*
         * Organization permissions used by Department authorization.
         */
        $permissions = [
            'departments.view',
            'departments.show',
            'departments.update',
            'departments.delete',
            'departments.restore',
            'departments.create',
            'departments.view.all',
            'departments.assign-manager',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'sanctum',
            ]);
        }

        /*
         * HR Admin receives all department permissions required
         * by the feature tests.
         */
        $hrRole->syncPermissions($permissions);

        /*
         * Refresh Spatie cache after permissions have been created
         * and assigned.
         */
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    // =========================================================
    // Authentication
    // =========================================================

    #[Test]
    public function returns_401_unauthenticated_on_index_route_when_no_token_provided(): void
    {
        $response = $this->getJson('/api/v1/departments');

        $response->assertStatus(401);
    }

    // =========================================================
    // Success Operations
    // =========================================================

    #[Test]
    public function can_list_departments_in_hierarchical_tree_format(): void
    {
        $user = User::factory()->create();

        $user->assignRole(
            Role::findOrCreate('hr_admin', 'sanctum')
        );

        $this->seed(OrganizationDatabaseSeeder::class);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/departments');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
                'meta',
            ]);
    }

    #[Test]
    public function allows_hr_admin_to_create_a_new_department(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            Role::findOrCreate('hr_admin', 'sanctum')
        );

        $payload = [
            'name' => 'Engineering',
            'code' => 'ENG-01',
            'is_active' => true,
        ];

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/departments', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('departments', [
            'code' => 'ENG-01',
        ]);
    }

    #[Test]
    public function allows_hr_admin_to_view_department_details(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            Role::findOrCreate('hr_admin', 'sanctum')
        );

        $department = Department::factory()->create();

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson("/api/v1/departments/{$department->id}");

        $response->assertStatus(200);
    }

    #[Test]
    public function allows_manager_to_view_own_department_details(): void
    {
        $user = User::factory()->create();

        $user->assignRole(
            Role::findOrCreate('manager', 'sanctum')
        );

        $department = Department::factory()->create([
            'parent_id' => null,
            'manager_id' => null,
        ]);

        $jobTitle = JobTitle::factory()->create();

        /*
         * Create a unique employee number through the factory.
         * This avoids duplicate employee_number violations.
         */
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'department_id' => $department->id,
            'job_title_id' => $jobTitle->id,
        ]);

        $department->update([
            'manager_id' => $employee->id,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson("/api/v1/departments/{$department->id}");

        $response->assertStatus(200);
    }

    #[Test]
    public function allows_hr_admin_who_has_permission_to_update_a_department(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            Role::findOrCreate('hr_admin', 'sanctum')
        );

        $department = Department::factory()->create([
            'name' => 'Old Name',
            'code' => 'OLD',
        ]);

        $payload = [
            'name' => 'New Name',
            'code' => 'NEW',
        ];

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->putJson(
                "/api/v1/departments/{$department->id}",
                $payload
            );

        $response->assertStatus(200);

        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'code' => 'NEW',
        ]);
    }

    #[Test]
    public function allows_hr_admin_to_soft_delete_a_department(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            Role::findOrCreate('hr_admin', 'sanctum')
        );

        $department = Department::factory()->create();

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->deleteJson(
                "/api/v1/departments/{$department->id}"
            );

        $response->assertStatus(200);

        $this->assertSoftDeleted('departments', [
            'id' => $department->id,
        ]);
    }

    #[Test]
    public function allows_hr_admin_to_restore_a_soft_deleted_department(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            Role::findOrCreate('hr_admin', 'sanctum')
        );

        $department = Department::factory()->create();

        $department->delete();

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson(
                "/api/v1/departments/{$department->id}/restore"
            );

        $response->assertStatus(200);

        $this->assertNotSoftDeleted('departments', [
            'id' => $department->id,
        ]);
    }

    #[Test]
    public function allows_authorized_user_to_assign_a_manager_to_department(): void
    {
        $admin = User::factory()->create();

        /*
         * HR Admin already has departments.assign-manager
         * from setUp(), so no need to call givePermissionTo()
         * separately here.
         */
        $admin->assignRole(
            Role::findOrCreate('hr_admin', 'sanctum')
        );

        $user = User::factory()->create();

        $jobTitle = JobTitle::factory()->create();

        $department = Department::factory()->create();

        /*
         * Use factory so employee_number remains unique.
         */
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'department_id' => $department->id,
            'job_title_id' => $jobTitle->id,
        ]);

        $payload = [
            'manager_id' => $employee->id,
        ];

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->putJson(
                "/api/v1/departments/{$department->id}/assign-manager",
                $payload
            );

        $response->assertStatus(200);

        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'manager_id' => $employee->id,
        ]);
    }

    // =========================================================
    // Authorization - 403
    // =========================================================

    #[Test]
    public function returns_403_on_store_for_unauthorized_user(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/v1/departments', [
                'name' => 'Finance',
                'code' => 'FIN-01',
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function returns_403_on_show_when_user_is_neither_hr_admin_nor_manager(): void
    {
        $user = User::factory()->create();

        $department = Department::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(
                "/api/v1/departments/{$department->id}"
            );

        $response->assertStatus(403);
    }

    #[Test]
    public function returns_403_on_update_without_department_update_permission(): void
    {
        $user = User::factory()->create();

        $department = Department::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson(
                "/api/v1/departments/{$department->id}",
                [
                    'name' => 'Updated Name',
                ]
            );

        $response->assertStatus(403);
    }

    #[Test]
    public function returns_403_on_destroy_without_department_delete_permission(): void
    {
        $user = User::factory()->create();

        $department = Department::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson(
                "/api/v1/departments/{$department->id}"
            );

        $response->assertStatus(403);
    }

    #[Test]
    public function returns_403_on_restore_without_departments_restore_permission(): void
    {
        $user = User::factory()->create();

        $department = Department::factory()->create();

        $department->delete();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/v1/departments/{$department->id}/restore"
            );

        $response->assertStatus(403);
    }

    #[Test]
    public function returns_403_on_assign_manager_without_permission(): void
    {
        $user = User::factory()->create();

        $department = Department::factory()->create();

        $manager = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson(
                "/api/v1/departments/{$department->id}/assign-manager",
                [
                    'manager_id' => $manager->id,
                ]
            );

        $response->assertStatus(403);
    }

    #[Test]
    public function returns_403_when_manager_tries_to_view_another_department_details(): void
    {
        $role = Role::findOrCreate('manager', 'sanctum');

        $managerUser1 = User::factory()->create();

        $managerUser1->assignRole($role);

        $department1 = Department::factory()->create([
            'parent_id' => null,
            'manager_id' => null,
        ]);

        $jobTitle = JobTitle::factory()->create();

        /*
         * Employee 1 belongs to managerUser1 and department 1.
         */
        $employee1 = Employee::factory()->create([
            'user_id' => $managerUser1->id,
            'department_id' => $department1->id,
            'job_title_id' => $jobTitle->id,
        ]);

        $department1->update([
            'manager_id' => $employee1->id,
        ]);

        $managerUser2 = User::factory()->create();

        $managerUser2->assignRole($role);

        $department2 = Department::factory()->create([
            'parent_id' => null,
            'manager_id' => null,
        ]);

        /*
         * Employee 2 belongs to managerUser2 and department 2.
         */
        $employee2 = Employee::factory()->create([
            'user_id' => $managerUser2->id,
            'department_id' => $department2->id,
            'job_title_id' => $jobTitle->id,
        ]);

        $department2->update([
            'manager_id' => $employee2->id,
        ]);

        /*
         * Manager 1 must not be allowed to view department 2.
         */
        $response = $this
            ->actingAs($managerUser1, 'sanctum')
            ->getJson(
                "/api/v1/departments/{$department2->id}"
            );

        $response->assertStatus(403);
    }

    // =========================================================
    // Validation - 422
    // =========================================================

    #[Test]
    public function validates_department_creation_payload(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            Role::findOrCreate('hr_admin', 'sanctum')
        );

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/departments', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'code',
            ]);
    }

    #[Test]
    public function returns_422_when_department_sets_itself_as_its_parent(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            Role::findOrCreate('hr_admin', 'sanctum')
        );

        $department = Department::factory()->create([
            'parent_id' => null,
            'manager_id' => null,
        ]);

        $payload = [
            'name' => 'Updated Name',
            'code' => $department->code,
            'parent_id' => $department->id,
        ];

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->putJson(
                "/api/v1/departments/{$department->id}",
                $payload
            );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'parent_id',
            ]);
    }

    #[Test]
    public function returns_422_when_parent_id_does_not_exist(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            Role::findOrCreate('hr_admin', 'sanctum')
        );

        $payload = [
            'name' => 'New Department',
            'code' => 'NEW-01',
            'parent_id' => 999999,
        ];

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson(
                '/api/v1/departments',
                $payload
            );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'parent_id',
            ]);
    }

    #[Test]
    public function returns_422_when_selected_parent_is_a_descendant_creating_a_circular_reference(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            Role::findOrCreate('hr_admin', 'sanctum')
        );

        $parentDepartment = Department::factory()->create([
            'parent_id' => null,
            'manager_id' => null,
        ]);

        $childDepartment = Department::factory()->create([
            'parent_id' => $parentDepartment->id,
            'manager_id' => null,
        ]);

        $payload = [
            'name' => $parentDepartment->name,
            'code' => $parentDepartment->code,
            'parent_id' => $childDepartment->id,
        ];

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->putJson(
                "/api/v1/departments/{$parentDepartment->id}",
                $payload
            );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'parent_id',
            ]);
    }

    #[Test]
    public function returns_422_when_assigning_a_non_existing_manager_to_department(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            Role::findOrCreate('hr_admin', 'sanctum')
        );

        /*
         * The HR Admin role already receives this permission
         * from setUp().
         *
         * This assertion is intentionally here because if the test
         * returns 403, we can immediately identify that the problem
         * is authorization rather than validation.
         */
        $this->assertTrue(
            $admin->can('departments.assign-manager'),
            'HR Admin should have departments.assign-manager permission.'
        );

        $department = Department::factory()->create([
            'parent_id' => null,
            'manager_id' => null,
        ]);

        $payload = [
            'manager_id' => 999999,
        ];

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->putJson(
                "/api/v1/departments/{$department->id}/assign-manager",
                $payload
            );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'manager_id',
            ])
            ->assertJsonFragment([
                'manager_id' => [
                    'The selected manager does not exist in the system.',
                ],
            ]);
    }
}