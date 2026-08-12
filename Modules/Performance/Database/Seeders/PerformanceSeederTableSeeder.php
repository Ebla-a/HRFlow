<?php

namespace Modules\Performance\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\Performance\Entities\PerformanceReview;
use Modules\User\Entities\User;

class PerformanceSeederTableSeeder extends Seeder
{
    public function run(): void
    {
        $n = 20;

        /*
         * Create organization data required by employees.
         */
        $department = Department::factory()->create();

        $jobTitle = JobTitle::factory()->create([
            'department_id' => $department->id,
        ]);

        /*
         * Create performance cycles.
         */
        PerformanceCycle::factory()
            ->count($n)
            ->create();

        PerformanceCycle::factory()
            ->count(2)
            ->create([
                'status' => 'Active',
            ]);

        /*
         * Create employees with their related users.
         */
        for ($i = 0; $i < $n; $i++) {
            Employee::factory()->create([
                'user_id' => User::factory()->create()->id,
                'department_id' => $department->id,
                'job_title_id' => $jobTitle->id,
                'employee_number' => 'EMP-00' . ($i + 1),
                'national_id' => '123456789' . $i,
            ]);
        }

        $employees = Employee::all();

        $activeCycles = PerformanceCycle::where(
            'status',
            'Active'
        )->get();

        /*
         * Select the first employee as manager.
         */
        $manager = $employees->first();

        $subordinates = $employees->where(
            'id',
            '!=',
            $manager->id
        );

        /*
         * Assign all other employees to the manager.
         */
        foreach ($subordinates as $subordinate) {
            $subordinate->update([
                'manager_id' => $manager->id,
            ]);
        }

        /*
         * Create performance reviews for a subset
         * of employees in every active cycle.
         */
        foreach ($activeCycles as $cycle) {
            foreach ($subordinates->take(5) as $employee) {
                PerformanceReview::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'performance_cycle_id' => $cycle->id,
                    ],
                    [
                        'reviewer_id' => $manager->id,
                        'status' => 'Draft',
                        'score' => rand(1, 5),
                        'comments' => 'Sample review comment generated automatically.',
                    ]
                );
            }
        }
    }
}