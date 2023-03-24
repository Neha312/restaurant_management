<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantPicture extends Model
{
    protected $fillable = [
        'id',
        'restaurant_id',
        'picture',
        'type'
    ];
    public $timestamps = false;
    /* Accessors */
    public function getTypeNameAttribute()
    {
        switch ($this->type) {
            case 'M':
                return 'Menu';
            case 'O':
                return 'Other';
            default:
                return $this->type;
        }
    }
    /* Restaurant picture belongs to restaurant Relationship */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
