<?php

namespace Modules\Payroll\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Payroll\App\Actions\PayrollRun\CreatePayrollRunAction;
use Modules\Payroll\App\Actions\PayrollRun\FinalizePayrollRunAction;
use Modules\Payroll\App\Actions\PayrollRun\ProcessPayrollRunAction;
use Modules\Payroll\App\DTOs\PayrollRunDTO;
use Modules\Payroll\Entities\PayrollRun;
use Modules\Payroll\Entities\Payslip;
use Modules\Payroll\Http\Requests\StorePayrollRunRequest;
use Modules\Payroll\Http\Resources\V1\PayrollRunResource;

use Modules\Payroll\Http\Resources\V1\PayslipResource as V1PayslipResource;

use Modules\Payroll\Services\GetPayrollSummaryService;

class PayrollRunController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', PayrollRun::class);

        $query = PayrollRun::query()->latest();

        if ($request->has('include')) {
            $relations = explode(',', $request->input('include'));
            $query->with($relations);
        }

        $runs = $query->paginate(15);

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
        $run->load(['processedBy', 'finalizedBy']);

        return response()->json([
            'message' => 'Payroll run created successfully.',
            'data' => new PayrollRunResource($run),
        ], 201);
    }

    public function process(Request $request, PayrollRun $payrollRun, ProcessPayrollRunAction $action): JsonResponse
    {
        $this->authorize('process', $payrollRun);

        $run = $action->execute($payrollRun, $request->user()->id);
        $run->load(['processedBy', 'payslips.employee', 'payslips.deductions']);

        return response()->json([
            'message' => 'Payroll run processed successfully.',
            'data' => new PayrollRunResource($run),
        ]);
    }

    public function finalize(Request $request, PayrollRun $payrollRun, FinalizePayrollRunAction $action): JsonResponse
    {
        $this->authorize('finalize', $payrollRun);

        $run = $action->execute($payrollRun, $request->user()->id);
        $run->load(['finalizedBy']);

        return response()->json([
            'message' => 'Payroll run finalized successfully.',
            'data' => new PayrollRunResource($run),
        ]);
    }

    public function summary(PayrollRun $payrollRun, GetPayrollSummaryService $service): JsonResponse
    {
        $this->authorize('view', $payrollRun);

        return response()->json([
            'data' => $service->getSummary($payrollRun),
        ]);
    }

   public function payslips(Request $request, PayrollRun $payrollRun): JsonResponse
    {
        $query = $payrollRun->payslips()->with(['employee', 'deductions']);

        if ($request->has('include')) {
            $relations = explode(',', $request->input('include'));
            $query->with($relations);
        }

        $payslips = $query->paginate(15);

        return response()->json(V1PayslipResource::collection($payslips)->response()->getData(true));
    }
}