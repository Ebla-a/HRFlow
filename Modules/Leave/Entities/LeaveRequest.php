<?php

namespace Modules\Leave\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employee\Entities\Employee;
use Modules\Leave\Entities\LeaveType;


class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'days_count',
        'status',
        'reason',
        'rejection_reason',
        'attachment_path',
        'manager_approval_status',
        'manager_approved_at',
        'hr_approval_status',
        'hr_approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'manager_approved_at' => 'datetime',
        'hr_approved_at' => 'datetime',
    ];

     protected static function newFactory()
    {
      return \Modules\Leave\Database\Factories\LeaveRequestFactory::new();
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
 