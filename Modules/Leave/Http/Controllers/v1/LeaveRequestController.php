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
    $this->authorize('viewAny', LeaveRequest::class);

    $user = auth('sanctum')->user();

    $query = LeaveRequest::with([
        'employee.user',
        'leaveType'
    ]);

  
    if (!$user->hasPermissionTo('leave.requests.view.all')) {
        $query->where('employee_id', $user->employee?->id);
    }

    return LeaveRequestResource::collection($query->paginate());
}

    public function store(StoreLeaveRequestRequest $request)
    {
       $this->authorize(
         'create',
           LeaveRequest::class
       );

       if (!auth('sanctum')->user()->employee) {

            return $this->error("User is not associated with an employee record",422);

       }

       $request->merge([
           'employee_id' => auth('sanctum')->user()->employee->id
       ]);

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
