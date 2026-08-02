<?php

namespace Modules\Organization\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Entities\Department;
use Modules\Organization\Enums\JobTitleGrade;

#[Fillable(['department_id','title','grade','is_active','description'])]
class JobTitle extends Model
{
    use HasFactory,SoftDeletes;
protected $casts = [
        'is_active' => 'boolean',
        'grade' => JobTitleGrade::class,
    ];

    public function department(): BelongsTo
     {
        return $this->belongsTo(Department::class, 'department_id');
      }

public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'job_title_id');
    }

    //scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    //mutator
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = ucwords(strtolower($value));
    }

}
