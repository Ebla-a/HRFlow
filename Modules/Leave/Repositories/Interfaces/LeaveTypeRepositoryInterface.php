<?php

namespace Modules\Leave\Repositories\Interfaces;

use Modules\Leave\Entities\LeaveType;

interface LeaveTypeRepositoryInterface
{
    public function all();

    public function findById(int $id): ?LeaveType;

    public function create(array $data): LeaveType;

    public function update(
        LeaveType $leaveType,
        array $data
    ): LeaveType;

    public function delete(
        LeaveType $leaveType
    ): bool;
}
 