<?php

namespace Modules\Organization\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Database\factories\DepartmentFactory;
use Modules\Organization\Entities\JobTitle;
use CodingPartners\TranslaGenius\Traits\Translatable;

#[Fillable(['name','code','parent_id','manager_id','is_active'])]

class Department extends Model
{
    use HasFactory,SoftDeletes,Translatable;

    protected static function newFactory()
    {
        return DepartmentFactory::new();
    }
protected $casts = [
        'is_active' => 'boolean',
        'name'=>'json'
    ];

    public array $translatable = ['name'];


    protected $appends = ['main_department_name', 'manager_name'];
    /**
     * Summary of jobTitles
     * @return HasMany<JobTitle, Department>
     */
    public function jobTitles(): HasMany
    {
        return $this->hasMany(JobTitle::class, 'department_id');
    }
    /**
     * Summary of employees
     * @return HasMany<Employee, Department>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department_id');
    }
    /**
     * Summary of children
     * @return HasMany<Department, Department>
     */
    //for hierarchical relationship
    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    //  (Self-referencing Relationship)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }
    /**
     * Summary of childrenRecursive
     * @return HasMany<Department, Department>
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

//manager of department
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

//scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    //accessor
    public function getMainDepartmentNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->name . ' is a Main Department ';
        }
        return $this->name . ' is a Main Department';
    }

    public function getManagerNameAttribute()
    {
        return $this->manager ? $this->manager->first_name  . ' ' . $this->manager->last_name : 'No Manager Assigned';
    }




}



