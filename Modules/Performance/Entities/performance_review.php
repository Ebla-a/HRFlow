<?php

namespace Modules\Performance\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employee\Entities\Employee;
use Illuminate\Database\Eloquent\Model;
use Modules\Performance\Database\Factories\ReviewFactory;

#[Fillable([ 'employee_id',
        'performance_cycle_id',
        'reviewer_id',
        'status',
        'score',
        'comments',
        'reviewed_at'])]
class Performance_review extends Model
{
    use HasFactory;
    
    protected $table = 'performance_reviews';
    

    protected static function newFactory()
    {
        return ReviewFactory::new();
    }

    public function cycle()
    {
        return $this->belongsTo(performance_cycle::class,'performance_cycle_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

}

