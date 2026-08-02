<?php

namespace Modules\Performance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\performance_cycle;
use Modules\Performance\Entities\performance_review;


class PerformanceSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run(): void
    {
        
        $employees = Employee::all();
        if ($employees->count() < 2) {
            $employees = Employee::factory()->count(10)->create();
        }

        $activeCycles = performance_cycle::where('status', 'Active')->get();
        if ($activeCycles->isEmpty()) {
            $activeCycles = performance_cycle::factory()->count(3)->create(['status' => 'Active']);
        }

        for ($i = 0; $i < 20; $i++) {
            $employee = $employees->random();
            $reviewer = $employees->where('id', '!=', $employee->id)->random();

            performance_review::factory()->create([
                'employee_id' => $employee->id,
                'reviewer_id' => $reviewer->id,
                'cycle_id'    => $activeCycles->random()->id,
            ]);
        }
    }
}
