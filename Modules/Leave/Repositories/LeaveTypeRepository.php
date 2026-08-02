<?php

namespace Modules\Leave\Repositories;

use Modules\Leave\Entities\LeaveType;
use Modules\Leave\Repositories\Interfaces\LeaveTypeRepositoryInterface;

class LeaveTypeRepository implements LeaveTypeRepositoryInterface
{
    public function all()
    {
        return LeaveType::latest()->get();
    }

    public function findById(
        int $id
    ): ?LeaveType {

        return LeaveType::find($id);
    }

    public function create(
        array $data
    ): LeaveType {

        return LeaveType::create($data);
    }

    public function update(
        LeaveType $leaveType,
        array $data
    ): LeaveType {

        $leaveType->update($data);

        return $leaveType->refresh();
    }

    public function delete(
        LeaveType $leaveType
    ): bool {

        return (bool) $leaveType->delete();
    }
}
 