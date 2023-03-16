<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class RestaurantUser extends Model
{
    protected $fillable = ['restaurant_id', 'users_id'];
}
