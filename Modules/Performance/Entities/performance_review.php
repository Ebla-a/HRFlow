<?php

namespace Modules\Performance\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employee\Entities\Employee;
use Illuminate\Database\Eloquent\Model;


class performance_review extends Model
{
    use HasFactory;
    
    protected $table = 'performance_reviews';
    
    protected $fillable = [
        'employee_id',
        'performance_cycle_id',
        'reviewer_id',
        'status',
        'score',
        'comments',
        'reviewed_at'
    ];

    public function cycle()
    {
        return $this->belongsTo(performance_cycle::class,'performance_cycle_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

}

