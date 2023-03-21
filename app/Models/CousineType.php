<?php

namespace App\Models;


use App\Models\BaseModel;

class CousineType extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
    ];
    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'cousine_type_id');
    }
}
