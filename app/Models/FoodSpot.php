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
        'rating',
        'visit_count',
    ];

    protected $casts = [
        'latitude'    => 'float',
        'longitude'   => 'float',
        'rating'      => 'float',
        'visit_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
