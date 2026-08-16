<?php

namespace Modules\Leave\Repositories\Interfaces;

use Modules\Leave\Entities\LeaveRequest;

interface LeaveRequestRepositoryInterface
{
    public function all();

    public function findById(int $id): ?LeaveRequest;

    public function create(array $data): LeaveRequest;

    public function update(
        LeaveRequest $leaveRequest,
        array $data
    ): LeaveRequest;

    public function delete(
        LeaveRequest $leaveRequest
    ): bool;


}
