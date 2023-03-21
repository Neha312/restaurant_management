<?php

namespace App\Models;


use App\Models\BaseModel;

class StockType extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
    ];
    /* Restaurant stock belongs to stock Relationship */
    public function resStock()
    {
        return $this->hasMany(RestaurantStock::class, 'stock_type_id');
    }
}
