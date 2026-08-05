<?php

namespace Modules\Performance\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\performance_cycle;
use Modules\Performance\Entities\performance_review;

class PerformanceSeederTableSeeder extends Seeder
{
    public function run(): void
    {
        Performance_cycle::factory()->count(5)->create();

        $employees = Employee::all();

        if ($employees->count() < 2) {
            $employees = Employee::factory()->count(10)->create();
        }

        $activeCycles = performance_cycle::where('status', 'Active')->get();
        if ($activeCycles->isEmpty()) {
            $activeCycles = performance_cycle::factory()->count(3)->create(['status' => 'Active']);
        }

 
        foreach ($activeCycles as $cycle) {
            foreach ($employees->take(5) as $employee) {
                
                $reviewer = $employees->where('id', '!=', $employee->id)->random();

            
                Performance_review::firstOrCreate(
                    [
                        'employee_id'          => $employee->id,
                        'performance_cycle_id' => $cycle->id,
                    ],
                    [
                        'reviewer_id' => $reviewer->id,
                        'status'      => 'Draft',
                        'score'       => rand(1, 5),
                        'comments'    => 'Sample review comment generated automatically.',
                    ]
                );
            }
        }
    }
}