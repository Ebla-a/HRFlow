<?php
namespace Modules\Employee\Policies;

use App\Models\User;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\EmployeeDocument;

class EmployeeDocumentPolicy
{
    public function viewAny(User $user, Employee $employee): bool
    {
        if ($user->hasPermissionTo('view.documents.employee.all', 'sanctum')) {
            return true;
        }

        if ($user->hasPermissionTo('view.employee.documents.own', 'sanctum')) {
            return $user->employee && $user->employee->department_id === $employee->department_id;
        }

        if ($user->hasPermissionTo('view.document.own', 'sanctum')) {
            return $user->id === $employee->user_id;
        }

        return false;
    }

    public function store(User $user, Employee $employee): bool
    {
        if ($user->hasPermissionTo('upload.documents.employee.all', 'sanctum')) {
            return true;
        }

        if ($user->id === $employee->user_id && $user->hasPermissionTo('upload.document.own', 'sanctum')) {
            return true;
        }

        return false;
    }

    public function update(User $user, EmployeeDocument $document): bool
    {
        return $user->hasPermissionTo('upload.documents.employee.all', 'sanctum');
    }

    public function destroy(User $user, EmployeeDocument $document): bool
    {
        return $user->hasPermissionTo('delete.documents.employee.all', 'sanctum');
    }
}