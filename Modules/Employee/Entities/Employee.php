<?php

namespace Modules\Employee\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\User;
use Modules\Department\Entities\Department;
use Modules\Department\Entities\JobTitle;
use Modules\Employee\App\Enums\EmployeeStatus;
use Modules\Employee\App\Enums\EmploymentType;
use Modules\Employee\App\Enums\Gender;
use Carbon\Carbon;

#[Fillable([
    'user_id',
    'department_id',
    'job_title_id',
    'manager_id',
    'employee_number',
    'first_name',
    'last_name',
    'phone',
    'national_id',
    'birth_date',
    'gender',
    'address',
    'employment_type',
    'status',
    'hire_date',
    'termination_date',
    'termination_reason',
])]
class Employee extends Model
{
    protected $casts = [
        'status' => EmployeeStatus::class,
        'employment_type' => EmploymentType::class,
        'gender' => Gender::class,
        'hire_date' => 'date',
        'birth_date' => 'date',
        'termination_date' => 'date',
    ];

    /**
     * get full name
     * @return Attribute
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}"
        );
    }
    /**
     * @return Attribute
     */
    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->birth_date ? Carbon::parse($this->birth_date)->age : null
        );
    }
    /**
     * @return Attribute
     */
    protected function yearsOfService(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->hire_date ? Carbon::parse($this->hire_date)->diffInYears(now()) : 0
        );
    }
    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', EmployeeStatus::ACTIVE->value);
    }
    /**
     * @param Builder $query
     * @param int $departmentId
     * @return Builder
     */
    public function scopeDepartment(Builder $query, int $departmentId): Builder
    {
        return $query->where('department_id', $departmentId);
    }
    /**
     * @return BelongsTo<User, Employee>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * @return BelongsTo<Department, Employee>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    /**
     * @return BelongsTo<JobTitle, Employee>
     */
    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class);
    }
    /**
     * @return BelongsTo<Employee, Employee>
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(self::class, 'manager_id');
    }
    /**
     * @return HasMany<Employee, Employee>
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(self::class, 'manager_id');
    }

    public function leaveRequests()
   {
     return $this->hasMany(
        \Modules\Leave\Entities\LeaveRequest::class
    );
   }


    public function leaveBalances()
   {
      return $this->hasMany(
        \Modules\Leave\Entities\LeaveBalance::class
    );
   }
    /**
     * @return HasMany<EmployeeDocument, Employee>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }
}