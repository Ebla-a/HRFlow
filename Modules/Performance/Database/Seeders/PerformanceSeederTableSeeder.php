<?php

namespace Modules\Performance\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\Performance_cycle;
use Modules\Performance\Entities\Performance_review;

class PerformanceSeederTableSeeder extends Seeder
{
    public function run(): void
    {
       
        $activeCycles = Performance_cycle::where('status', 'Active')->get();
        if ($activeCycles->isEmpty()) {
            $activeCycles = Performance_cycle::factory()->count(2)->create(['status' => 'Active']);
        }

   
        $employees = Employee::all();
        if ($employees->count() < 2) {
            $employees = Employee::factory()->count(10)->create();
        }

       
        $manager = $employees->first();
        $subordinates = $employees->where('id', '!=', $manager->id);

        foreach ($subordinates as $subordinate) {
            $subordinate->update(['manager_id' => $manager->id]);
        }

     
        foreach ($activeCycles as $cycle) {
            foreach ($subordinates->take(5) as $employee) {
                Performance_review::firstOrCreate(
                    [
                        'employee_id'          => $employee->id,
                        'performance_cycle_id' => $cycle->id,
                    ],
                    [
                        'reviewer_id' => $manager->id,
                        'status'      => 'Draft',
                        'score'       => rand(1, 5),
                        'comments'    => 'Sample review comment generated automatically.',
                    ]
                );
            }
        }
    }
}