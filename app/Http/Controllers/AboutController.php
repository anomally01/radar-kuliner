<?php

namespace App\Http\Controllers;

use App\Models\FoodSpot;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AboutController extends Controller
{
    public function index()
    {
        // Wrap semua query DB dalam try/catch agar aman di Vercel (tanpa DB)
        try {
            $totalSpots    = FoodSpot::count();
            $totalMembers  = User::count();
            $totalCategories = FoodSpot::distinct('category')->count('category');
            $categories    = FoodSpot::selectRaw('category, count(*) as total')
                                ->groupBy('category')
                                ->orderByDesc('total')
                                ->get();
        } catch (\Exception $e) {
            $totalSpots     = 0;
            $totalMembers   = 0;
            $totalCategories = 3;
            $categories     = collect();
        }

        return view('about', compact(
            'totalSpots',
            'totalMembers',
            'totalCategories',
            'categories'
        ));
    }
}
