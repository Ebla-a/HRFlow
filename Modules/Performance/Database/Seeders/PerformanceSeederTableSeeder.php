<?php

namespace Modules\Performance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\Performance\Entities\PerformanceReview;
use Modules\User\Entities\User;

class PerformanceSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run(): void
    {

        $n=20;

        $department=Department::factory()->create();
        $jobTitle=JobTitle::factory()->create([
            'department_id'=>$department->id
        ]);

        PerformanceCycle::factory()->count($n)->create();
        PerformanceCycle::factory()->count($n)->create(['status' => 'Active']);

        for($i=0;$i<$n;$i++)
        { 
            Employee::factory()->create(
            [
                'user_id'=>User::factory()->create()->id,
                'department_id' => $department->id,
                'job_title_id' => $jobTitle->id,
                'employee_number' => 'EMP-001'.$i,
                'national_id' => '1234567892'.$i,
            ]
            );
        }
        
        $employees = Employee::all();
        $activeCycles = performanceCycle::where('status', 'Active')->get();

        $uniquePairs = collect();

        foreach ($activeCycles as $cycle) {
        foreach ($employees as $employee) {
            $uniquePairs->push([
                'cycle_id'    => $cycle->id,
                'employee_id' => $employee->id,
            ]);
        }
        }
        $selectedPairs = $uniquePairs->shuffle()->take($n);

        foreach ($selectedPairs as $pair) {
        $reviewer = $employees->where('id', '!=', $pair['employee_id'])->random();
        PerformanceReview::factory()->create([
            'employee_id'          => $pair['employee_id'],
            'reviewer_id'          => $reviewer->id,
            'performance_cycle_id' => $pair['cycle_id'],
        ]);
        }
    }
}
