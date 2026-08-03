<?php

namespace Modules\Attendance\Entities;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
    'employee_id',
    'logged_at',
    'type',
    'result',
    'message',
];

    protected $casts = [
    'logged_at' => 'datetime',
    ];
    

}