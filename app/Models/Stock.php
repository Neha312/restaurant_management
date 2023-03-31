<?php

namespace App\Models;

use App\Models\BaseModel;

class Stock extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
        'quantity',
        'price',
        'tax',
        'stock_type_id',
        'is_available',
        'manufacture_date',
        'expired_date'
    ];
    /* Stock belong to stock type Relationship */
    public function stockType()
    {
        return $this->belongsTo(StockType::class, 'stock_type_id');
    }
    /* stock belong to order Relationship */
    public function orders()
    {
        return $this->hasMany(Order::class, 'stock_id');
    }
}
