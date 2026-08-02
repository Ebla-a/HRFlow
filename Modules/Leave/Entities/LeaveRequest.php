<?php


namespace Modules\Leave\Entities;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Leave\Entities\LeaveType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Modules\Leave\Enums\LeaveRequestStatusEnum;
use Modules\Employee\Entities\Employee;
 
  #[Fillable([
    'employee_id',
    'leave_type_id',
    'start_date',
    'end_date',
    'days_count',
    'reason',
    'attachment_path',
    'status',

    'manager_approval_status',
    'manager_approved_at',
    'approved_by',

    'hr_approval_status',
    'hr_approved_at',
    'hr_approved_by',

    'rejection_reason',
  ])]

  class LeaveRequest extends Model
  {
    use SoftDeletes;

    protected $casts = [

        'status' => LeaveRequestStatusEnum::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'manager_approved_at' => 'datetime',
        'hr_approved_at' => 'datetime',
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
            LeaveType::class
        );
    }

    /**
     * Manager who approved request
     */

    public function approvedBy()
   {
    return $this->belongsTo(
        User::class,
        'approved_by'
      );
   }

   /**
     * HR who approved request
     */

   public function hrApprovedBy()
   {
    return $this->belongsTo(
        User::class,
        'hr_approved_by'
    );
   }
 }