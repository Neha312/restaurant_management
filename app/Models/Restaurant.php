<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'id',
        'owner_id',
        'name',
        'address1',
        'address2',
        'profile_picture',
        'zip_code',
        'phone'
    ];
}
