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
        'vendor_id',
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
    /* Stock belong to stock type Relationship */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
