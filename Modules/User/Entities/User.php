<?php

namespace Modules\User\Entities;


use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\AI\Entities\AiConversation;
use Modules\Employee\Entities\Employee;
use Modules\User\Database\Factories\UserFactory;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['email', 'password','avatar_url','is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
     use HasRoles, HasFactory, Notifiable,HasApiTokens;


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
      /**
       * Summary of AiConversation
       * @return \Illuminate\Database\Eloquent\Relations\HasMany<AiConversation, User>
       */
      public function AiConversation()
    {
        return $this->hasMany(AiConversation::class, 'user_id');
    }

}
