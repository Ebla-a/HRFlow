<?php

namespace Modules\Leave\Services;

use Modules\Leave\DTO\LeaveTypeDTO;
use Modules\Leave\Entities\LeaveType;
use Modules\Leave\Repositories\Interfaces\LeaveTypeRepositoryInterface;

class LeaveTypeService
{
    public function __construct(
        protected LeaveTypeRepositoryInterface $repository
    ) {
    }

    public function create(
        LeaveTypeDTO $dto
    ): LeaveType {

        return $this->repository->create(
            $dto->toArray()
        );
    }

    public function update(
        LeaveType $leaveType,
        LeaveTypeDTO $dto
    ): LeaveType {

        return $this->repository->update(
            $leaveType,
            $dto->toArray()
        );
    }

    public function delete(
        LeaveType $leaveType
    ): bool {

        return $this->repository->delete(
            $leaveType
        );
    }
}
 