<?php

namespace Modules\User\Entities;

use App\Models\Employee\Entities\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Modules\User\Database\Factories\UserFactory;

#[Fillable(['name', 'email', 'password','avatar_url','is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Model
{
    use HasFactory, Notifiable,HasApiTokens,HasRoles;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
        
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    
    
}
