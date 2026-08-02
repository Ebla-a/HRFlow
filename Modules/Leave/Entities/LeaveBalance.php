<?php

namespace Modules\Leave\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employee\Entities\Employee;
use Modules\Leave\Entities\LeaveType;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'year',
        'accrual_days',
        'used_days',
        'remaining_days',
    ];

    protected static function newFactory()
   {
       return \Modules\Leave\Database\Factories\LeaveBalanceFactory::new();
   }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
 