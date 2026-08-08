<?php

namespace Modules\User\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Employee\Entities\Employee;
use Spatie\Permission\Traits\HasRoles;
use Modules\User\Database\Factories\UserFactory;

#[Fillable(['email', 'password','avatar_url','is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
     use HasRoles, HasFactory, Notifiable,HasApiTokens;

     protected $guard_name = 'sanctum';


    /**
     * @return array{email_verified_at: string, is_active: string, password: string}
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
    
        
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    
    
}
