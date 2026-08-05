<?php

namespace Modules\Payroll\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Payroll\App\Actions\PayrollRun\CreatePayrollRunAction;
use Modules\Payroll\App\Actions\PayrollRun\FinalizePayrollRunAction;
use Modules\Payroll\App\Actions\PayrollRun\ProcessPayrollRunAction;
use Modules\Payroll\App\DTOs\PayrollRunDTO;
use Modules\Payroll\Entities\PayrollRun;
use Modules\Payroll\Http\Requests\StorePayrollRunRequest;
use Modules\Payroll\Http\Resources\V2\PayrollRunResource;
use Modules\Payroll\Services\GetPayrollSummaryService;


class PayrollRunController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', PayrollRun::class);

        $runs = PayrollRun::query()
            ->with(['processedBy', 'finalizedBy'])
            ->latest()
            ->paginate(15);

        return response()->json(PayrollRunResource::collection($runs)->response()->getData(true));
    }

    public function store(StorePayrollRunRequest $request, CreatePayrollRunAction $action): JsonResponse
    {
        $this->authorize('create.payroll.run', PayrollRun::class);

        $validated = $request->validated();

        $dto = new PayrollRunDTO(
            month: $validated['month'],
            year: $validated['year'],
            notes: $validated['notes'] ?? null
        );

        $run = $action->execute($dto);

        return response()->json([
            'message' => 'Payroll run created successfully.',
            'data' => new PayrollRunResource($run->load(['processedBy', 'finalizedBy'])),
        ], 201);
    }

    public function process(Request $request, PayrollRun $payrollRun, ProcessPayrollRunAction $action): JsonResponse
    {
        $this->authorize('process', $payrollRun);

        $run = $action->execute($payrollRun, $request->user()->id);

        return response()->json([
            'message' => 'Payroll run processed successfully.',
            'data' => new PayrollRunResource($run->load(['processedBy', 'payslips.employee', 'payslips.deductions'])),
        ]);
    }

    public function finalize(Request $request, PayrollRun $payrollRun, FinalizePayrollRunAction $action): JsonResponse
    {
        $this->authorize('finalize', $payrollRun);

        $run = $action->execute($payrollRun, $request->user()->id);

        return response()->json([
            'message' => 'Payroll run finalized successfully.',
            'data' => new PayrollRunResource($run->load(['finalizedBy'])),
        ]);
    }

    public function summary(PayrollRun $payrollRun, GetPayrollSummaryService $service): JsonResponse
    {
        $this->authorize('view', $payrollRun);

        return response()->json([
            'data' => $service->getSummary($payrollRun),
        ]);
    }
}