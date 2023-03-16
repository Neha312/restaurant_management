<?php

namespace App\Models;

use App\Models\BaseModel;

class Restaurant extends BaseModel
{
    protected $fillable = [
        'id',
        'user_id',
        'cousine_type_id',
        'name',
        'address1',
        'address2',
        'profile_picture',
        'zip_code',
        'phone'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'restaurant_users', 'restaurant_id', 'user_id');
    }

    public function cousines()
    {
        return $this->belongsTo(CousineType::class, 'cuisine_type_id');
    }
}
