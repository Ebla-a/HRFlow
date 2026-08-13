<?php

namespace Modules\Employee\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Modules\User\Entities\User;
use Modules\Employee\Entities\Employee;





#[Fillable([
    'employee_id',
    'uploaded_by',
    'title',
    'type',
    'disk',
    'file_path',
    'original_name',
    'mime_type',
    'file_size',
])]
class EmployeeDocument extends Model
{
 
    /**
     * @return BelongsTo<Employee, EmployeeDocument>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    /**
     * @return BelongsTo<User, EmployeeDocument>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}