<?php

namespace App\Models;


use App\Models\BaseModel;

class StockType extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
    ];
    /* Stock belongs to restuarant stock Relationship */
    public function resStocks()
    {
        return $this->hasMany(RestaurantStock::class, 'stock_type_id');
    }
}
