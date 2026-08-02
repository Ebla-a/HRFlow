<?php

namespace Modules\Employee\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;


/**
 * Summary of Employee
 */
class Employee extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
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
        return $this->belongsTo(Department::class,);
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


       public function managedDepartment()
    {
        return $this->hasOne(Department::class, 'manager_id');
    }
}
