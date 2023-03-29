<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->user() ? auth()->user()->id : User::where('role_id', 1)->first()->id ?? null;
        });
        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : User::where('role_id', 1)->first()->id ?? null;
        });
    }
}
