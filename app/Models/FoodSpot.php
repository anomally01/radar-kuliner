<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodSpot extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'latitude',
        'longitude',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
