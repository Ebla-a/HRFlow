<?php

namespace Modules\Performance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\performanceCycle;
use Modules\Performance\Entities\performanceReview;
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

        PerformanceCycle::factory()->count(5)->create();
        $employees = Employee::all();
        if ($employees->count() < 2) {
            $employees = Employee::factory()->count(10)->create();
        }

        $activeCycles = performanceCycle::where('status', 'Active')->get();
        if ($activeCycles->isEmpty()) {
            $activeCycles = performanceCycle::factory()->count(3)->create(['status' => 'Active']);
        }

        for ($i = 0; $i < 20; $i++) {
            $employee = $employees->random();
            $reviewer = $employees->where('id', '!=', $employee->id)->random();

            PerformanceReview::factory()->create([
                'employee_id' => $employee->id,
                'reviewer_id' => $reviewer->id,
                'cycle_id'    => $activeCycles->random()->id,
            ]);
        }
    }
}
