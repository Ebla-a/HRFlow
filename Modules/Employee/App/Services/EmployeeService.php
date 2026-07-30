<?php

namespace Modules\Employee\App\Services;

use Modules\Employee\Entities\Employee;
use App\Models\User;
use Modules\Department\Entities\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EmployeeService
{
    /*
     * Create employee
     */
    public function store(array $data): Employee
    {
        $employee = Employee::create($data);

        return $employee->load([
            'user',
            'department',
            'jobTitle',
            'manager'
        ]);
    }

    /*
     * Get paginated employees with filters & sorting
     */
    public function getPaginatedEmployees(
        array $filters,
        int $perPage = 10
    ): LengthAwarePaginator {

        return Employee::query()
            ->with([
                'user',
                'department',
                'jobTitle',
                'manager'
            ])

            ->when(
                !empty($filters['search']),
                fn($q) => $this->applySearch(
                    $q,
                    $filters['search']
                )
            )

            ->when(
                !empty($filters['department_id']),
                fn($q) => $q->where(
                    'department_id',
                    $filters['department_id']
                )
            )

            ->when(
                !empty($filters['status']),
                fn($q) => $q->where(
                    'status',
                    $filters['status']
                )
            )

            ->when(
                !empty($filters['sort_by']),
                fn($q) => $this->applySorting(
                    $q,
                    $filters['sort_by'],
                    $filters['direction'] ?? 'asc'
                ),
                fn($q) => $q->latest()
            )

            ->paginate($perPage);
    }


    /*
     * Show employee
     */
    public function show(Employee $employee): Employee
    {
        return $employee->load([
            'user',
            'department',
            'jobTitle',
            'manager'
        ]);
    }


    /*
     * Update employee
     */
    public function update(
        Employee $employee,
        array $data
    ): Employee {

        $employee->update($data);

        return $employee->fresh([
            'user',
            'department',
            'jobTitle',
            'manager'
        ]);
    }


    /*
     * Terminate employee
     */
    public function terminate(
        Employee $employee,
        string $reason
    ): Employee {

        $employee->update([
            'status' => 'terminated',
            'termination_date' => now(),
            'termination_reason' => $reason,
        ]);

        return $employee->fresh([
            'user',
            'department',
            'jobTitle',
            'manager'
        ]);
    }


    /*
     * Search
     */
    private function applySearch(
        Builder $query,
        string $search
    ): Builder {

        return $query->where(function ($q) use ($search) {

            $q->where(
                'employee_number',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'national_id',
                'like',
                "%{$search}%"
            )

            ->orWhereHas('user', function ($u) use ($search) {

                $u->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'email',
                    'like',
                    "%{$search}%"
                );

            });

        });
    }


    /*
     * Sorting
     */
    private function applySorting(
        Builder $query,
        string $sortBy,
        string $direction
    ): Builder {

        $direction =
            strtolower($direction) === 'desc'
            ? 'desc'
            : 'asc';


        $relationSortMap = [

            'name' => User::select('name')
                ->whereColumn(
                    'users.id',
                    'employees.user_id'
                ),

            'department' => Department::select('name')
                ->whereColumn(
                    'departments.id',
                    'employees.department_id'
                ),
        ];


        if (array_key_exists($sortBy, $relationSortMap)) {

            return $query->orderBy(
                $relationSortMap[$sortBy],
                $direction
            );
        }


        if ($sortBy === 'age') {

            $ageDirection =
                $direction === 'asc'
                ? 'desc'
                : 'asc';

            return $query->orderBy(
                'birth_date',
                $ageDirection
            );
        }


        return $query->orderBy(
            $sortBy,
            $direction
        );
    }
}