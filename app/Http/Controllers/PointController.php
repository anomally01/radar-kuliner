<?php

namespace App\Http\Controllers;

use App\Models\RedeemHistory;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PointController extends Controller
{
    // Daftar voucher yang tersedia
    private array $vouchers = [
        'data_1gb'    => ['name' => 'Paket Data 1GB',        'points' => 100, 'icon' => '📶', 'color' => 'primary'],
        'data_5gb'    => ['name' => 'Paket Data 5GB',        'points' => 400, 'icon' => '📡', 'color' => 'info'],
        'data_10gb'   => ['name' => 'Paket Data 10GB',       'points' => 700, 'icon' => '🚀', 'color' => 'primary'],
        'gplay_10k'   => ['name' => 'Google Play Rp10.000',  'points' => 150, 'icon' => '🎮', 'color' => 'success'],
        'gplay_50k'   => ['name' => 'Google Play Rp50.000',  'points' => 600, 'icon' => '🎯', 'color' => 'success'],
        'gplay_100k'  => ['name' => 'Google Play Rp100.000', 'points' => 1100, 'icon' => '💎', 'color' => 'warning'],
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userId  = Auth::id();
        $balance = UserPoint::getBalance($userId);
        $history = RedeemHistory::where('user_id', $userId)
                        ->orderBy('redeemed_at', 'desc')
                        ->get();
        $earnLogs = UserPoint::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('point.index', [
            'balance'  => $balance,
            'vouchers' => $this->vouchers,
            'history'  => $history,
            'earnLogs' => $earnLogs,
        ]);
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'voucher_key' => 'required|string',
        ]);

        $key = $request->voucher_key;

        if (!isset($this->vouchers[$key])) {
            return back()->withErrors(['voucher_key' => 'Voucher tidak valid.'])->withInput();
        }

        $voucher = $this->vouchers[$key];
        $userId  = Auth::id();
        $balance = UserPoint::getBalance($userId);

        if ($balance < $voucher['points']) {
            return back()->withErrors([
                'voucher_key' => "Poin tidak cukup. Butuh {$voucher['points']} poin, saldo kamu {$balance} poin."
            ])->withInput();
        }

        // Kurangi poin
        UserPoint::create([
            'user_id'     => $userId,
            'points'      => $voucher['points'],
            'description' => 'Redeem: ' . $voucher['name'],
            'type'        => 'redeem',
        ]);

        // Buat kode voucher unik
        $code = strtoupper('RK-' . Str::random(4) . '-' . Str::random(4));

        // Simpan riwayat
        RedeemHistory::create([
            'user_id'      => $userId,
            'voucher_type' => $voucher['name'],
            'voucher_code' => $code,
            'points_used'  => $voucher['points'],
            'status'       => 'success',
            'redeemed_at'  => now(),
        ]);

        return back()->with([
            'success'      => true,
            'voucher_name' => $voucher['name'],
            'voucher_code' => $code,
        ]);
    }
}
