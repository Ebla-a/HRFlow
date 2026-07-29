<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $message;
    public function __construct($resource, $message = "Success")
    {
        parent::__construct($resource);
        $this->message = $message;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return[
            'id'=>$this->id,
            'email'=>$this->email,
            'is_active'=>$this->is_active,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
    }

    public function with($request)
    {
        return [
            'status'  => true,
            'message' => $this->message,
            'meta'    => [] // You can add pagination or versions here later
        ];
    }
}
