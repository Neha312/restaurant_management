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
        'profile_picture',
        'zip_code',
        'phone'
    ];
    /* Restaurant belong to many users Relationship  */
    public function users()
    {
        return $this->belongsToMany(User::class, 'restaurant_users', 'restaurant_id', 'user_id');
    }
    /* Restaurant belong to cousines Relationship */
    public function cousines()
    {
        return $this->belongsTo(CousineType::class, 'cousine_type_id');
    }
    /*Restaurant has many orders Relationship */
    public function orders()
    {
        return $this->hasMany(Order::class, 'restaurant_id');
    }
}
