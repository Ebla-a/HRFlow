<?php

namespace Modules\Attendance\Services;

use Modules\Attendance\Entities\Attendance;
use Modules\Attendance\Entities\AttendanceLog;

use Modules\Attendance\DTOs\CheckAttendanceDTO;
use Modules\Attendance\DTOs\FilterAttendanceDTO;
use Modules\Attendance\DTOs\FilterAttendanceLogDTO;
use Modules\Attendance\DTOs\UpdateAttendanceDTO;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;


class AttendanceService
{
    /**
     * Summary of check
     * @param CheckAttendanceDTO $dto
     */
    public function check(CheckAttendanceDTO $dto)
    {
        return DB::transaction(function () use ($dto) {

            $employeeId = $dto->employeeId;
            $type = $dto->type;

            $now = Carbon::now();
            $today = $now->toDateString();


            $attendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('attendance_date', $today)
                ->lockForUpdate()
                ->first();


            try {

                if ($type === 'check_in') {

                    $attendance = $this->handleCheckIn(
                        $attendance,
                        $employeeId,
                        $now,
                        $today
                    );


                } elseif ($type === 'check_out') {


                    $attendance = $this->handleCheckOut(
                        $attendance,
                        $now
                    );


                } else {

                    throw new Exception('Invalid attendance type');

                }

                AttendanceLog::create([
                    'employee_id' => $employeeId,
                    'logged_at' => $now,
                    'type' => $type,
                    'result' => 'success',
                ]);


                return $attendance;



            } catch(Exception $e) {


                AttendanceLog::create([
                    'employee_id' => $employeeId,
                    'logged_at' => $now,
                    'type' => $type,
                    'result' => 'failed',
                    'message' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Summary of handleCheckIn
     * @param mixed $attendance
     * @param mixed $employeeId
     * @param mixed $now
     * @param mixed $today
     * @throws Exception
     */
    private function handleCheckIn($attendance, $employeeId, $now, $today)
    {

        if ($attendance && $attendance->check_in && !$attendance->check_out) {

            throw new Exception('Already checked in');

        }

        if ($attendance && $attendance->check_out) {

            throw new Exception('Already completed attendance for today');
        }

        $officialStartTime = config('attendance.start_time', '08:00');

        $officialStart = Carbon::today()
            ->setTimeFromTimeString($officialStartTime);

        $lateMinutes = $now->greaterThan($officialStart)
            ? $officialStart->diffInMinutes($now)
            : 0;


        $status = $lateMinutes > 0
            ? 'late'
            : 'present';


        return Attendance::create([

            'employee_id' => $employeeId,

            'attendance_date' => $today,

            'check_in' => $now,

            'late_minutes' => $lateMinutes,

            'status' => $status,

        ]);

    }
   

    private function handleCheckOut($attendance, $now)
    {

        if (!$attendance || !$attendance->check_in) {

            throw new Exception('Cannot check out without check in');

        }


        if ($attendance->check_out) {

            throw new Exception('Already checked out');

        }

        $workedMinutes = $attendance->check_in
            ->diffInMinutes($now);


        $officialEndTime = config('attendance.end_time', '17:00');

        $officialEnd = Carbon::today()
            ->setTimeFromTimeString($officialEndTime);
        $overtime = $now->greaterThan($officialEnd)
            ? $officialEnd->diffInMinutes($now)
            : 0;
        $attendance->update([

            'check_out' => $now,

            'worked_minutes' => $workedMinutes,

            'overtime_minutes' => $overtime,

            'status' => $attendance->status,

        ]);



        return $attendance->fresh();

    }
    /**
     * Summary of update
     * @param Attendance $attendance
     * @param UpdateAttendanceDTO $dto
     * @return Attendance|null
     */
    public function update(
        Attendance $attendance,
        UpdateAttendanceDTO $dto
    )
    {

        $attendance->update($dto->toArray());


        return $attendance->fresh();

    }
    /**
     * Summary of filter
     * @param FilterAttendanceDTO $dto
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, Attendance>
     */
    public function filter(FilterAttendanceDTO $dto)
    {

        $filters = $dto->toArray();



        return Attendance::query()

            ->with('employee')


            ->when(
                $filters['employee_id'] ?? null,
                fn($q,$id) =>
                    $q->where('employee_id',$id)
            )


            ->when(
                isset($filters['late']),
                function($q) use ($filters){

                    return $filters['late']
                        ? $q->where('late_minutes','>',0)
                        : $q->where('late_minutes',0);

                }
            )


            ->when(
                $filters['status'] ?? null,
                fn($q,$status) =>
                    $q->where('status',$status)
            )


            ->when(
                $filters['from_date'] ?? null,
                fn($q,$date) =>
                    $q->whereDate('attendance_date','>=',$date)
            )


            ->when(
                $filters['to_date'] ?? null,
                fn($q,$date) =>
                    $q->whereDate('attendance_date','<=',$date)
            )


            ->when(
                ($filters['sort_by'] ?? null) === 'arrival',
                fn($q)=>
                    $q->orderBy('check_in')
            )


            ->when(
                ($filters['sort_by'] ?? null) === 'late',
                fn($q)=>
                    $q->orderByDesc('late_minutes')
            )


            ->paginate($filters['per_page'] ?? 10);

    }
     

    /**
     * Summary of logs
     * @param FilterAttendanceLogDTO $dto
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, AttendanceLog>
     */
    public function logs(FilterAttendanceLogDTO $dto)
    {

        $filters = $dto->toArray();

        return AttendanceLog::query()

            ->when(
                $filters['employee_id'] ?? null,
                fn($query,$employeeId)=>
                    $query->where('employee_id',$employeeId)
            )


            ->when(
                $filters['type'] ?? null,
                fn($query,$type)=>
                    $query->where('type',$type)
            )


            ->when(
                $filters['result'] ?? null,
                fn($query,$result)=>
                    $query->where('result',$result)
            )


            ->when(
                $filters['from_date'] ?? null,
                fn($query,$date)=>
                    $query->whereDate('logged_at','>=',$date)
            )


            ->when(
                $filters['to_date'] ?? null,
                fn($query,$date)=>
                    $query->whereDate('logged_at','<=',$date)
            )


            ->latest('logged_at')


            ->paginate($filters['per_page'] ?? 10);

    }

}