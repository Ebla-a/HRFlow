<?php
namespace Modules\Leave\Contracts;

use Modules\AI\Contracts\AiToolInterface;
use Modules\Leave\Services\LeaveBalanceService;

class GetLeaveBalanceTool implements AiToolInterface
{

public function __construct(private LeaveBalanceService $leaveService) {}

    public function name(): string
    {
        return 'get_leave_balance';
    }

   public function description(): string
{
    return 'تُستخدم لجلب رصيد الإجازات السنوية والمرضية المتبقية والمستهلكة للموظف الحالي تلقائياً بدون الحاجة لسؤاله عن رقمه الوظيفي.';
}

public function parameters(): array
{
    return [
        'type' => 'OBJECT',
        'properties' => (object) [],
    ];
}

    public function execute(array $arguments, int $userId): array
    {
        return $this->leaveService->getUserBalance($userId);
    }

}
