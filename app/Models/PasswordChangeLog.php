<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordChangeLog extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'device',
    ];
}