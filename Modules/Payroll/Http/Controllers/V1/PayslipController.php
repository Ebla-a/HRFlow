<?php

namespace Modules\Payroll\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Payroll\App\Actions\Payslip\AddPayslipDeductionAction;
use Modules\Payroll\App\Actions\Payslip\GeneratePayslipAction;
use Modules\Payroll\App\DTOs\DeductionDTO;
use Modules\Payroll\App\Enums\PayslipDeductionType;
use Modules\Payroll\Entities\Payslip;
use Modules\Payroll\Http\Requests\AddPayslipDeductionRequest;

class PayslipController extends Controller
{
    public function show(Payslip $payslip, GeneratePayslipAction $action): JsonResponse
    {
        return response()->json([
            'data' => $action->execute($payslip),
        ]);
    }

    public function addDeduction(
        AddPayslipDeductionRequest $request,
        Payslip $payslip,
        AddPayslipDeductionAction $action
    ): JsonResponse {
        $validated = $request->validated();

        $dto = new DeductionDTO(
            type: PayslipDeductionType::from($validated['type']),
            amount: (float) $validated['amount'],
            description: $validated['description']
        );

        $updatedPayslip = $action->execute($payslip, $dto);

        return $this->success([ 'data' => $updatedPayslip,    'message' => 'Deduction added successfully.',]);
    }
}