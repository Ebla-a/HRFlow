<?php

namespace Modules\Employee\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\Employee\Entities\Employee;

class EmployeeService
{
    /**
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, Employee>
     */
    public function getPaginatedEmployees(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $page = request('page', 1);
        $cacheKey = "employees_page_{$page}_" . md5(json_encode($filters) . $perPage);

        return Cache::tags(['employees'])->remember($cacheKey, now()->addMinutes(30), function () use ($filters, $perPage) {
            return Employee::query()
                ->with(['user', 'department', 'jobTitle', 'manager'])
                ->when(!empty($filters['search']), fn ($q) => $this->applySearch($q, $filters['search']))
                ->when(!empty($filters['department_id']), fn ($q) => $q->where('department_id', $filters['department_id']))
                ->when(!empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
                ->when(
                    !empty($filters['sort_by']),
                    fn ($q) => $this->applySorting($q, $filters['sort_by'], $filters['direction'] ?? 'asc'),
                    fn ($q) => $q->latest()
                )
                ->paginate($perPage);
        });
    }

    /**
     * get employee details
     * @param Employee $employee
     * @return Employee
     */
    public function show(Employee $employee): Employee
    {
        $cacheKey = "employee_profile_{$employee->id}";

        return Cache::tags(['employees', "employee_{$employee->id}"])->remember($cacheKey, now()->addHours(24), function () use ($employee) {
            $employee->load(['user', 'department', 'jobTitle', 'manager', 'documents']);
            return $employee;
        });
    }

    private function applySearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('employee_number', 'like', "%{$search}%")
              ->orWhere('national_id', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhereHas('user', function ($u) use ($search) {
                  $u->where('email', 'like', "%{$search}%");
              });
        });
    }

    private function applySorting(Builder $query, string $sortBy, string $direction): Builder
    {
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'age') {
            return $query->orderBy('birth_date', $direction === 'asc' ? 'desc' : 'asc');
        }

        return $query->orderBy($sortBy, $direction);
    }
}