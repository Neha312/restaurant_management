<?php

namespace App\Models;


use App\Models\BaseModel;

class StockType extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
    ];
    /* Stock type has many restuarant stock Relationship */
    public function resStocks()
    {
        return $this->hasMany(RestaurantStock::class, 'stock_type_id');
    }
    /* Stock type has many restuarant stock Relationship */
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'stock_type_id');
    }
}
