<?php
namespace Modules\Organization\Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Database\Seeders\OrganizationDatabaseSeeder;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {

app(PermissionRegistrar::class)->forgetCachedPermissions();

    $hrRole = Role::firstOrCreate(['name' => 'hr_admin', 'guard_name' => 'sanctum']);
    $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'sanctum']);

    Permission::firstOrCreate(['name' => 'department.update', 'guard_name' => 'sanctum']);
    Permission::firstOrCreate(['name' => 'department.delete', 'guard_name' => 'sanctum']);
    Permission::firstOrCreate(['name' => 'department.restore', 'guard_name' => 'sanctum']);
    Permission::firstOrCreate(['name' => 'department.create', 'guard_name' => 'sanctum']);
    Permission::firstOrCreate(['name' => 'departments.view.all', 'guard_name' => 'sanctum']);
    Permission::firstOrCreate(['name' => 'departments.assign-manager', 'guard_name' => 'sanctum']);

    $hrRole->syncPermissions([
        'departments.view.all',
        'department.create',
        'department.update',
        'department.delete',
        'department.restore',
        'departments.assign-manager'
    ]);
});


//un authenticated cases 401

it('returns 401 unauthenticated on index route when no token provided', function () {
    $response = $this->getJson('/api/v1/departments');

    $response->assertStatus(401);
});

// success cases 200,201

describe('Department Success Operations', function () {

    it('can list departments in hierarchical tree format', function () {
        $user = User::factory()->create();
        $this->seed(OrganizationDatabaseSeeder::class);
        $response = $this->actingAs($user)
            ->getJson('/api/v1/departments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
                'meta'
            ]);
    });

    it('allows hr admin to create a new department', function () {
        $admin = User::factory()->create();

$role = Role::findOrCreate('hr_admin', 'sanctum');
    $admin->assignRole($role);
        $payload = [
            'name' => 'Engineering',
            'code' => 'ENG-01',
            'is_active' => true,
        ];

        $response = $this->actingAs($admin)
            ->postJson('/api/v1/departments', $payload);


        $response->assertStatus(201);

        $this->assertDatabaseHas('departments', [
            'code' => 'ENG-01'
        ]);
    });

    it('allows hr admin  to view department details', function () {
        $admin = User::factory()->create();
        $role = Role::findOrCreate('hr_admin', 'sanctum');
        $admin->assignRole($role);
        $department = Department::factory()->create();

        $response = $this->actingAs($admin)
            ->getJson("/api/v1/departments/{$department->id}");


        $response->assertStatus(200);
    });



       it('allows manager to view own department details', function () {

      $user = User::factory()->create(['id'=>1]);
      $user->guard_name = 'sanctum';

    $role = Role::findOrCreate('manager', 'sanctum');
    $user->assignRole($role);

    $department = Department::factory()->create([
        'parent_id'  => null,
        'manager_id' => null,
    ]);


       $jobTitle = JobTitle::factory()->create();
        $employee= Employee::firstOrCreate([
                'id' => 1,
                'user_id' => $user->id,
                'department_id' => $department->id,
                'job_title_id' => $jobTitle->id,
                'employee_number' => 'EMP-001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'national_id' => '123456789',
                'birth_date' => '1995-01-01',
                'gender' => 'male',
                'employment_type' => 'full_time',
                'status' => 'active',
                'hire_date' => now(),
            ]);

$department->update(['manager_id' => $employee->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/v1/departments/{$department->id}");


        $response->assertStatus(200);
    });



    it('allows hr-admin who has permission  to update a department', function () {
        $admin = User::factory()->create();

        $role = Role::findOrCreate('hr_admin', 'sanctum');
        $admin->assignRole($role);

        $department = Department::factory()->create(['name' => 'Old Name', 'code' => 'OLD']);

        $payload = [
            'name' => 'New Name',
            'code' => 'NEW',
        ];

        $response = $this->actingAs($admin)
            ->putJson("/api/v1/departments/{$department->id}", $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('departments', [
            'id'   => $department->id,
            'code' => 'NEW'
        ]);
    });


    it('allows hr admin to soft delete a department', function () {
       $admin = User::factory()->create();

        $role = Role::findOrCreate('hr_admin', 'sanctum');
        $admin->assignRole($role);

        $department = Department::factory()->create();

        $response = $this->actingAs($admin)
            ->deleteJson("/api/v1/departments/{$department->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('departments', [
            'id' => $department->id
        ]);
    });

    it('allows hr-admin to restore a soft-deleted department', function () {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('hr_admin', 'sanctum'));

        $department = Department::factory()->create();
        $department->delete();

        $response = $this->actingAs($admin)
            ->postJson("/api/v1/departments/{$department->id}/restore");

        $response->assertStatus(200);

        $this->assertNotSoftDeleted('departments', [
            'id' => $department->id
        ]);
    });

    it('allows authorized user to assign a manager to department', function () {
       $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('hr_admin', 'sanctum'));


        $user = User::factory()->create();
        $jobTitle = JobTitle::factory()->create();
         $department = Department::factory()->create();

        $employee= Employee::firstOrCreate([
                'id' => 1,
                'user_id' => $user->id,
                'department_id' => $department->id,
                'job_title_id' => $jobTitle->id,
                'employee_number' => 'EMP-001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'national_id' => '123456789',
                'birth_date' => '1995-01-01',
                'gender' => 'male',
                'employment_type' => 'full_time',
                'status' => 'active',
                'hire_date' => now(),
            ]);

        $payload = [
            'manager_id' => $employee->id,
        ];

        $response = $this->actingAs($admin)
            ->putJson("/api/v1/departments/{$department->id}/assign-manager", $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('departments', [
            'id'         => $department->id,
            'manager_id' => $employee->id,
        ]);
    });


});


//un authorized cases 403

describe('Department Authorization (403 Forbidden)', function () {

    it('returns 403 on store for unauthorized user', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/departments', [
                'name' => 'Finance',
                'code' => 'FIN-01',
            ]);

        $response->assertStatus(403);
    });

    it('returns 403 on show when user is neither Hr_admin nor manager', function () {
        $user = User::factory()->create();
        $department = Department::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/v1/departments/{$department->id}");

        $response->assertStatus(403);
    });

    it('returns 403 on update without department.update permission', function () {
        $user = User::factory()->create();
        $department = Department::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/v1/departments/{$department->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(403);
    });

    it('returns 403 on destroy without department.delete permission', function () {
        $user = User::factory()->create();
        $department = Department::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/v1/departments/{$department->id}");

        $response->assertStatus(403);
    });

    it('returns 403 on restore without departments.restore permission', function () {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        $department->delete();

        $response = $this->actingAs($user)
            ->postJson("/api/v1/departments/{$department->id}/restore");

        $response->assertStatus(403);
    });

    it('returns 403 on assignManager without departments.assign-manager permission', function () {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        $manager = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/v1/departments/{$department->id}/assign-manager", [
                'manager_id' => $manager->id,
            ]);

        $response->assertStatus(403);
    });






 it('returns 403 when a manager tries to view another department details', function () {

    $managerUser1 = User::factory()->create();
    $managerUser1->guard_name = 'sanctum';
    $role = Role::findOrCreate('manager', 'sanctum');
    $managerUser1->assignRole($role);

    $department1 = Department::factory()->create([
        'parent_id'  => null,
        'manager_id' => null,
    ]);

    $jobTitle = JobTitle::factory()->create();

    $employee1 = Employee::create([
        'user_id'         => $managerUser1->id,
        'department_id'   => $department1->id,
        'job_title_id'    => $jobTitle->id,
        'employee_number' => 'EMP-001',
        'first_name'      => 'John',
        'last_name'       => 'Doe',
        'national_id'     => '123456789',
        'birth_date'      => '1995-01-01',
        'gender'          => 'male',
        'employment_type' => 'full_time',
        'status'          => 'active',
        'hire_date'       => now(),
    ]);

    $department1->update(['manager_id' => $employee1->id]);

    $managerUser2 = User::factory()->create();
    $managerUser2->guard_name = 'sanctum';
    $managerUser2->assignRole($role);

    $department2 = Department::factory()->create([
        'parent_id'  => null,
        'manager_id' => null,
    ]);

    $employee2 = Employee::create([
        'user_id'         => $managerUser2->id,
        'department_id'   => $department2->id,
        'job_title_id'    => $jobTitle->id,
        'employee_number' => 'EMP-002',
        'first_name'      => 'Jane',
        'last_name'       => 'Smith',
        'national_id'     => '987654321',
        'birth_date'      => '1996-01-01',
        'gender'          => 'female',
        'employment_type' => 'full_time',
        'status'          => 'active',
        'hire_date'       => now(),
    ]);

    $department2->update(['manager_id' => $employee2->id]);

//attempting manager of department  1 view department 2
    $response = $this->actingAs($managerUser1, 'sanctum')
        ->getJson("/api/v1/departments/{$department2->id}");

    $response->assertStatus(403);
});

});


//validation cases 422
describe('Department validation error (422 Forbidden)', function () {


it('validates department creation payload', function () {
    $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('hr_admin', 'sanctum'));


    $response = $this->actingAs($admin)
        ->postJson('/api/v1/departments', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'code']);
});

it('returns 422 when department sets itself as its parent', function () {

    $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('hr_admin', 'sanctum'));

    $department = Department::factory()->create([
        'parent_id'  => null,
        'manager_id' => null,
    ]);

    $payload = [
        'name'      => 'Updated Name',
        'code'      => $department->code,
        'parent_id' => $department->id,
    ];

    $response = $this->actingAs($admin, 'sanctum')
        ->putJson("/api/v1/departments/{$department->id}", $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['parent_id']);
});


it('returns 422 when parent_id does not exist', function () {

    $admin = User::factory()->create();
    $role = Role::findOrCreate('hr_admin', 'sanctum');
    $admin->assignRole($role);

    $payload = [
        'name'      => 'New Department',
        'code'      => 'NEW-01',
        'parent_id' => 999999,
    ];

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/v1/departments', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['parent_id']);
});


it('returns 422 when selected parent is a descendant creating a circular reference', function () {

      $admin = User::factory()->create();
    $role = Role::findOrCreate('hr_admin', 'sanctum');
    $admin->assignRole($role);

    $parentDepartment = Department::factory()->create([
        'parent_id'  => null,
        'manager_id' => null,
    ]);

    $childDepartment = Department::factory()->create([
        'parent_id'  => $parentDepartment->id,
        'manager_id' => null,
    ]);

    $payload = [
        'name'      => $parentDepartment->name,
        'code'      => $parentDepartment->code,
        'parent_id' => $childDepartment->id,// Attempting to set the child as the parent of its own parent
    ];

    $response = $this->actingAs($admin, 'sanctum')
        ->putJson("/api/v1/departments/{$parentDepartment->id}", $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['parent_id']);
});



it('returns 422 when assigning a non-existing manager to department', function () {

   $admin = User::factory()->create();
    $role = Role::findOrCreate('hr_admin', 'sanctum');
    $admin->assignRole($role);
    $admin->givePermissionTo(Permission::findOrCreate('departments.assign-manager', 'sanctum'));

    $department = Department::factory()->create([
        'parent_id'  => null,
        'manager_id' => null,
    ]);

    $payload = [
        'manager_id' => 999999,
    ];

    $response = $this->actingAs($admin, 'sanctum')
        ->putJson("/api/v1/departments/{$department->id}/assign-manager", $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['manager_id'])
        ->assertJsonFragment([
            'manager_id' => [
                'the selected manager does not exist in the system.'
            ]
        ]);



});



});
