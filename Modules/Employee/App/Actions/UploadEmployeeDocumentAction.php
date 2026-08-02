<?php

namespace Modules\Employee\App\Actions;


use Modules\Employee\App\Services\EmployeeDocumentService;
use Illuminate\Http\UploadedFile;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\EmployeeDocument;

class UploadEmployeeDocumentAction
{
    public function __construct(
        protected EmployeeDocumentService $documentService
    ) {}

    public function execute(Employee $employee, UploadedFile $file, string $title, string $type, int $uploaderId): EmployeeDocument
    {
        return $this->documentService->upload($employee, $file, $title, $type, $uploaderId);
    }
}