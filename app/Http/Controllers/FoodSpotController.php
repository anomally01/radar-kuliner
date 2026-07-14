<?php

namespace App\Http\Controllers;

use App\Models\FoodSpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FoodSpotController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all food spots as JSON (for map markers).
     */
    public function index()
    {
        $spots = FoodSpot::with('user:id,name')
            ->latest()
            ->get()
            ->map(function ($spot) {
                return [
                    'id'        => $spot->id,
                    'name'      => $spot->name,
                    'category'  => $spot->category,
                    'lat'       => (float) $spot->latitude,
                    'lng'       => (float) $spot->longitude,
                    'photo_url' => $spot->photo ? asset('storage/' . $spot->photo) : null,
                    'user'      => $spot->user->name ?? 'Anonim',
                    'created'   => $spot->created_at->diffForHumans(),
                ];
            });

        return response()->json($spots);
    }

    /**
     * Store a new food spot with optional photo upload.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'category'  => 'required|string|max:100',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5MB
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('food-spots', 'public');
        }

        $spot = FoodSpot::create([
            'user_id'   => Auth::id(),
            'name'      => $request->name,
            'category'  => $request->category,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'photo'     => $photoPath,
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Spot kuliner berhasil ditambahkan!',
            'spot'      => [
                'id'        => $spot->id,
                'name'      => $spot->name,
                'category'  => $spot->category,
                'lat'       => (float) $spot->latitude,
                'lng'       => (float) $spot->longitude,
                'photo_url' => $photoPath ? asset('storage/' . $photoPath) : null,
                'user'      => Auth::user()->name,
                'created'   => $spot->created_at->diffForHumans(),
            ],
        ], 201);
    }
}
