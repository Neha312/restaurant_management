<?php

namespace App\Models;

use App\Models\BaseModel;

class RestaurantBill extends BaseModel
{
    protected $fillable = [
        'id',
        'restaurant_id',
        'vendor_id',
        'stock_type_id',
        'total_amount',
        'tax',
        'due_date'
    ];

    /* Restaurant bill belong to restaurants Relationship */
    public function restaurants()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    /* Restaurant bill belong to stocks Relationship */
    public function stocks()
    {
        return $this->belongsTo(StockType::class, 'stock_type_id');
    }
    /* Restaurant bill belongs to vendor Relationship */
    public function vendors()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
    /* Restaurant has one trail Relationship */
    public function trail()
    {
        return $this->hasOne(RestaurantBillTrail::class, 'restaurant_bill_id');
    }
}
