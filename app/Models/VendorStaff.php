<?php

namespace App\Models;

use App\Models\BaseModel;

class VendorStaff extends BaseModel
{
    protected $fillable = [
        'id',
        'vendor_id',
        'stock_type_id',
        'first_name',
        'last_name',
        'phone'
    ];

    public function vendors()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
