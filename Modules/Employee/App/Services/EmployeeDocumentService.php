<?php

namespace Modules\Employee\App\Services;



use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\EmployeeDocument;

class EmployeeDocumentService
{
    public function upload(Employee $employee, UploadedFile $file, string $title, string $type, int $uploaderId): EmployeeDocument
    {
        $path = $file->store("employees/{$employee->id}/documents", 'public');

        return EmployeeDocument::create([
            'employee_id' => $employee->id,
            'uploaded_by' => $uploaderId,
            'title' => $title,
            'type' => $type,
            'disk' => 'public',
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }
}