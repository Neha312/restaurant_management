<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Notifications\Notifiable;

class Order extends BaseModel
{
    use Notifiable;
    protected $fillable = [
        'id',
        'restaurant_id',
        'vendor_id',
        'service_type_id',
        'quantity',
        'status'
    ];
    /* Accessors */
    public function getStatusNameAttribute()
    {
        switch ($this->status) {
            case 'P':
                return 'Pending';
            case 'DP':
                return 'Dispatch';
            case 'D':
                return 'Delivered';
            default:
                return $this->status;
        }
    }
    /* Order belong to restaurants Relationship */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    /* Order belong to service Relationship */
    public function service()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
    /* Order belongs to vendor Relationship */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
    /* Order has one bill Relationship */
    public function bill()
    {
        return $this->hasOne(RestaurantBill::class, 'order_id');
    }
}
