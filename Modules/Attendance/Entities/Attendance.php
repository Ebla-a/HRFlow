<?php

namespace Modules\Attendance\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Employee;
use CodingPartners\TranslaGenius\Traits\Translatable;

/**
 * Summary of Attendance
 */
class Attendance extends Model
{
    use Translatable;

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

    public array $translatable = ['notes'];

        /**
         * Summary of casts
         * @var array
         */
        protected $casts = [
    'notes'=>'json',
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