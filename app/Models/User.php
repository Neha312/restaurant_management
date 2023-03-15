<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'joining_date',
        'ending_date',
        'address1',
        'address2',
        'zip_code',
        'phone',
        'total_leave',
        'used_leave',
        'email',
        'password',
    ];
    public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
