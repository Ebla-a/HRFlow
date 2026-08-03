<?php

namespace Modules\Leave\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'annual_days',
        'is_paid',
        'requires_attachment',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'requires_attachment' => 'boolean',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(
            LeaveRequest::class
        );
    }

    public function leaveBalances()
    {
        return $this->hasMany(
            LeaveBalance::class
        );
    }
}
 