<?php

namespace Modules\Leave\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Leave\Entities\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Annual',
                'annual_days' => 21,
                'is_paid' => true,
                'requires_attachment' => false,
            ],
            [
                'name' => 'Sick',
                'annual_days' => 14,
                'is_paid' => true,
                'requires_attachment' => true,
            ],
            [
                'name' => 'Unpaid',
                'annual_days' => 30,
                'is_paid' => false,
                'requires_attachment' => false,
            ],
        ];

        foreach ($types as $type) {
            LeaveType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}