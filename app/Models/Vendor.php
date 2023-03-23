<?php

namespace App\Models;

use App\Models\BaseModel;

class Vendor extends BaseModel
{
    protected $fillable = [
        'id',
        'user_id',
        'status'
    ];
    /* Accessors */
    public function getStatusNameAttribute()
    {
        switch ($this->status) {
            case 'A':
                return 'Active';
            case 'In':
                return 'Inactive';
            default:
                return $this->status;
        }
    }
    // /* Vendor belongs to many services Relationship */
    public function services()
    {
        return $this->belongsToMany(ServiceType::class, 'service_type_vendors', 'vendor_id', 'service_type_id');
    }
    /* Vendor has many staff Relationship */
    public function staffs()
    {
        return $this->hasMany(VendorStaff::class, 'vendor_id');
    }
}
