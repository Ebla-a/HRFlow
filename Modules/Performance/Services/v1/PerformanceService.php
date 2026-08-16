<?php

namespace Modules\Performance\Services\v1;

use Modules\Performance\Entities\PerformanceCycle;
use Modules\Performance\DTO\CreateCycleDTO;
use Modules\Performance\Exceptions\CycleEndedException;

class PerformanceService
{
    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function show(int $perPage = 15)
    {
        return PerformanceCycle::query()->paginate($perPage);
    }

    /**
     * @param CreateCycleDTO $data
     * @return PerformanceCycle
     */
    public function create(CreateCycleDTO $data)
    {
        return PerformanceCycle::create([
            'name'       => [app()->getLocale() => $data->name],
            'start_date' => $data->start_date,
            'end_date'   => $data->end_date,
            'status'     => 'Draft', 
        ]);
    }

    /**
     * @param PerformanceCycle $cycle
     * @return PerformanceCycle
     */
    public function activate(PerformanceCycle $cycle)
    {
        if ($cycle->end_date->isPast()) {
        throw new CycleEndedException
        (__('Cannot activate a performance cycle whose end date has passed.'));
    }
        $cycle->status = 'Active';
        $cycle->save();
        return $cycle;

    }

    /**
     * @param PerformanceCycle $cycle
     * @return PerformanceCycle
     */
    public function close(PerformanceCycle $cycle)
    {
        $cycle->status = 'Closed';
        $cycle->save();
        return $cycle;
    }
}