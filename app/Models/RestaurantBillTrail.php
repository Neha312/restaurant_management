<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Notifications\Notifiable;

class RestaurantBillTrail extends BaseModel
{
    use Notifiable;
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
    /* Trail belongs to bill Relationship */
    public function bill()
    {
        return $this->belongsTo(RestaurantBill::class, 'restaurant_bill_id');
    }
}
