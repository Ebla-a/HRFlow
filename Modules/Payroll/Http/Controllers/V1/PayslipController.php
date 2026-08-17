<?php

namespace Modules\Payroll\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Payroll\App\Actions\Payslip\AddPayslipDeductionAction;
use Modules\Payroll\App\Actions\Payslip\GeneratePayslipAction;
use Modules\Payroll\App\DTOs\DeductionDTO;
use Modules\Payroll\App\Enums\PayslipDeductionType;
use Modules\Payroll\Entities\PayrollRun;
use Modules\Payroll\Entities\Payslip;
use Modules\Payroll\Http\Requests\AddPayslipDeductionRequest;
use Modules\Payroll\Http\Resources\V1\PayslipResource;

class PayslipController extends Controller
{
    public function index(Request $request, PayrollRun $payrollRun): JsonResponse
    {
        $this->authorize('viewAny', Payslip::class);

        $query = $payrollRun->payslips()->with(['employee', 'deductions']);

        if ($request->has('include')) {
            $relations = explode(',', $request->input('include'));
            $query->with($relations);
        }

        $payslips = $query->paginate(15);

        return response()->json(PayslipResource::collection($payslips)->response()->getData(true));
    }

    public function myPayslips(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Payslip::class);

        $user = $request->user();
        $employeeId = $user->employee?->id;

        $query = Payslip::forEmployee($employeeId)->with(['employee', 'deductions']);

        if ($request->has('include')) {
            $relations = explode(',', $request->input('include'));
            $query->with($relations);
        }

        $payslips = $query->paginate(15);

        return response()->json(PayslipResource::collection($payslips)->response()->getData(true));
    }

    public function show(Request $request, $id, GeneratePayslipAction $action): JsonResponse
    {
        $payslip = Payslip::findOrFail($id);

        $this->authorize('view', $payslip);

        $action->execute($payslip);

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
        $id,
        AddPayslipDeductionAction $action
    ): JsonResponse {
        $payslip = Payslip::findOrFail($id);

        $this->authorize('update', $payslip);

        $validated = $request->validated();

        $dto = new DeductionDTO(
            type: PayslipDeductionType::from($validated['type']),
            amount: (float) $validated['amount'],
            description: $validated['description']
        );

        $updatedPayslip = $action->execute($payslip, $dto);
        $updatedPayslip->load(['employee', 'deductions']);

        return response()->json([
            'message' => __('Deduction added successfully.'),
            'data' => new PayslipResource($updatedPayslip),
        ]);
    }
}