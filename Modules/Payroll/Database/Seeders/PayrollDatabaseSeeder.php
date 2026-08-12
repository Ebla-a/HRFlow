<?php

namespace Modules\Payroll\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Employee;
use Modules\Payroll\Entities\SalaryStructure;

class PayrollDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $employees = Employee::all();

        foreach ($employees as $employee) {
            SalaryStructure::updateOrCreate(
                ['employee_id' => $employee->id],
                SalaryStructure::factory()->raw([
                    'employee_id' => $employee->id,
                ])
            );
        }
    }
}