<?php

namespace Modules\Performance\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name'=>$this->getTranslation('name', app()->getLocale()),
            'start_date'=>$this->start_date->toDateTimeString(),
            'end_date'=>$this->end_date->toDateTimeString(),
            'status'=>__($this->status),

        ];
    }
}
