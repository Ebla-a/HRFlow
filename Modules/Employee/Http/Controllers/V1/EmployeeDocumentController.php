<?php

namespace Modules\Employee\App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\App\Actions\UploadEmployeeDocumentAction;
use Modules\Employee\App\Http\Requests\V1\UploadEmployeeDocumentRequest;
use Modules\Employee\App\Http\Resources\V1\EmployeeDocumentResource;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\EmployeeDocument;

class EmployeeDocumentController extends Controller
{
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
     * Update/Replace an existing document file and metadata.
     */
    public function update(UploadEmployeeDocumentRequest $request, EmployeeDocument $document)
    {
        $this->authorize('update', $document);

        if (Storage::disk($document->disk)->exists($document->file_path)) {
            Storage::disk($document->disk)->delete($document->file_path);
        }

        $file = $request->file('file');
        $path = $file->store("employees/{$document->employee_id}/documents", $document->disk);

        $document->update([
            'title' => $request->input('title', $document->title),
            'type' => $request->input('type', $document->type),
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return new EmployeeDocumentResource($document);
    }

    /**
     * Remove the specified document from storage and database.
     */
    public function destroy(EmployeeDocument $document)
    {
        $this->authorize('destroy', $document);

        if (Storage::disk($document->disk)->exists($document->file_path)) {
            Storage::disk($document->disk)->delete($document->file_path);
        }

        $document->delete();
        return $this->success(['message' => 'Document deleted successfully'], 200);
    }



    /**
 * Download or stream the specified document securely.
 */
public function download(EmployeeDocument $document)
{
    
    $this->authorize('viewAny', [EmployeeDocument::class, $document->employee]);
   
    $disk = Storage::disk($document->disk);

   if (!$disk->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

   
   return response()->download(
            $disk->path($document->file_path),
            $document->original_name
        );
}
}
