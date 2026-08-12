<?php

namespace Modules\Employee\Policies;

use Modules\User\Entities\User;
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
            return $user->employee 
                && $employee->department_id 
                && $user->employee->department_id === $employee->department_id;
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

   
        if ($user->hasPermissionTo('upload.document.own', 'sanctum')) {
            return $user->id === $employee->user_id;
        }

        return false;
    }

    public function update(User $user, EmployeeDocument $document): bool
    {
        if ($user->hasPermissionTo('upload.documents.employee.all', 'sanctum')) {
            return true;
        }

      
        if ($user->hasPermissionTo('upload.document.own', 'sanctum')) {
            return $user->id === $document->employee->user_id;
        }

        return false;
    }

    public function destroy(User $user, EmployeeDocument $document): bool
    {
        return $user->hasPermissionTo('delete.documents.employee.all', 'sanctum');
    }
}