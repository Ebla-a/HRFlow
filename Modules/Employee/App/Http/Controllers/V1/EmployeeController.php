<?php

namespace Modules\Employee\App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
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
    $employees = $this->employeeService->index($request);

    return EmployeeListResource::collection($employees);
}
public function show($id)
{
    $employee = $this->employeeService->show($id);

    return new EmployeeListResource($employee);
}
    
public function store(StoreEmployeeRequest $request)
{
    $employee = $this->employeeService->store($request->validated());

    return new EmployeeResource($employee);
}


public function update(UpdateEmployeeRequest $request, $id)
{
    $employee = $this->employeeService->update($id, $request->validated());

    return new EmployeeResource($employee);
}

public function terminate(TerminateEmployeeRequest $request, $id)
{
    $reason = $request->input('termination_reason');

    $employee = $this->employeeService->terminate($id, $reason);

    return new EmployeeResource($employee);
}
}