<?php

namespace Modules\Organization\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;

class OrganizationDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

    //  $this->call(OrganizationPermissionSeeder::class);

        $techDept = Department::create([
            'name' => ' IT',
            'code' => 'DEP-IT',
            'is_active' => true,
            // 'manager_id'=>1
        ]);

        $hrDept = Department::create([
            'name' => ' HR',
            'code' => 'DEP-HR',
            'is_active' => true,
        ]);

        $softwareDept = Department::create([
            'name' => ' DEVELOPMENT',
            'code' => 'DEP-DEV',
            'parent_id' => $techDept->id,
            'is_active' => true,
        ]);

        $infrastructureDept = Department::create([
            'name' => ' Network & Infrastructure',
            'code' => 'DEP-OPS',
            'parent_id' => $techDept->id,
            'is_active' => true,
        ]);

        JobTitle::create([
            'title' => ' backend developer',
            'grade' => 'junior',
            'department_id' => $softwareDept->id,
            'description' => 'manage the back-end of the application and ensure a smooth user experience',
            'is_active' => true,
        ]);

        JobTitle::create([
            'title' => ' frontend developer',
            'grade' => 'junior',
            'department_id' => $softwareDept->id,
            'description' => 'manage the front-end of the application and ensure a smooth user experience',
            'is_active' => true,
        ]);

        JobTitle::create([
            'title' => ' Network & Infrastructure Specialist',
            'grade' => 'senior',
            'department_id' => $infrastructureDept->id,
            'description' => 'manage the network and infrastructure of the company',
            'is_active' => true,
        ]);

        JobTitle::create([
            'title' => ' HR Specialist',
            'grade' => 'junior',
            'department_id' => $hrDept->id,
            'description' => 'manage the employees and their records',
            'is_active' => true,
        ]);

    }
}
