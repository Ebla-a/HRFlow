<?php

namespace Modules\Attendance\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Employee;
/**
 * Summary of Attendance
 */
class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'check_in',
        'check_out',
        'worked_minutes',
        'late_minutes',
        'overtime_minutes',
        'status',
        'notes'
    ];
    

        /**
         * Summary of casts
         * @var array
         */
        protected $casts = [
    'attendance_date' => 'date',
    'check_in' => 'datetime',
    'check_out' => 'datetime',
    ];



    /*
     * Relations
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}