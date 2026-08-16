<?php
namespace Modules\Leave\Repositories\Interfaces;
interface LeaveBalanceRepositoryInterface{
    public function getEmployeeBalances(int $employeeId, int $year);

}
