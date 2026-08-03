<?php

namespace Modules\Performance\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Performance\Database\Factories\CycleFactory;
use Illuminate\Database\Eloquent\Model;


#[Fillable([  'name',
        'start_date',
        'end_date',
        'status',])]
class Performance_cycle extends Model
{
    use HasFactory;

    protected $table = 'performance_cycles';

    protected static function newFactory()
    {
        return CycleFactory::new();
    }

  public function reviews()
    {
        return $this->hasMany(Performance_review::class, 'performance_cycle_id');
    }



}

