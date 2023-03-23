<?php

namespace App\Models;


use App\Models\BaseModel;

class CousineType extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
    ];
    // /* Cousine belong to many restaurants Relationship  */
    // public function restaurants()
    // {
    //     return $this->belongsToMany(Restaurant::class, 'cousine_type_restaurants', 'cousine_type_id', 'restaurant_id');
    // }
}
