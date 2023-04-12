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
    /* stock has many order Relationship */
    public function orderItem()
    {
        return $this->hasMany(OrderItem::class, 'stock_id');
    }
    /* stock has many order Relationship */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
