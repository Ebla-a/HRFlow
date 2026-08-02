<?php

namespace Modules\Organization\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Employee\Entities\Employee;
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

$techDept = Department::firstOrCreate(
            ['code' => 'DEP-IT'],
            ['name' => 'IT & Technology', 'is_active' => true,]
        );

        $hrDept = Department::firstOrCreate(
            ['code' => 'DEP-HR'],
            ['name' => 'Human Resources', 'is_active' => true, ]
        );

        $softwareDept = Department::firstOrCreate(
            ['code' => 'DEP-DEV'],
            ['name' => 'Software Development', 'parent_id' => $techDept->id, 'is_active' => true,]
        );

        $infrastructureDept = Department::firstOrCreate(
            ['code' => 'DEP-OPS'],
            ['name' => 'Network & Infrastructure', 'parent_id' => $techDept->id, 'is_active' => true, ]
        );

        // -------------------------------------------------------------
        // 2️⃣ إنشاء المسميات الوظيفية المحددة للشركة (Defined Job Titles)
        // -------------------------------------------------------------
        $definedJobTitles = [
            [
                'title'         => 'Backend Developer',
                'grade'         => 'junior',
                'department_id' => $softwareDept->id,
                'description'   => 'Manage backend APIs and application infrastructure.',
            ],
            [
                'title'         => 'Senior Backend Developer',
                'grade'         => 'senior',
                'department_id' => $softwareDept->id,
                'description'   => 'Architecture, microservices, and system performance optimization.',
            ],
            [
                'title'         => 'Frontend Developer',
                'grade'         => 'junior',
                'department_id' => $softwareDept->id,
                'description'   => 'Manage UI/UX and integrate frontend with APIs.',
            ],
            [
                'title'         => 'Network Specialist',
                'grade'         => 'senior',
                'department_id' => $infrastructureDept->id,
                'description'   => 'Manage company network security and servers infrastructure.',
            ],
            [
                'title'         => 'HR Specialist',
                'grade'         => 'junior',
                'department_id' => $hrDept->id,
                'description'   => 'Manage employee records, onboarding, and recruitment.',
            ],
        ];

        foreach ($definedJobTitles as $job) {
            JobTitle::firstOrCreate(
                ['title' => $job['title'], 'department_id' => $job['department_id']],
                array_merge($job, ['is_active' => true])
            );
        }

        // -------------------------------------------------------------
        // 3️⃣ توليد أقسام ومسميات عشوائية كثيرة للاختبار (Dynamic Mock Data)
        // -------------------------------------------------------------
        // إنشاء 5 أقسام فرعية إضافية عشوائية تحت قسم IT
        $randomSubDepartments = Department::factory()
            ->count(5)
            ->withParent($techDept->id)
            ->create();

        // لكل قسم فرعي تم إنشاؤه، نولد 3 مسميات وظيفية عشوائية
        $randomSubDepartments->each(function ($dept) {
            JobTitle::factory()
                ->count(3)
                ->create(['department_id' => $dept->id]);
        });
    }
}
