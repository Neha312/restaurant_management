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
    /* Accessors */
    public function getApprovalStatusNameAttribute()
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
    /* Vendor belongs to services Relationship */
    public function services()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}
