<?php

namespace App\Models;

use App\Models\BaseModel;

class RestaurantBill extends BaseModel
{
    protected $fillable = [
        'id',
        'restaurant_id',
        'order_id',
        'vendor_id',
        'stock_type_id',
        'total_amount',
        'tax',
        'due_date'
    ];

    /* Restaurant bill belong to restaurants Relationship */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    /* Restaurant bill belong to stocks Relationship */
    public function stock()
    {
        return $this->belongsTo(StockType::class, 'stock_type_id');
    }
    /* Restaurant bill belongs to vendor Relationship */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
    /* Restaurant has many  trail Relationship */
    public function trails()
    {
        return $this->hasMany(RestaurantBillTrail::class, 'restaurant_bill_id');
    }
}
