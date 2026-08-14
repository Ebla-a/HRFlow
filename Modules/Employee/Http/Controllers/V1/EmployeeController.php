<?php

declare(strict_types=1);

namespace Modules\Employee\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Modules\Employee\App\Actions\HireEmployeeAction;
use Modules\Employee\App\Actions\TerminateEmployeeAction;
use Modules\Employee\App\Actions\UpdateEmployeeAction;
use Modules\Employee\App\DTOs\CreateEmployeeDTO;
use Modules\Employee\App\DTOs\TerminateEmployeeDTO;
use Modules\Employee\App\DTOs\UpdateEmployeeDTO;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Http\Requests\EmployeeFilterRequest;
use Modules\Employee\Http\Requests\StoreEmployeeRequest;
use Modules\Employee\Http\Requests\TerminateEmployeeRequest;
use Modules\Employee\Http\Requests\UpdateEmployeeRequest;
use Modules\Employee\Http\Resources\V1\EmployeeListResource;
use Modules\Employee\Http\Resources\V1\EmployeeResource;
use Modules\Employee\Services\EmployeeService;
use Illuminate\Auth\Access\AuthorizationException;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeService $employeeService
    ) {
    }

    /**
     * Display a paginated list of employees.
     */
    public function index(EmployeeFilterRequest $request)
    {

        $this->authorize('viewAny', Employee::class);

        $employees = $this->employeeService->getPaginatedEmployees(
            $request->validated()
        );

        return EmployeeListResource::collection($employees);
    }

    /**
     * Store a newly created employee.
     */
    public function store(
        StoreEmployeeRequest $request,
        HireEmployeeAction $action
    ) {
        //$this->authorize('create', Employee::class);

        $dto = CreateEmployeeDTO::fromRequest($request);

        $employee = $action->execute($dto);

        return new EmployeeResource($employee);
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        $this->authorize('view', $employee);

        return new EmployeeResource(
            $this->employeeService->show($employee)
        );
    }

    /**
     * Update the specified employee.
     */
    public function update(
        UpdateEmployeeRequest $request,
        Employee $employee,
        UpdateEmployeeAction $action
    ) {
        $this->authorize('update', $employee);

        $dto = UpdateEmployeeDTO::fromRequest($request);

        $updatedEmployee = $action->execute(
            $employee,
            $dto
        );

        return new EmployeeResource($updatedEmployee);
    }

    /**
     * Terminate the specified employee.
     */
    public function terminate(
        TerminateEmployeeRequest $request,
        Employee $employee,
        TerminateEmployeeAction $action
    ) {
        $this->authorize('terminate', $employee);

        $dto = TerminateEmployeeDTO::fromRequest($request);

        $terminatedEmployee = $action->execute(
            $employee,
            $dto
        );

        return new EmployeeResource($terminatedEmployee);
    }

    /**
     * Display the authenticated employee profile.
     */
    public function me()
    {
        $employee = auth('sanctum')->user()?->employee;

        abort_if(
            $employee === null,
            404,
            'Employee record not found.'
        );

        return new EmployeeResource(
            $this->employeeService->show($employee)
        );
    }
}
