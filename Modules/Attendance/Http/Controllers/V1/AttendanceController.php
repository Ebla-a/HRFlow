<?php

namespace Modules\Attendance\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Modules\Attendance\Services\AttendanceService;
use Modules\Attendance\Http\Requests\CheckAttendanceRequest;
use Modules\Attendance\Http\Requests\FilterAttendanceRequest;
use Modules\Attendance\Http\Requests\UpdateAttendanceRequest;
use Modules\Attendance\Http\Requests\FilterAttendanceLogRequest;
use Modules\Attendance\Http\Resources\AttendanceResource;
use Modules\Attendance\Http\Resources\AttendanceLogResource;
use Modules\Attendance\Entities\Attendance;
use Modules\Attendance\DTOs\CheckAttendanceDTO;
use Modules\Attendance\DTOs\FilterAttendanceDTO;
use Modules\Attendance\DTOs\UpdateAttendanceDTO;
use Modules\Attendance\DTOs\FilterAttendanceLogDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


/**
 * Summary of AttendanceController
 */
class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService
    ) {}
    /**
     * Summary of index
     * @param FilterAttendanceRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(FilterAttendanceRequest $request)
    {
        $dto = FilterAttendanceDTO::fromRequest($request);

        return AttendanceResource::collection(
            $this->attendanceService->filter($dto)
        );
    }
    /**
     * Summary of show
     * @param Attendance $attendance
     * @return AttendanceResource
     */
    public function show(Attendance $attendance)
    {
        return new AttendanceResource(
            $attendance->load('employee')
        );
    }
    /**
     * Summary of check
     * @param CheckAttendanceRequest $request
     * @return AttendanceResource
     */
    public function check(CheckAttendanceRequest $request): JsonResponse
    {

        $this->authorize('create', Attendance::class);

        $dto = new CheckAttendanceDTO(
            employeeId: $request->input('employee_id'),
            type: $request->input('type')
        );

        $attendance = $this->attendanceService->check($dto);

        return Controller::success(
            new AttendanceResource($attendance),
            'Attendance record created successfully',
            201
        );
    }


    /**
     * Summary of update
     * @param UpdateAttendanceRequest $request
     * @param Attendance $attendance
     * @return AttendanceResource
     */
    public function update(
        UpdateAttendanceRequest $request,
        Attendance $attendance
    ) {
        $dto = UpdateAttendanceDTO::fromRequest($request);

        $attendance = $this->attendanceService->update($attendance, $dto);

        return new AttendanceResource($attendance);
    }
    /**
     * Summary of logs
     * @param FilterAttendanceLogRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function logs(FilterAttendanceLogRequest $request)
    {
        $dto = FilterAttendanceLogDTO::fromRequest($request);

        $logs = $this->attendanceService->logs($dto);

        return AttendanceLogResource::collection($logs);
    }

    /**
     * Fetch personal attendance history for the authenticated user.
     * Access: Employee (attendence.view.own)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myAttendance(Request $request): JsonResponse
    {

        $this->authorize('viewOwn', Attendance::class);

        $employeeId = $request->user()->employee?->id;

        $attendances = Attendance::query()
            ->where('employee_id', $employeeId)
            ->latest('attendance_date')
            ->paginate($request->get('per_page', 15));


        return $this->success(
            [
                $attendances,
                'Personal attendance records retrieved successfully'
            ]
        );
    }


    /**
     * Display the monthly attendance summary.
     * Access: Manager / HR Admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function summary(Request $request): JsonResponse
    {
        $this->authorize('viewSummary', Attendance::class);

        $month = $request->get('month', now()->format('Y-m'));

        $summary = Attendance::query()
            ->selectRaw("
            COUNT(id) as total_records,
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as total_present,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as total_absent,
            SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as total_late,
            SUM(CASE WHEN status = 'on_leave' THEN 1 ELSE 0 END) as total_on_leave,
            SUM(CASE WHEN status = 'holiday' THEN 1 ELSE 0 END) as total_holiday,
            SUM(worked_minutes) as total_worked_minutes,
            SUM(late_minutes) as total_late_minutes,
            SUM(overtime_minutes) as total_overtime_minutes
        ")
            ->whereRaw("DATE_FORMAT(attendance_date, '%Y-%m') = ?", [$month])
            ->first();

        return Controller::success(
            $summary,
            'Attendance summary retrieved successfully'
        );
    }
}
