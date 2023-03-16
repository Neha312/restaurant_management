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
        'created_by',
        'updated_by'
    ];
    public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function restaurants()
    {
        return $this->belongsToMany(Rastaurant::class, 'restaurant_users', 'user_id', 'restaurant_id',);
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->user() ? auth()->user()->id : User::where('role_id', 1)->first()->id;
        });
        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : User::where('role_id', 1)->first()->id;
        });
    }
}
