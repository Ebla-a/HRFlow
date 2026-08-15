<?php

namespace Modules\Performance\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'cycle_id'      => $this->performance_cycle_id,
            'employee_id'   => $this->employee_id,
            'manager_id'    => $this->reviewer_id,
            'status'        => $this->status,
            'score'         => $this->score,
            'comments'      => $this->comments,
            'reviewed_at'   => $this->reviewed_at
                ? $this->reviewed_at->toDateTimeString()
                : null,
            'status_cycle'  => $this->cycle?->status         ?? 'Unassigned',
            'cycle_name'    => $this->cycle?->name           ?? 'Unassigned',
            'employee_name' => $this->employee?->first_name  ?? 'Unassigned',
        ];
    }
}

