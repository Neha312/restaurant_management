<?php

namespace App\Models;

use App\Models\BaseModel;

class Order extends BaseModel
{
    protected $fillable = [
        'id',
        'restaurant_id',
        'vendor_id',
        'service_type_id',
        'name',
        'quantity'
    ];
    /* Order bill belong to restaurants Relationship */
    public function restaurants()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    /* Order bill belong to stocks Relationship */
    public function services()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
    /* Order belongs to vendor Relationship */
    public function vendors()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
