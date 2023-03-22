<?php

namespace App\Models;

use App\Models\BaseModel;

class RestaurantStock extends BaseModel
{
    protected $fillable = [
        'id',
        'restaurant_id',
        'stock_type_id',
        'name',
        'available_quantity',
        'minimum_quantity'
    ];
    /* Restaurant stock belongs to restaurant Relationship */
    public function restaurants()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    /* Restaurant stock belongs to stock type Relationship */
    public function stocks()
    {
        return $this->belongsTo(StockType::class, 'stock_type_id');
    }
}
