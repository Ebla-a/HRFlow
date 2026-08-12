<?php

namespace Modules\Organization\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
    $createPerm  = Permission::firstOrCreate(['name' => 'jobtitle.create', 'guard_name' => 'sanctum']);
    $updatePerm  = Permission::firstOrCreate(['name' => 'jobtitle.update', 'guard_name' => 'sanctum']);
    $deletePerm  = Permission::firstOrCreate(['name' => 'jobtitle.delete', 'guard_name' => 'sanctum']);
    $restorePerm = Permission::firstOrCreate(['name' => 'jobtitle.restore', 'guard_name' => 'sanctum']);

    $hrRole = Role::firstOrCreate(['name' => 'hr_admin', 'guard_name' => 'sanctum']);
    $hrRole->syncPermissions([$createPerm, $updatePerm, $deletePerm, $restorePerm]);
});

describe('GET /api/v1/job-titles (Index)', function () {
    it('fetches a list of all job titles', function () {
        JobTitle::factory()->count(5)->create();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/job-titles');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');
    });


      it('denies access to job titles if user is not authenticated', function () {
        JobTitle::factory()->count(5)->create();


        $response = $this->getJson('/api/v1/job-titles');

        $response->assertStatus(401);

    });
});

describe('POST /api/v1/job-titles (Store)', function () {
    it('creates a new job title with valid data', function () {
        $department = Department::factory()->create();

        $user = User::factory()->create();
$user->assignRole(Role::findByName('hr_admin', 'sanctum'));
        $payload = [
            'title'         => 'Software Engineer',
            'description'   => 'Responsible for developing backend features.',
            'is_active'     => true,
            'department_id' => $department->id,
            'grade'         => 'junior',
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/job-titles', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('job_titles', [
            'title'         => 'Software Engineer',
            'department_id' => $department->id,
        ]);
    });

    it('denies creating job title if user lacks required role/permission', function () {
        $user = User::factory()->create();

        $payload = ['title' => 'Software Engineer'];

        $response = $this->actingAs($user)->postJson('/api/v1/job-titles', $payload);

        $response->assertStatus(403);
    });

    it('denies creating job title if user role is manager', function () {
        Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'sanctum']);

        $user = User::factory()->create();
$user->assignRole(Role::findByName('manager', 'sanctum'));
        $payload = ['title' => 'Software Engineer'];

        $response = $this->actingAs($user)->postJson('/api/v1/job-titles', $payload);

        $response->assertStatus(403);
    });

    it('validates required fields when creating a job title', function () {
        $user = User::factory()->create();
        $user->assignRole(Role::findByName('hr_admin', 'sanctum'));

        $payload = ['description' => 'Responsible for developing frontend features.'];

        $response = $this->actingAs($user)->postJson('/api/v1/job-titles', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title']);
    });
});

describe('PUT /api/v1/job-titles/{id} (Update)', function () {
    it('updates an existing job title successfully', function () {
        $department = Department::factory()->create();
        $jobTitle   = JobTitle::factory()->create([
            'title'         => 'Old Title',
            'department_id' => $department->id,
        ]);

        $updateData = ['title' => 'Updated Senior Engineer'];

        $user = User::factory()->create();
        $user->assignRole(Role::findByName('hr_admin', 'sanctum'));

        $response = $this->actingAs($user)->putJson("/api/v1/job-titles/{$jobTitle->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('job_titles', [
            'id'            => $jobTitle->id,
            'title'         => 'Updated Senior Engineer',
            'department_id' => $department->id,
        ]);
    });

    it('denies update jobtitle if user is not hr-admin', function () {
        $department = Department::factory()->create();
        $jobTitle   = JobTitle::factory()->create([
            'title'         => 'Old Title',
            'department_id' => $department->id,
        ]);

        $updateData = ['title' => 'Updated Senior Engineer'];

        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson("/api/v1/job-titles/{$jobTitle->id}", $updateData);

        $response->assertStatus(403);
    });

   it('validates update data', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::findByName('hr_admin', 'sanctum'));
    $department = Department::factory()->create();
JobTitle::factory()->create([
    'title'         => 'Back End',
    'department_id' => $department->id,
]);

$jobTitle2 = JobTitle::factory()->create([
    'title'         => 'front end',
    'department_id' => $department->id,
]);

$updateData = ['title' => 'back end'];

    $response = $this->actingAs($user)
        ->withHeaders(['Accept' => 'application/json'])
        ->putJson("/api/v1/job-titles/{$jobTitle2->id}", $updateData);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['title']);
});

});

describe('DELETE & RESTORE job title', function () {
    it('soft deletes a job title from the database', function () {
        $jobTitle = JobTitle::factory()->create();

        $user = User::factory()->create();
        $user->assignRole(Role::findByName('hr_admin', 'sanctum'));

        $response = $this->actingAs($user)->deleteJson("/api/v1/job-titles/{$jobTitle->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('job_titles', [
            'id' => $jobTitle->id,
        ]);
    });

    it('restores a soft-deleted job title', function () {
        $jobTitle = JobTitle::factory()->create();
        $jobTitle->delete();

        $user = User::factory()->create();
        $user->assignRole(Role::findByName('hr_admin', 'sanctum'));

        $response = $this->actingAs($user)->postJson("/api/v1/job-titles/{$jobTitle->id}/restore");

        $response->assertStatus(200);

        $this->assertNotSoftDeleted('job_titles', [
            'id' => $jobTitle->id,
        ]);
    });

    it('denies removing a job title if user lacks permission', function () {
        $jobTitle = JobTitle::factory()->create();
        $user     = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/v1/job-titles/{$jobTitle->id}");

        $response->assertStatus(403);
    });
});
