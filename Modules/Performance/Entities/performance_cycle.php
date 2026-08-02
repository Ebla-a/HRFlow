<?php

namespace Modules\Performance\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class performance_cycle extends Model
{
    use HasFactory;

    protected $table = 'performance_cycles';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
    ];

    public function reviews()
    {
        return $this->hasMany(Performance_Review::class, 'performance_cycle_id');
    }



}

