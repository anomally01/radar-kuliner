<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'description',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the total balance of points for a user.
     */
    public static function getBalance(int $userId): int
    {
        $earned = self::where('user_id', $userId)->where('type', 'earn')->sum('points');
        $redeemed = self::where('user_id', $userId)->where('type', 'redeem')->sum('points');
        return max(0, $earned - $redeemed);
    }
}
