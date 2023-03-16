<?php

namespace App\Models;

use App\Models\BaseModel;

class RestaurantPicture extends BaseModel
{
    protected $fillable = [
        'id',
        'restaurant_id',
        'picture',
        'type'
    ];
    public function restaurants()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
