<?php

namespace Modules\Payroll\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Payroll\App\Actions\SalaryStructure\UpdateSalaryStructureAction;
use Modules\Payroll\App\DTOs\UpdateSalaryStructureDTO;
use Modules\Payroll\Entities\SalaryStructure;
use Modules\Payroll\Services\SalaryHistoryService;

class SalaryStructureController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', SalaryStructure::class);

        $query = SalaryStructure::query()->with('employee')->latest();

        if ($request->has('employee_id')) {
            $query->forEmployee($request->input('employee_id'));
        }

        $structures = $query->paginate(15);

        return response()->json($structures);
    }

    public function store(Request $request, SalaryHistoryService $historyService): JsonResponse
    {
        $this->authorize('create', SalaryStructure::class);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id|unique:salary_structures,employee_id',
            'basic_salary' => 'required|numeric|min:0',
            'housing_allowance' => 'required|numeric|min:0',
            'transport_allowance' => 'required|numeric|min:0',
            'other_allowance' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
        ]);

        $structure = SalaryStructure::create($validated);

        return response()->json([
            'message' => 'Salary structure created successfully.',
            'data' => $structure->load('employee'),
        ], 201);
    }

   public function update(
        Request $request,
        SalaryStructure $salaryStructure,
        UpdateSalaryStructureAction $action
    ): JsonResponse {
        $this->authorize('update', $salaryStructure);

        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'housing_allowance' => 'required|numeric|min:0',
            'transport_allowance' => 'required|numeric|min:0',
            'other_allowance' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'reason' => 'required|string|max:255',
        ]);

        $dto = new UpdateSalaryStructureDTO(
            basic_salary: (float) $validated['basic_salary'],
            housing_allowance: (float) $validated['housing_allowance'],
            transport_allowance: (float) $validated['transport_allowance'],
            other_allowance: (float) $validated['other_allowance'],
            effective_date: $validated['effective_date'],
            reason: $validated['reason']
        );

        $updatedStructure = $action->execute($salaryStructure, $dto, $request->user()->id);

        return response()->json([
            'message' => 'Salary structure updated successfully.',
            'data' => $updatedStructure->fresh('employee'),
        ]); }
}