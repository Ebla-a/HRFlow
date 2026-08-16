<?php

namespace Modules\Leave\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Leave\Entities\LeaveType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Modules\Leave\Enums\LeaveBalanceStatusEnum;
use Modules\Employee\Entities\Employee;

  #[Fillable([
    'employee_id',
    'leave_type_id',
    'total_days',
    'used_days',
    'remaining_days',
    'status',
    'year',
  ])]

  class LeaveBalance extends Model
  {
    use SoftDeletes;

    protected $casts = [
        'year' => 'integer',
        'status' => LeaveBalanceStatusEnum::class,
    ];

    public function employee()
    {
        return $this->belongsTo(
            Employee::class
        );
    }

    public function leaveType()
    {
        return $this->belongsTo(
            LeaveType::class,'leave_type_id'
        );
    }
  }
