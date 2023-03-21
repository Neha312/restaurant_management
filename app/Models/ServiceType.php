<?php

namespace App\Models;

use App\Models\BaseModel;

class ServiceType extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
    ];
    /* Service has many vendors Relationship */
    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'service_type_id');
    }
}
