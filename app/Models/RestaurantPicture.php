<?php

namespace App\Models;

use App\Models\BaseModel;

class RestaurantPicture extends BaseModel
{
    protected $fillable = [
        'id',
        'restaurant_id',
        'created_by',
        'updated_by'
    ];
    /* Restaurant picture belongs to restaurant Relationship */
    public function restaurants()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    /* Restaurant picture has many images Relationship */
    public function images()
    {
        return $this->hasMany(Picture::class, 'restaurant_picture_id');
    }
}
