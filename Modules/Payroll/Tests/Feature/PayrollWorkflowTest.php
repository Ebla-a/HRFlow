<?php

namespace Modules\Payroll\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Permission;
use Modules\Employee\Entities\Employee;
use Modules\Payroll\Entities\PayrollRun;
use Modules\Payroll\Entities\SalaryStructure;

class PayrollWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_payroll_workflow_from_structure_to_finalization(): void
    {
        $permissions = [
            'create.payroll.run',
            'update.payroll.run',
            'finalize.payroll.run',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        $employee = Employee::factory()->create();

        SalaryStructure::create([
            'employee_id' => $employee->id,
            'basic_salary' => 5000.00,
            'housing_allowance' => 1000.00,
            'transport_allowance' => 500.00,
            'other_allowance' => 0.00,
            'effective_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/payroll/runs', [
            'month' => 8,
            'year' => 2026,
            'notes' => 'August Payroll',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'draft');

        $payrollRunId = $response->json('data.id');

        $processResponse = $this->actingAs($user, 'sanctum')->postJson("/api/v1/payroll/runs/{$payrollRunId}/process");

        $processResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'processing');

        $finalizeResponse = $this->actingAs($user, 'sanctum')->postJson("/api/v1/payroll/runs/{$payrollRunId}/finalize");

        $finalizeResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'finalized');

        $this->assertDatabaseHas('payroll_runs', [
            'id' => $payrollRunId,
            'status' => 'finalized',
        ]);
    }
}