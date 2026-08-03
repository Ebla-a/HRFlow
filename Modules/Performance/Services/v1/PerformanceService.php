<?php
namespace Modules\Performance\Services\v1;

use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\Performance_cycle;
use Modules\Performance\Entities\Performance_review;
use Modules\Performance\DTO\CreateCycleDTO;

class PerformanceService
{
    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function show(int $perPage = 15)
    {
        return Performance_cycle::query()
        ->paginate($perPage);
    }

    /**
     * @param CreateCycleDTO $data
     * @return Performance_cycle
     */
    public function create(CreateCycleDTO $data)
    {
        return Performance_cycle::create([
            'name'=>$data->name,
            'start_date'=>$data->start_date,
            'end_date'=>$data->end_date,
            'status'=>'Active',
        ]);
    }

    /**
     * @param Performance_cycle $id
     * @return Performance_cycle
     */
    public function activate(Performance_cycle $id)
    {
        $id->status='Active';
        $id->save();
        return $id;
    }

    /**
     * @param Performance_cycle $id
     * @return Performance_cycle
     */
    public function close(Performance_cycle $id)
    {
        $id->status='Closed';
        $id->save();
        return $id;
    }


}