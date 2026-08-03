<?php

namespace Modules\Performance\Http\Controllers\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Performance\Http\Requests\CycleRequest;
use Modules\Performance\Services\v1\PerformanceService;
use Modules\Performance\Services\v1\ReviewService;
use Modules\Performance\Transformers\PerformanceResource;
use Modules\Performance\Entities\Performance_cycle;
use Modules\Performance\Entities\Performance_review;
use Modules\Performance\Http\Requests\ReviewRequest;
use Modules\Performance\Transformers\ReviewResource;
use Modules\Employee\Entities\Employee;
use Modules\Performance\DTO\CreateCycleDTO;
use Modules\Performance\DTO\CreateReviewDTO;

class PerformanceController extends Controller
{


    public $performanceService;
    public $reviewService;
    public function __construct(PerformanceService $performanceService,ReviewService $reviewService)
    {
        $this->performanceService = $performanceService;
        $this->reviewService = $reviewService;
    }

    /**
     * @return JsonResponse
     */
    public function ShowCycles()
    {
        $this->authorize('viewCycles');
        $data=$this->performanceService->show();
        return  $this->success(PerformanceResource::collection($data)
        ->response()->getData(true),"Performance cycles retrieved successfully.");
        
    }

    /**
     * @param CycleRequest $request
     * @return JsonResponse
     */
    public function CreateCycle(CycleRequest $request)
    {

        $this->authorize('createCycle');
        $dto=CreateCycleDTO::fromRequest($request->validated());
        $data=$this->performanceService->create($dto);
        return $this->success(new PerformanceResource($data),
        "Performance cycle created successfully.");
    }


    /**
     * @param Performance_cycle $id
     * @return JsonResponse
     */
    public function ActivateCycle(Performance_cycle $id)
    {
        $this->authorize('updateCycle');
        $data=$this->performanceService->activate($id);
        return $this->success(new PerformanceResource($data),
        "Performance cycle activated successfully.");
    }

    /**
     * @param Performance_cycle $id
     * @return JsonResponse
     */
    public function CloseCycle(Performance_cycle $id)
    {
        $this->authorize('updateCycle');
        $data=$this->performanceService->close($id);
        return $this->success(new PerformanceResource($data),
        "Performance cycle closed successfully.");
    }

    /**
     * @return JsonResponse
     */
    public function MyReviews()
    {
        $this->authorize('viewMyReviews');
        $data=$this->reviewService->myReviews();
        return $this->success(ReviewResource::collection($data),
        "My reviews retrieved successfully.");
    }

    /**
     * @param Employee $id
     * @return JsonResponse
     */
    public function EmployeeReviews(Employee $id)
    {
        $this->authorize('viewEmployeeReviews',$id);
        $data=$this->reviewService->employeeReviews($id);
        return $this->success(ReviewResource::collection($data),
        "Employee reviews retrieved successfully.");
    }





    /**
     * @return JsonResponse
     */
    public function ShowReviews()
    {
        $this->authorize('performanceReviews');
        $data=$this->reviewService->showReviews();
        return $this->success(ReviewResource::collection($data),
        "Performance reviews retrieved successfully.");
    }

    

    /**
     * @param ReviewRequest $request
     * @param Employee $id
     * @return JsonResponse
     */
    public function CreateReview(ReviewRequest $request,Employee $id)
    {
        $this->authorize('createReview',$id);
        $dto=CreateReviewDTO::fromRequest($request->validated());
        $data=$this->reviewService->createReview($dto);
        return $this->success(new ReviewResource($data),
        "Performance review created successfully.");
    }



    /**
     * @param ReviewRequest $request
     * @param Performance_review $id
     * @return JsonResponse
     */
    public function UpdateReview(ReviewRequest $request,Performance_review $id)
    {
        $this->authorize('updateReview',$id);
        $dto=CreateReviewDTO::fromRequest($request->validated());
        $data=$this->reviewService->updateReview($dto,$id);
        return $this->success(new ReviewResource($data),
        "Performance review updated successfully.");
    }

}



