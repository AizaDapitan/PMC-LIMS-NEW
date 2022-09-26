<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable  as AuditableContract;
use OwenIt\Auditing\Contracts\UserResolver;
use OwenIt\Auditing\Auditable;

class User extends Authenticatable implements AuditableContract, UserResolver
{
    use HasApiTokens, HasFactory, Notifiable;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username', 'password', 'name', 'dept', 'isActive',  'role_id', 'role',  'email', 'remember_token', 'assigned_module'
    ];
    protected $auditInclude = [
        'username', 'password', 'name', 'dept', 'isActive',  'role_id', 'role', 'email', 'assigned_module'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    // protected $attributes = ['appRoleDesc'];
    protected $appends = ['status'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getStatusAttribute()
    {
        $status = 'Active';
        if ($this->isActive != 1) {
            $status  = 'Inactive';
        }
        return $status;
    }
    public static function resolve()
    {
        return Auth::check() ? Auth::user()->getAuthIdentifier() : null;
    }
}
