<?php

namespace App\Http\Controllers;

use App\Models\FoodSpot;
use App\Models\UserPoint;
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
     * Get all food spots as JSON (for map markers), with optional filtering.
     * Supports: category, sort_by (rating|terlaris|terdekat|terjauh), user_lat, user_lng, search
     */
    public function index(Request $request)
    {
        $query = FoodSpot::with('user:id,name');

        // --- Filter: Kategori ---
        if ($request->filled('category') && $request->category !== 'Semua') {
            $query->where('category', $request->category);
        }

        // --- Filter: Search by name ---
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $spots = $query->get();

        // --- Filter: Jarak (Haversine) ---
        $userLat = $request->filled('user_lat') ? (float) $request->user_lat : null;
        $userLng = $request->filled('user_lng') ? (float) $request->user_lng : null;

        $spots = $spots->map(function ($spot) use ($userLat, $userLng) {
            $distance = null;
            if ($userLat !== null && $userLng !== null) {
                $distance = $this->haversineDistance($userLat, $userLng, $spot->latitude, $spot->longitude);
            }
            return [
                'id'          => $spot->id,
                'name'        => $spot->name,
                'category'    => $spot->category,
                'lat'         => $spot->latitude,
                'lng'         => $spot->longitude,
                'photo_url'   => $spot->photo ? $this->getPhotoUrl($spot->photo) : null,
                'user'        => $spot->user->name ?? 'Anonim',
                'created'     => $spot->created_at->diffForHumans(),
                'rating'      => (float) $spot->rating,
                'visit_count' => (int) $spot->visit_count,
                'distance_km' => $distance,
            ];
        });

        // --- Sort ---
        $sortBy = $request->get('sort_by', 'terbaru');
        $spots = match ($sortBy) {
            'terdekat' => $spots->sortBy('distance_km'),
            'terjauh'  => $spots->sortByDesc('distance_km'),
            'terlaris' => $spots->sortByDesc('visit_count'),
            'rating'   => $spots->sortByDesc('rating'),
            default    => $spots->sortByDesc('id'), // terbaru
        };

        return response()->json($spots->values());
    }

    /**
     * Store a new food spot with optional photo upload.
     * Awards 50 points to the user on each new spot.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'category'  => 'required|string|max:100',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
            $photoPath = $request->file('photo')->store('food-spots', $disk);
        }

        $spot = FoodSpot::create([
            'user_id'   => Auth::id(),
            'name'      => $request->name,
            'category'  => $request->category,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'photo'     => $photoPath,
        ]);

        // Award 50 poin kepada user yang menambahkan spot
        UserPoint::create([
            'user_id'     => Auth::id(),
            'points'      => 50,
            'description' => 'Menambahkan spot kuliner: ' . $spot->name,
            'type'        => 'earn',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Spot kuliner berhasil ditambahkan! Kamu mendapat 50 poin! 🎉',
            'spot'    => [
                'id'          => $spot->id,
                'name'        => $spot->name,
                'category'    => $spot->category,
                'lat'         => (float) $spot->latitude,
                'lng'         => (float) $spot->longitude,
                'photo_url'   => $photoPath ? $this->getPhotoUrl($photoPath) : null,
                'user'        => Auth::user()->name,
                'created'     => $spot->created_at->diffForHumans(),
                'rating'      => 0,
                'visit_count' => 0,
                'distance_km' => null,
            ],
        ], 201);
    }

    /**
     * Haversine formula to calculate distance in km between two coordinates.
     */
    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 2);
    }

    /**
     * Get the public URL for a photo, supporting both local and S3/Supabase storage.
     */
    private function getPhotoUrl(string $path): string
    {
        if (config('filesystems.default') === 's3') {
            $publicUrl = env('SUPABASE_STORAGE_URL');
            if ($publicUrl) {
                return rtrim($publicUrl, '/') . '/' . $path;
            }
            return Storage::disk('s3')->url($path);
        }
        return asset('storage/' . $path);
    }
}
