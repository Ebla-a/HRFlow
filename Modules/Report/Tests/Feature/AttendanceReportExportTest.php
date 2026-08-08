<?php

namespace Modules\Report\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Entities\User;
use Modules\Employee\Entities\Employee;
use Modules\Attendance\Entities\Attendance;
use Modules\User\Database\Seeders\RolesAndPermissionsSeeder;
use Spatie\Permission\Models\Role;

class AttendanceReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_admin_can_download_attendance_report_excel(): void
    {

        $this->seed(RolesAndPermissionsSeeder::class);


        $employee = Employee::factory()->create();

    
        Attendance::create([
            'employee_id'     => $employee->id,
            'attendance_date' => '2026-08-01',
            'status'          => 'present',
            'worked_minutes'  => 480,
            'late_minutes'    => 0,
            'overtime_minutes' => 0,
        ]);

  
        /** @var User $hrUser */
        $hrUser = User::factory()->create();
        $role = Role::where('name', 'Hr_admin')->where('guard_name', 'sanctum')->first();
        $hrUser->assignRole($role);

        $response = $this->actingAs($hrUser, 'sanctum')
                         ->get('/api/report/attendance/export?month=8&year=2026');

    
        $response->assertStatus(200);
    }
}