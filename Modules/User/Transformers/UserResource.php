<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'is_active'  => $this->is_active,
            'avatar_url' => $this->avatar_url ? Storage::url($this->avatar_url) : null, 
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}