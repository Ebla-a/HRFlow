<?php

namespace Modules\Leave\Repositories;

use Modules\Leave\Entities\LeaveRequest;
use Modules\Leave\Repositories\Interfaces\LeaveRequestRepositoryInterface;

class LeaveRequestRepository implements LeaveRequestRepositoryInterface
{

    public function all()
    {
        return LeaveRequest::with([
            'employee',
            'leaveType'
        ])
        ->latest()
        ->get();
    }


    public function findById(int $id): ?LeaveRequest
    {
        return LeaveRequest::with([
            'employee',
            'leaveType'
        ])
        ->find($id);
    }


    public function create(array $data): LeaveRequest
    {
        return LeaveRequest::create($data);
    }


    public function update(
        LeaveRequest $leaveRequest,
        array $data
    ): LeaveRequest {

        $leaveRequest->update($data);

        return $leaveRequest->refresh();
    }


    public function delete(
       LeaveRequest $leaveRequest
    ): bool {

    return $leaveRequest->delete();}
}
 