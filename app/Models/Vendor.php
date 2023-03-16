<?php

namespace App\Models;

use App\Models\BaseModel;

class Vendor extends BaseModel
{
    protected $fillable = [
        'id',
        'service_type_id',
        'legal_name',
        'address1',
        'address2',
        'zip_code',
        'phone',
        'status'
    ];
    public function services()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}
