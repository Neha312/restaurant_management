<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'id',
        'order_id',
        'restaurant_id',
        'vendor_id',
        'service_type_id',
        'stock_id',
        'quantity',
        'price',
        'status'
    ];

    /* Accessors */
    public function getStatusNameAttribute()
    {
        switch ($this->status) {
            case 'P':
                return 'Pending';
            case 'A':
                return 'Accept';
            case 'R':
                return 'Reject';
            default:
                return $this->status;
        }
    }
    /* Order item belong to order Relationship */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    /* Order item belong to stock Relationship */
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }
    /* Order item  belong to restaurants Relationship */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    /* Order item belong to service Relationship */
    public function service()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
    /* Order item belongs to vendor Relationship */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
