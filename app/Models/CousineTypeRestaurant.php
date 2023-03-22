<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CousineTypeRestaurant extends Model
{
    protected $table = 'cousine_type_restaurants';
    protected $fillable = ['restaurant_id', 'cousine_type_id'];
}
