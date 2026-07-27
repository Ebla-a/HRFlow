<?php

namespace Modules\User\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreated
{
    use Dispatchable, SerializesModels;

    public $email;
    public $tempPassword;

    public function __construct(String $email, string $tempPassword = null)
    {
        $this->email =$email;
        $this->tempPassword = $tempPassword;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
