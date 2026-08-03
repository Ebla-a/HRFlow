<?php

namespace Modules\Employee\App\Services;



use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\EmployeeDocument;

class EmployeeDocumentService
{
    public function upload(Employee $employee, UploadedFile $file, string $title, string $type, int $uploaderId): EmployeeDocument
    {
        $disk = config('filesystems.default_private_disk', 'local');
        $path = $file->store("employees/{$employee->id}/documents", $disk);

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


    public function update(EmployeeDocument $document, ?UploadedFile $file = null, ?string $title = null, ?string $type = null): EmployeeDocument
    {
        return DB::transaction(function () use ($document, $file, $title, $type) {


            $updateData = array_filter([
                'title' => $title,
                'type'  => $type,
            ], fn($value) => !is_null($value));


            if ($file) {
                if (Storage::disk($document->disk)->exists($document->file_path)) {
                    Storage::disk($document->disk)->delete($document->file_path);
                }

                $updateData['file_path']     = $file->store("employees/{$document->employee_id}/documents", $document->disk);
                $updateData['original_name'] = $file->getClientOriginalName();
                $updateData['mime_type']     = $file->getClientMimeType();
                $updateData['file_size']     = $file->getSize();
            }

            if (!empty($updateData)) {
                $document->update($updateData);
            }

            return $document->fresh();
        });
    }


    public function delete(EmployeeDocument $document): bool
    {
        return DB::transaction(function () use ($document) {
            if (Storage::disk($document->disk)->exists($document->file_path)) {
                Storage::disk($document->disk)->delete($document->file_path);
            }

            return $document->delete();
        });
    }
}
