<?php
namespace Modules\Leave\Repositories;

use Modules\Leave\Entities\LeaveBalance;
use Modules\Leave\Repositories\Interfaces\LeaveBalanceRepositoryInterface;

class LeaveBalanceRepository implements LeaveBalanceRepositoryInterface
{



    /**
     * Summary of getEmployeeBalances
     * @param int $employeeId
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection<int, LeaveBalance>|\Illuminate\Support\Collection<int, \stdClass>
     */
    public function getEmployeeBalances(int $employeeId, int $year)
    {
        return LeaveBalance::where('employee_id', $employeeId)
            ->where('year', $year)
            ->where('status', 'active')
            ->with('leaveType:id,name,is_paid')
            ->get();
    }
}
