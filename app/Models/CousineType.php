<?php

namespace App\Models;


use App\Models\BaseModel;

class CousineType extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
    ];
    /* Cousine has many restaurants Relationship */
    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'cousine_type_id');
    }
}
