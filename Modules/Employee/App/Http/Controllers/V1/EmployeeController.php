<?php

namespace Modules\Employee\App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Modules\Employee\Entities\Employee;
use Modules\Employee\App\Http\Requests\V1\StoreEmployeeRequest;
use Modules\Employee\App\Http\Requests\V1\UpdateEmployeeRequest;
use Modules\Employee\App\Http\Requests\V1\EmployeeFilterRequest;
use Modules\Employee\App\Http\Requests\V1\TerminateEmployeeRequest;
use Modules\Employee\App\Http\Resources\V1\EmployeeResource;
use Modules\Employee\App\Http\Resources\V1\EmployeeListResource;
use Modules\Employee\App\Services\EmployeeService;


class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeService $employeeService
    ) {}


   public function index(EmployeeFilterRequest $request)
{
    $employees = $this->employeeService->getPaginatedEmployees(
        $request->validated()
    );

    return EmployeeListResource::collection($employees);
}


    public function store(StoreEmployeeRequest $request)
    {
        $employee = $this->employeeService->store(
            $request->validated()
        );

        return new EmployeeResource($employee);
    }


    public function show(Employee $employee)
    {
        return new EmployeeResource(
            $this->employeeService->show($employee)
        );
    }


    public function update(
        UpdateEmployeeRequest $request,
        Employee $employee
    ) {

        $employee = $this->employeeService->update(
            $employee,
            $request->validated()
        );

        return new EmployeeResource($employee);
    }


    public function terminate(
        TerminateEmployeeRequest $request,
        Employee $employee
    ) {

        $employee = $this->employeeService->terminate(
            $employee,
            $request->input('termination_reason')
        );

        return new EmployeeResource($employee);
    }
}