<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Notifications\Notifiable;

class Order extends BaseModel
{
    use Notifiable;
    protected $fillable = [
        'id',
        'order_number',
        'status'
    ];
    /* Accessors */
    public function getStatusNameAttribute()
    {
        switch ($this->status) {
            case 'O':
                return 'Ordered';
            case 'DP':
                return 'Dispatch';
            case 'D':
                return 'Delivered';
            default:
                return $this->status;
        }
    }

    /* Order has one bill Relationship */
    public function bill()
    {
        return $this->hasOne(RestaurantBill::class, 'order_id');
    }
    /* Order belong to restaurants Relationship */
    public function orderItem()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
