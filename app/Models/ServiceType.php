<?php

namespace App\Models;

use App\Models\BaseModel;

class ServiceType extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
    ];
    /* Service type belongs to many vendor Relationship */
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'service_type_vendors', 'service_type_id', 'vendor_id');
    }
}
