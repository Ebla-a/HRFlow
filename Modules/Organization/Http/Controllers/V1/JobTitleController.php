<?php

namespace Modules\Organization\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Modules\Organization\DTO\V1\StoreJobTitleDto;
use Modules\Organization\DTO\V1\UpdateJobTitleDto;
use Modules\Organization\Http\Requests\V1\JobTitle\StoreJobTitleRequest;
use Modules\Organization\Http\Requests\V1\JobTitle\UpdateJobTitleRequest;
use Modules\Organization\Services\V1\JobTitleService;
use Modules\Organization\Transformers\V1\JobTitleResource;

class JobTitleController extends Controller
{

public function __construct(
        protected JobTitleService $jobTitleService
    ) {}
    /**
     * Summary of index
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $result = $this->jobTitleService->getAllJobTitles();

         return $this->success($data = $result['data'],
            $message = 'jobtitles retrieved successfully',
            $status = 200,
            $meta = $result['meta']
        );
    }
    /**
     * Summary of store
     * @param StoreJobTitleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreJobTitleRequest $request)
    {
        $dto = StoreJobTitleDto::fromRequest($request->validated());
        $jobTitle = $this->jobTitleService->createJobTitle($dto);

        return $this->success([
            'status' => true,
            'data' => new JobTitleResource($jobTitle),
        ], 'Job title created successfully',201);
    }
    /**
     * Summary of update
     * @param UpdateJobTitleRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateJobTitleRequest $request, int $id)
    {

            $dto = UpdateJobTitleDto::fromRequest($request->validated());
            $jobTitle = $this->jobTitleService->updateJobTitle($id, $dto);

            return $this->success([
                'status' => true,
                'message' => 'Job title updated successfully.',
                'data' => new JobTitleResource($jobTitle),
            ]);

    }
    /**
     * Summary of destroy
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {

            $this->jobTitleService->deleteJobTitle($id);

            return $this->success([
                'status' => true,
                'message' => 'Job title deleted successfully.',
            ]);



    }



      /**
       * Summary of restore
       * @param int $id
       * @return \Illuminate\Http\JsonResponse
       */
      public function restore(int $id)
    {
        $jobTitle = $this->jobTitleService->restoreJobTitle($id);

        return $this->success(new JobTitleResource($jobTitle),
'successfully restore the job title'        );
    }
}
