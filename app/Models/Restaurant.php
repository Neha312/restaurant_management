<?php

namespace App\Models;

use App\Models\BaseModel;

class Restaurant extends BaseModel
{
    protected $fillable = [
        'id',
        'cousine_type_id',
        'name',
        'address1',
        'address2',
        'logo',
        'zip_code',
        'phone',
        'logo'
    ];
    /* Restaurant belong to many users Relationship  */
    public function users()
    {
        return $this->belongsToMany(User::class, 'restaurant_users', 'restaurant_id', 'user_id')->withPivot('is_owner');
    }
    /*Restaurant has many orders Relationship */
    public function orders()
    {
        return $this->hasMany(Order::class, 'restaurant_id');
    }
    /* Restaurant has many pictures Relationship */
    public function pictures()
    {
        return $this->hasMany(RestaurantPicture::class, 'restaurant_id');
    }
    /* Restaurant belong to many cousine type Relationship  */
    public function cousines()
    {
        return $this->belongsToMany(CousineType::class, 'cousine_type_restaurants', 'restaurant_id', 'cousine_type_id');
    }
}
