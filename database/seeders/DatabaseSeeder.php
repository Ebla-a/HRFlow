<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\User\Database\Seeders\RolesAndPermissionsSeeder;
use Modules\Employee\Database\Seeders\EmployeeSeeder;
use Modules\Performance\Database\Seeders\PerformanceDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
       
            RolesAndPermissionsSeeder::class,

           
            EmployeeSeeder::class,

            
            PerformanceDatabaseSeeder::class,
        ]);
    }
}