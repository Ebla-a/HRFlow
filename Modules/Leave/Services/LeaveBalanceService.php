<?php
namespace Modules\Leave\Services;

use Modules\Leave\Repositories\Interfaces\LeaveBalanceRepositoryInterface;

class LeaveBalanceService{
    public function  __construct( protected LeaveBalanceRepositoryInterface $repository
) {}



  /**
   * Summary of getUserBalance
   * @param int $userId
   * @param mixed $year
   * @return array{balances: array, message: string, year: int|array{balances: mixed, total_remaining_days: mixed, year: int}}
   */
  public function getUserBalance(int $userId, ?int $year = null): array
    {
        $currentYear = $year ?? (int) date('Y');

        $balances = $this->repository->getEmployeeBalances($userId, $currentYear);

        if ($balances->isEmpty()) {
            return [
                'year' => $currentYear,
                'message' => 'لا يوجد سجل أرصدة إجازات متاح لهذه السنة.',
                'balances' => [],
            ];
        }

        $formattedBalances = $balances->map(function ($balance) {
            return [
                'leave_type' => $balance->leaveType->name ?? 'غير محدد',
                'total_days' => $balance->total_days,
                'used_days' => $balance->used_days,
                'remaining_days' => $balance->remaining_days,
                'is_paid' => $balance->leaveType->is_paid ?? true,
            ];
        })->toArray();

        return [
            'year' => $currentYear,
            'total_remaining_days' => $balances->sum('remaining_days'),
            'balances' => $formattedBalances,
        ];
    }




}
