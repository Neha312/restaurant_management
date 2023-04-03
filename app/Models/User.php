<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
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
    protected $hidden = [
        'password',
        'remember_token'
    ];
    /* User belongs to role Relationship */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    /* User has many restaurant Relationship */
    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'user_id');
    }
    /* User belongs to many restaurants Relationship */
    public function restaurantUsers()
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_users', 'user_id', 'restaurant_id')->withPivot('is_owner');
    }
    /* User has many vendors Relationship */
    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'user_id');
    }
    /* boot method */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->user() ? auth()->user()->id : User::where('role_id', 1)->first()->id ?? null;
        });
        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : User::where('role_id', 1)->first()->id;
        });
    }
}
