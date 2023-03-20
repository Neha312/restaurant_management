<?php

namespace App\Models;

use App\Models\BaseModel;

class RestaurantBillTrail extends BaseModel
{
    protected $fillable = [
        'id',
        'restaurant_bill_id',
        'status',
    ];
    /* Accessors */
    public function getStatusNameAttribute()
    {
        switch ($this->status) {
            case 'PN':
                return 'Pending';
            case 'P':
                return 'Paid';
            default:
                return $this->status;
        }
    }
}
