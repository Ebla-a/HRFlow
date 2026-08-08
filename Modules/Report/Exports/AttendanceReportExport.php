<?php

namespace Modules\Report\Exports;


class AttendanceReportExport 
{
    public function __construct(private array $reportData) {}

    public function array(): array
    {
        return [
            ['Present', $this->reportData['status_breakdown']['present']],
            ['Absent', $this->reportData['status_breakdown']['absent']],
            ['Late', $this->reportData['status_breakdown']['late']],
            ['On Leave', $this->reportData['status_breakdown']['on_leave']],
        ];
    }

    public function headings(): array
    {
        return ['Status', 'Total Count'];
    }

    public function title(): string
    {
        return 'Attendance Summary';
    }
}