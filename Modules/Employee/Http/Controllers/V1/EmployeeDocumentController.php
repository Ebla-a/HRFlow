<?php

namespace Modules\Employee\App\Http\Controllers\V1;

use App\Http\Controllers\Controller;

use Modules\Employee\App\Actions\UploadEmployeeDocumentAction;
use Modules\Employee\App\Http\Requests\V1\UploadEmployeeDocumentRequest;
use Modules\Employee\App\Http\Resources\V1\EmployeeDocumentResource;
use Modules\Employee\Entities\Employee;

class EmployeeDocumentController extends Controller
{
    public function index(Employee $employee)
    {
        $this->authorize('view', $employee);

        return EmployeeDocumentResource::collection($employee->documents);
    }

    public function store(UploadEmployeeDocumentRequest $request, Employee $employee, UploadEmployeeDocumentAction $action)
    {
        $this->authorize('uploadDocument', $employee);

        $document = $action->execute(
            $employee,
            $request->file('file'),
            $request->input('title'),
            $request->input('type'),
            auth('sanctum')->id()
        );

        return new EmployeeDocumentResource($document);
    }
}