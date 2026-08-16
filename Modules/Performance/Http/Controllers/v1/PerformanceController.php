<?php

namespace Modules\Performance\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Employee\Entities\Employee;
use Modules\Performance\DTO\CreateCycleDTO;
use Modules\Performance\DTO\CreateReviewDTO;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\Performance\Entities\PerformanceReview;
use Modules\Performance\Http\Requests\CycleRequest;
use Modules\Performance\Http\Requests\ReviewRequest;
use Modules\Performance\Http\Requests\UpdateReviewRequest;
use Modules\Performance\Services\v1\PerformanceService;
use Modules\Performance\Services\v1\ReviewService;
use Modules\Performance\Transformers\PerformanceResource;
use Modules\Performance\Transformers\ReviewResource;

class PerformanceController extends Controller
{
    public function __construct(
        public PerformanceService $performanceService,
        public ReviewService $reviewService
    ) {}

    /**
     * @return JsonResponse
     */
    public function ShowCycles(): JsonResponse
    {
        $this->authorize('viewCycles', PerformanceCycle::class);

        $data = $this->performanceService->show();

        return $this->success(
            PerformanceResource::collection($data)
                ->response()
                ->getData(true),
            'Performance cycles retrieved successfully.'
        );
    }

    /**
     * @param CycleRequest $request
     * @return JsonResponse
     */
    public function CreateCycle(CycleRequest $request): JsonResponse
    {
        $this->authorize('createCycle', PerformanceCycle::class);

        $dto = CreateCycleDTO::fromRequest(
            $request->validated()
        );

        $data = $this->performanceService->create($dto);

        return $this->success(
            new PerformanceResource($data),
            'Performance cycle created successfully.',
            201
        );
    }

    /**
     * @param PerformanceCycle $id
     * @return JsonResponse
     */
    public function ActivateCycle(PerformanceCycle $id): JsonResponse
    {
        $this->authorize('updateCycle', $id);

        $data = $this->performanceService->activate($id);

        return $this->success(
            new PerformanceResource($data),
            'Performance cycle activated successfully.'
        );
    }

    /**
     * @param PerformanceCycle $id
     * @return JsonResponse
     */
    public function CloseCycle(PerformanceCycle $id): JsonResponse
    {
        $this->authorize('updateCycle', $id);

        $data = $this->performanceService->close($id);

        return $this->success(
            new PerformanceResource($data),
            'Performance cycle closed successfully.'
        );
    }

    /**
     * @return JsonResponse
     */
    public function MyReviews(): JsonResponse
    {
        $this->authorize(
            'viewMyReviews',
            PerformanceReview::class
        );

        $data = $this->reviewService->myReviews();

        return $this->success(
            ReviewResource::collection($data)
                ->response()
                ->getData(true),
            'My reviews retrieved successfully.'
        );
    }

    /**
     * @param Employee $id
     * @return JsonResponse
     */
    public function EmployeeReviews(Employee $id): JsonResponse
    {
        $this->authorize('viewReviews', $id);

        $data = $this->reviewService->employeeReviews($id);

        return $this->success(
            ReviewResource::collection($data)
                ->response()
                ->getData(true),
            'Employee reviews retrieved successfully.'
        );
    }

    /**
     * @return JsonResponse
     */
    public function ShowReviews(): JsonResponse
    {
        $this->authorize(
            'viewReviews',
            PerformanceReview::class
        );

        $data = $this->reviewService->showReviews();

        return $this->success(
            ReviewResource::collection($data)
                ->response()
                ->getData(true),
            'Performance reviews retrieved successfully.'
        );
    }

    /**
     * @param ReviewRequest $request
     * @return JsonResponse
     */
    public function CreateReview(ReviewRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $targetEmployee = Employee::findOrFail(
            $validated['employee_id']
        );

        $cycle = PerformanceCycle::findOrFail(
            $validated['performance_cycle_id']
        );

        $this->authorize('createReview', [
            $targetEmployee,
            $cycle,
        ]);

        $dto = CreateReviewDTO::fromRequest($validated);

        $data = $this->reviewService->createReview($dto);

        return $this->success(
            new ReviewResource($data),
            'Performance review created successfully.',
            201
        );
    }

    /**
     * @param UpdateReviewRequest $request
     * @param PerformanceReview $id
     * @return JsonResponse
     */
    public function UpdateReview(
        UpdateReviewRequest $request,
        PerformanceReview $id
    ): JsonResponse {
        $this->authorize(
            'updateReview',
            $id
        );

        $dto = CreateReviewDTO::fromRequest(
            array_merge(
                $request->validated(),
                [
                    'employee_id' => $id->employee_id,
                    'performance_cycle_id' => $id->performance_cycle_id,
                    'reviewer_id' => $id->reviewer_id,
                ]
            )
        );

        $data = $this->reviewService->updateReview(
            $dto,
            $id
        );

        return $this->success(
            new ReviewResource($data),
            'Performance review updated successfully.'
        );
    }
}