<?php

namespace Modules\Employee\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Modules\Department\Entities\Department;
use Modules\Department\Entities\JobTitle;

/**
 * Summary of Employee
 */
class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'job_title_id',
        'manager_id',

        'employee_number',
        'employment_type',
        'hire_date',
        'status',

        'national_id',
        'phone',
        'address',
        'birth_date',
        'gender',

        'termination_date',
        'termination_reason',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function manager()
    {
        return $this->belongsTo(self::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(self::class, 'manager_id');
    }
}