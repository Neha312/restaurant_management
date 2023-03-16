<?php

namespace App\Models;

use App\Models\BaseModel;

class RestaurantBill extends BaseModel
{
    protected $fillable = [
        'id',
        'restaurant_id',
        'vendor_id',
        'stock_type_id',
        'total_amount',
        'tax',
        'status',
        'due_date'
    ];
    /* Accessors */
    public function getApprovalStatusNameAttribute()
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
    public function restaurants()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    public function stocks()
    {
        return $this->belongsTo(StockType::class, 'stock_type_id');
    }
    public function vendors()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
