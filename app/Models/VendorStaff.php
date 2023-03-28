<?php

namespace App\Models;

use App\Models\BaseModel;

class VendorStaff extends BaseModel
{
    protected $fillable = [
        'id',
        'vendor_id',
        'first_name',
        'last_name',
        'phone'
    ];
    /* Vendor staff belongs to vendor Relationship */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
