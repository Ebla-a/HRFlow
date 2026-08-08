<?php

namespace Modules\Payroll\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Payroll\App\Actions\Payslip\AddPayslipDeductionAction;
use Modules\Payroll\App\Actions\Payslip\GeneratePayslipAction;
use Modules\Payroll\App\DTOs\DeductionDTO;
use Modules\Payroll\App\Enums\PayslipDeductionType;
use Modules\Payroll\Entities\Payslip;
use Modules\Payroll\Http\Requests\AddPayslipDeductionRequest;
use Modules\Payroll\Http\Resources\V1\PayslipResource;

class PayslipController extends Controller
{
    public function show(Request $request, Payslip $payslip, GeneratePayslipAction $action): JsonResponse
    {
        $action->execute($payslip);

        // تحميل العلاقات الأساسية أو الإضافية بناءً على الطلب
        $relations = ['employee', 'deductions'];
        if ($request->has('include')) {
            $relations = array_merge($relations, explode(',', $request->input('include')));
        }

        $payslip->load($relations);

        return response()->json([
            'data' => new PayslipResource($payslip),
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
        $updatedPayslip->load(['employee', 'deductions']);

        return response()->json([
            'message' => 'Deduction added successfully.',
            'data' => new PayslipResource($updatedPayslip),
        ]);
    }
}