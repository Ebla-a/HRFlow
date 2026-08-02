<?php

namespace Modules\Organization\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Modules\Organization\DTO\V1\JobTitleDTO;
use Modules\Organization\Http\Requests\V1\JobTitle\StoreJobTitleRequest;
use Modules\Organization\Http\Requests\V1\JobTitle\UpdateJobTitleRequest;
use Modules\Organization\Services\V1\JobTitleService;
use Modules\Organization\Transformers\V1\JobTitleResource;

class JobTitleController extends Controller
{

public function __construct(
        protected JobTitleService $jobTitleService
    ) {}
    public function index()
    {
        $result = $this->jobTitleService->getAllJobTitles();

         return $this->success($data = $result['data'],
            $message = 'jobtitles retrieved successfully',
            $status = 200,
            $meta = $result['meta']
        );
    }

    public function store(StoreJobTitleRequest $request)
    {
        $dto = JobTitleDTO::fromRequest($request->validated());
        $jobTitle = $this->jobTitleService->createJobTitle($dto);

        return $this->success([
            'status' => true,
            'message' => 'Job title created successfully.',
            'data' => new JobTitleResource($jobTitle),
        ], 201);
    }

    public function update(UpdateJobTitleRequest $request, int $id)
    {

            $dto = JobTitleDTO::fromRequest($request->validated());
            $jobTitle = $this->jobTitleService->updateJobTitle($id, $dto);

            return $this->success([
                'status' => true,
                'message' => 'Job title updated successfully.',
                'data' => new JobTitleResource($jobTitle),
            ]);

    }

    public function destroy(int $id)
    {

            $this->jobTitleService->deleteJobTitle($id);

            return $this->success([
                'status' => true,
                'message' => 'Job title deleted successfully.',
            ]);



    }




      public function restore(int $id)
    {
        $jobTitle = $this->jobTitleService->restoreJobTitle($id);

        return $this->success(new JobTitleResource($jobTitle),
'successfully restore the job title'        );
    }
}
