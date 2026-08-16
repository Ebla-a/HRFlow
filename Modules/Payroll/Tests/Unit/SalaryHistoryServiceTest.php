<?php

namespace Modules\Payroll\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Entities\User;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Database\factories\JobTitleFactory;
use Modules\Payroll\Entities\SalaryStructure;
use Modules\Payroll\Entities\SalaryHistory;
use Modules\Payroll\Services\SalaryHistoryService;
use Modules\Payroll\App\DTOs\UpdateSalaryStructureDTO;

class SalaryHistoryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_salary_history_and_changed_items_correctly(): void
    {
        $user = User::factory()->create();

        $jobTitle = JobTitleFactory::new()->create();

        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'job_title_id' => $jobTitle->id,
        ]);

        $salaryStructure = SalaryStructure::factory()->create([
            'employee_id' => $employee->id,
            'basic_salary' => 5000.00,
            'housing_allowance' => 1000.00,
            'transport_allowance' => 500.00,
            'other_allowance' => 0.00,
        ]);

        $dto = new UpdateSalaryStructureDTO(
            basic_salary: 6000.00,
            housing_allowance: 1000.00,
            transport_allowance: 600.00,
            other_allowance: 0.00,
            effective_date: '2026-08-01',
            reason: 'Annual promotion'
        );

        $service = new SalaryHistoryService();
        
       
        $service->store($salaryStructure, $dto, $user->id);

        $this->assertDatabaseHas('salary_histories', [
            'employee_id' => $employee->id,
            'reason' => 'Annual promotion',
            'changed_by' => $user->id, 
        ]);

        $history = SalaryHistory::where('employee_id', $employee->id)->first();

        $this->assertDatabaseHas('salary_history_items', [
            'salary_history_id' => $history->id,
            'old_amount' => 5000.00,
            'new_amount' => 6000.00,
        ]);

        $this->assertDatabaseHas('salary_history_items', [
            'salary_history_id' => $history->id,
            'old_amount' => 500.00,
            'new_amount' => 600.00,
        ]);
    }
}