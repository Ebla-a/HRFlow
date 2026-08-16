<?php

namespace Modules\Performance\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employee\Entities\Employee;
use Illuminate\Database\Eloquent\Model;
use Modules\Performance\Database\Factories\ReviewFactory;
use CodingPartners\TranslaGenius\Traits\Translatable;


#[Fillable([ 'employee_id',
        'performance_cycle_id',
        'reviewer_id',
        'status',
        'score',
        'comments',
        'reviewed_at'])]
class PerformanceReview extends Model
{
    use HasFactory,Translatable;
    
    protected $table = 'performance_reviews';

    public array $translatable = ['comments'];

    
    
    protected $casts = [
    'comments' => 'json', 
    'reviewed_at' => 'datetime:Y-m-d H:i:s',
    use HasFactory;

    
    protected $table = 'performance_reviews';

    
    protected $casts = [
        'reviewed_at' => 'datetime', 
    ];
    

    protected static function newFactory()
    {
        return ReviewFactory::new();
    }

    public function cycle()
    {
        return $this->belongsTo(PerformanceCycle::class,'performance_cycle_id');
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