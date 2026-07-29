<?php

namespace Modules\Employee\App\Services;

use Modules\Employee\Entities\Employee;


/**
 * Summary of EmployeeService
 */
class EmployeeService
{
    public function store(array $data)
    {
        return Employee::create($data);
    }
   public function index($request)
{
    $query = Employee::with([
        'user',
        'department',
        'jobTitle',
        'manager'
    ]);

    /**
     * Filter, sort, and paginate employees.
     *
     * Filters:
     * - search (employee number, national ID, user name)
     * - department_id
     *
     * Sorting:
     * - hire_date
     * - age (using birth_date)
     * - user name
     * - department name
     */

    if ($request->filled('search')) {

        $search = $request->input('search');

        $query->where(function ($q) use ($search) {

            $q->where('employee_number', 'like', "%{$search}%")
              ->orWhere('national_id', 'like', "%{$search}%")
              ->orWhereHas('user', function ($userQuery) use ($search) {

                  $userQuery->where('name', 'like', "%{$search}%");

              });

        });
    }


    if ($request->filled('department_id')) {

        $query->where(
            'department_id',
            $request->input('department_id')
        );

    }


    $direction = in_array(
        $request->input('direction'),
        ['asc', 'desc']
    )
        ? $request->input('direction')
        : 'asc';



    switch ($request->input('sort_by')) {


        case 'hire_date':

            $query->orderBy(
                'hire_date',
                $direction
            );

            break;



        case 'age':

            $query->orderBy(
                'birth_date',
                $direction
            );

            break;



        case 'name':

            $query->leftJoin(
                'users',
                'employees.user_id',
                '=',
                'users.id'
            )
            ->select('employees.*')
            ->orderBy(
                'users.name',
                $direction
            );

            break;



        case 'department':

            $query->leftJoin(
                'departments',
                'employees.department_id',
                '=',
                'departments.id'
            )
            ->select('employees.*')
            ->orderBy(
                'departments.name',
                $direction
            );

            break;



        default:

            $query->latest();

    }


    return $query->paginate(10);
}

public function show($id)
{
    return Employee::with([
        'user',
        'department',
        'jobTitle',
        'manager'
    ])->findOrFail($id);
}
public function update($id, array $data)
{
    $employee = Employee::findOrFail($id);

    $employee->update($data);

    return $employee;
}

    public function terminate($id, $reason)
{
    $employee = Employee::findOrFail($id);

    $employee->update([
        'status' => 'terminated',
        'termination_date' => now(),
        'termination_reason' => $reason,
    ]);
    
    return $employee;
}
   
}

