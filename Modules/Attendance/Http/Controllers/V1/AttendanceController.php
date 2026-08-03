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


/**
 * Summary of AttendanceController
 */
class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $service
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
            $this->service->filter($dto)
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
    public function check(CheckAttendanceRequest $request)  
    {
        $dto = CheckAttendanceDTO::fromRequest($request);

        $attendance = $this->service->check($dto);

        return new AttendanceResource($attendance);
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

        $attendance = $this->service->update($attendance, $dto);

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

    $logs = $this->service->logs($dto);

    return AttendanceLogResource::collection($logs);
  }
   }
