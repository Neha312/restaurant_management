<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $fillable = [
        'id',
        'restaurant_picture_id',
        'picture',
        'type'
    ];
}
