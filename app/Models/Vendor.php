<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'id',
        'service_type_id',
        'legal_name',
        'address1',
        'address2',
        'zip_code',
        'phone',
        'status'
    ];
}
