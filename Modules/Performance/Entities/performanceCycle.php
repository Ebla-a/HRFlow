<?php

namespace Modules\Performance\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Performance\Database\Factories\CycleFactory;
use Illuminate\Database\Eloquent\Model;
use CodingPartners\TranslaGenius\Traits\Translatable;


#[Fillable([  'name',
        'start_date',
        'end_date',
        'status',])]
class PerformanceCycle extends Model
{
    use HasFactory,Translatable;

    protected $table = 'performance_cycles';

    public array $translatable = ['name'];

    protected $casts = [
    'name' => 'json', 
    'start_date' => 'datetime:Y-m-d H:i:s',
    'end_date'   => 'datetime:Y-m-d H:i:s',
    ];

    protected static function newFactory()
    {
        return CycleFactory::new();
    }

    public function reviews()
    {
        return $this->hasMany(PerformanceReview::class, 'performance_cycle_id');
    }



}

