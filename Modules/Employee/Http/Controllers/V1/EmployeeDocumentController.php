<?php

namespace Modules\Employee\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\App\Actions\UploadEmployeeDocumentAction;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\EmployeeDocument;
use Modules\Employee\Http\Requests\UploadEmployeeDocumentRequest;
use Modules\Employee\Http\Resources\V1\EmployeeDocumentResource;
use Modules\Employee\Services\EmployeeDocumentService;

class EmployeeDocumentController extends Controller
{
    public function __construct(
        protected EmployeeDocumentService $documentService
    ) {}

    /**
     * Display a listing of the employee's documents.
     */
    public function index(Employee $employee)
    {
        $this->authorize('viewAny', [EmployeeDocument::class, $employee]);

        return EmployeeDocumentResource::collection($employee->documents);
    }

    /**
     * Store a newly created document for an employee.
     */
    public function store(UploadEmployeeDocumentRequest $request, Employee $employee, UploadEmployeeDocumentAction $action)
    {
        $this->authorize('store', [EmployeeDocument::class, $employee]);

        $document = $action->execute(
            $employee,
            $request->file('file'),
            $request->input('title'),
            $request->input('type'),
            auth('sanctum')->id()
        );

        return new EmployeeDocumentResource($document);
    }

    /**
     * Update or replace an existing document file and metadata.
     */
    public function update(UploadEmployeeDocumentRequest $request, EmployeeDocument $document)
    {
        $this->authorize('update', $document);

        $updatedDocument = $this->documentService->update(
            $document,
            $request->file('file'),
            $request->input('title'),
            $request->input('type')
        );

        return new EmployeeDocumentResource($updatedDocument);
    }

    /**
     * Remove the specified document from storage and database.
     */
    public function destroy(EmployeeDocument $document)
    {
        $this->authorize('destroy', $document);

        $this->documentService->delete($document);

        return response()->json(['message' => __('Document deleted successfully')], 200);
    }

    /**
     * Download or stream the specified document securely.
     */
    public function download(EmployeeDocument $document)
    {
        $this->authorize('viewAny', [EmployeeDocument::class, $document->employee]);

        $diskName = $document->disk ?? 'local';
        
        if (!Storage::disk($diskName)->exists($document->file_path)) {
            return response()->json([
                'success' => false,
                'message' => __('File not found on local storage.')
            ], 404);
        }


        return Storage::disk($diskName)->download(
            $document->file_path,
            $document->original_name ?? basename($document->file_path)
        );
    }
}