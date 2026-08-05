<?php


namespace Modules\Leave\Entities;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Leave\Entities\LeaveType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Query\Builder;
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

   /**
    * For accepted vacations only
    * @param Builder $query
    * @return Builder
    */
   public function scopeApproved(Builder $query): Builder
{
    return $query->where('status', LeaveRequestStatusEnum::APPROVED);
}

/**
 * For unpaid leaves only
 * @param Builder $query
 * @return Builder
 */
public function scopeUnpaid(Builder $query): Builder
{
    return $query->whereHas('leaveType', fn ($q) => $q->where('is_paid', false));
}

/**
 * For vacations in a specific month and year
 * @param Builder $query
 * @param int $month
 * @param int $year
 * @return Builder
 */
public function scopeForMonth(Builder $query, int $month, int $year): Builder
{
    return $query->whereMonth('start_date', $month)
                 ->whereYear('start_date', $year);
}


 }