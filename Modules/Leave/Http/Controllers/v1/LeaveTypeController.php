<?php

namespace Modules\Leave\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Modules\Leave\DTO\LeaveTypeDTO;
use Modules\Leave\Entities\LeaveType;
use Modules\Leave\Http\Requests\StoreLeaveTypeRequest;
use Modules\Leave\Http\Requests\UpdateLeaveTypeRequest;
use Modules\Leave\Repositories\Interfaces\LeaveTypeRepositoryInterface;
use Modules\Leave\Services\LeaveTypeService;
use Modules\Leave\Transformers\LeaveTypeResource;

class LeaveTypeController extends Controller
{
    public function __construct(
        protected LeaveTypeService $service,
        protected LeaveTypeRepositoryInterface $repository
    ) {
    }

    public function index()
    {
        return LeaveTypeResource::collection(
            $this->repository->all()
        );
    }

    public function show(
        LeaveType $leaveType
    ) {
        return new LeaveTypeResource(
            $leaveType
        );
    }

    public function store(
        StoreLeaveTypeRequest $request
    ) {

        $dto = new LeaveTypeDTO(
            name: $request->name,
            annual_days: $request->annual_days,
            is_paid: $request->is_paid,
            requires_attachment: $request->requires_attachment,
        );

        return new LeaveTypeResource(
            $this->service->create($dto)
        );
    }

    public function update(
        UpdateLeaveTypeRequest $request,
        LeaveType $leaveType
    ) {

        $dto = new LeaveTypeDTO(
            name: $request->name,
            annual_days: $request->annual_days,
            is_paid: $request->is_paid,
            requires_attachment: $request->requires_attachment,
        );

        return new LeaveTypeResource(
            $this->service->update(
                $leaveType,
                $dto
            )
        );
    }

    public function destroy(
        LeaveType $leaveType
    ) {

        $this->service->delete(
            $leaveType
        );

        return response()->json([
            'message' => 'Leave type deleted successfully.'
        ]);
    }
}
 