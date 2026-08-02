<?php

namespace Modules\Leave\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Modules\Leave\DTO\LeaveRequestDTO;
use Modules\Leave\Entities\LeaveRequest;
use Modules\Leave\Http\Requests\StoreLeaveRequestRequest;
use Modules\Leave\Http\Requests\RejectLeaveRequestRequest;
use Modules\Leave\Services\LeaveRequestService;
use Modules\Leave\Transformers\LeaveRequestResource;

class LeaveRequestController extends Controller
{
    public function __construct(
        protected LeaveRequestService $service
    ) {
    }

    public function index()
    {
        $this->authorize('approveHR', LeaveRequest::class);

        return LeaveRequestResource::collection(
            LeaveRequest::with([
                'employee.user',
                'leaveType'
            ])->paginate()
        );
    }

    public function store(StoreLeaveRequestRequest $request) 
    {
       $this->authorize(
         'create',
           LeaveRequest::class
       );


       $dto = LeaveRequestDTO::fromRequest(
           $request
       );


       $leaveRequest = $this->service->create(
           $dto
       );

       return new LeaveRequestResource(
           $leaveRequest->load([
             'employee.user',
             'leaveType'
          ])
       );
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $this->authorize('view', $leaveRequest);

        return new LeaveRequestResource(
            $leaveRequest->load([
                'employee.user',
                'leaveType'
            ])
        );
    }

    public function approveManager(
        LeaveRequest $leaveRequest
    )
   {
       $this->authorize(
          'approveManager',
           $leaveRequest
      );

    return new LeaveRequestResource(
        $this->service->approveByManager(
            $leaveRequest
        )
    );
   }

    public function approveHR(
       LeaveRequest $leaveRequest
    )
    {
       $this->authorize(
         'approveHR',
         $leaveRequest
      );


    return new LeaveRequestResource(
        $this->service->approveByHR(
            $leaveRequest
        )
      );
    }

    public function reject(
       RejectLeaveRequestRequest $request,
       LeaveRequest $leaveRequest
   )
   {
       $this->authorize(
          'reject',
          $leaveRequest
      );


       return new LeaveRequestResource(
         $this->service->reject(
            $leaveRequest,
            $request->rejection_reason
        )
      );
   }
}
 