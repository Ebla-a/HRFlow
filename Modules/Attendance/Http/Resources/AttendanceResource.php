<?php

namespace Modules\Attendance\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{

    /**
     * Summary of toArray
     * @param Request $request
     * @return array{attendance_date: mixed, check_in: mixed, check_out: mixed, created_at: mixed, employee: mixed|\Illuminate\Http\Resources\MissingValue, employee_id: mixed, id: mixed, late: bool, late_minutes: mixed, notes: mixed, overtime_minutes: mixed, status: mixed, updated_at: mixed, worked_minutes: mixed, working_hours: float}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'employee_id' => $this->employee_id,

            'employee' => $this->whenLoaded('employee', function () {
                return [
                    'id' => $this->employee->id,
                ];
            }),

            'attendance_date' => $this->attendance_date?->toDateString(),

            'check_in' => $this->check_in?->toIso8601String(),
            'check_out' => $this->check_out?->toIso8601String(),

            'worked_minutes' => $this->worked_minutes,
            'working_hours' => round($this->worked_minutes / 60, 2),

            'late_minutes' => $this->late_minutes,
            'late' => $this->late_minutes > 0,

            'overtime_minutes' => $this->overtime_minutes,

            'status' => __($this->status),
            'notes' => $this->getTranslation('notes', app()->getLocale()),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}