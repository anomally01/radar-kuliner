@extends('layouts.app')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; background: #f8f9fa; }

    .about-hero {
        background: linear-gradient(135deg, #FF750F 0%, #F53003 100%);
        color: white;
        padding: 80px 0 60px;
        margin-bottom: 0;
    }
    .about-hero h1 { font-weight: 800; font-size: 2.8rem; }

    .team-card {
        border: none;
        border-radius: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    .team-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
    }
    .team-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #F53003;
    }

    .stat-card {
        border: none;
        border-radius: 16px;
        background: white;
        transition: transform 0.2s ease;
    }
    .stat-card:hover { transform: translateY(-4px); }
    .stat-number { font-size: 2.5rem; font-weight: 800; color: #F53003; }

    .feature-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #FF750F22, #F5300322);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .section-title { font-weight: 800; }
    .badge-tech { background: #F530031a; color: #F53003; border-radius: 20px; padding: 4px 12px; font-weight: 600; font-size: 0.8rem; }

    table.stats-table { border-radius: 12px; overflow: hidden; }
    table.stats-table thead { background: linear-gradient(135deg, #FF750F, #F53003); color: white; }
    table.stats-table tbody tr:hover { background: #fff5f5; }
</style>

{{-- ============================================================
     HERO SECTION
     ============================================================ --}}
<div class="about-hero">
    <div class="container text-center">
        <div class="mb-3" style="font-size: 3.5rem;">📍</div>
        <h1>Tentang Radar Kuliner</h1>
        <p class="lead opacity-75 mt-3 mx-auto" style="max-width: 600px;">
            Platform komunitas berbasis peta untuk menemukan, berbagi, dan menjelajahi hidden gem kuliner terbaik di kotamu.
        </p>
        <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('home') }}" class="btn btn-light text-danger fw-bold rounded-pill px-4">
                <i class="bi bi-map me-2"></i>Buka Peta
            </a>
            <a href="{{ route('tutorial') }}" class="btn btn-outline-light rounded-pill px-4">
                <i class="bi bi-play-circle me-2"></i>Video Tutorial
            </a>
        </div>
    </div>
</div>

{{-- ============================================================
     STATS TABLE
     ============================================================ --}}
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="section-title">Statistik Platform</h2>
        <p class="text-muted">Data terkini komunitas Radar Kuliner</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3 col-6">
            <div class="card stat-card shadow-sm text-center p-4">
                <div class="stat-number">{{ \App\Models\FoodSpot::count() }}</div>
                <div class="text-muted fw-500">Total Spot</div>
                <div class="mt-1" style="font-size:1.5rem">🗺️</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card shadow-sm text-center p-4">
                <div class="stat-number">{{ \App\Models\User::count() }}</div>
                <div class="text-muted">Total Member</div>
                <div class="mt-1" style="font-size:1.5rem">👥</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card shadow-sm text-center p-4">
                <div class="stat-number">{{ \App\Models\FoodSpot::distinct('category')->count('category') }}</div>
                <div class="text-muted">Kategori</div>
                <div class="mt-1" style="font-size:1.5rem">🍽️</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card shadow-sm text-center p-4">
                <div class="stat-number">5★</div>
                <div class="text-muted">Rating Maks</div>
                <div class="mt-1" style="font-size:1.5rem">⭐</div>
            </div>
        </div>
    </div>

    {{-- Tabel Kategori Spot --}}
    <div class="card border-0 shadow-sm rounded-3 mb-5">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="fw-bold mb-0"><i class="bi bi-table text-danger me-2"></i>Distribusi Kategori Spot</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table stats-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4 py-3">Kategori</th>
                            <th class="py-3 text-center">Jumlah Spot</th>
                            <th class="py-3 text-center">Persentase</th>
                            <th class="pe-4 py-3">Distribusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $categories = \App\Models\FoodSpot::selectRaw('category, count(*) as total')
                                ->groupBy('category')
                                ->orderByDesc('total')
                                ->get();
                            $totalSpots = \App\Models\FoodSpot::count();
                            $categoryEmojis = [
                                'Berat' => '🍲', 'Cemilan' => '🍟', 'Minuman' => '☕',
                                'Coffee Shop' => '☕', 'Restoran' => '🍽️', 'default' => '🍴'
                            ];
                        @endphp
                        @forelse($categories as $cat)
                        @php $pct = $totalSpots > 0 ? round(($cat->total / $totalSpots) * 100) : 0; @endphp
                        <tr>
                            <td class="ps-4 fw-bold">
                                {{ $categoryEmojis[$cat->category] ?? $categoryEmojis['default'] }}
                                {{ $cat->category }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger rounded-pill px-3">{{ $cat->total }}</span>
                            </td>
                            <td class="text-center text-muted">{{ $pct }}%</td>
                            <td class="pe-4">
                                <div class="progress" style="height: 8px; border-radius: 10px;">
                                    <div class="progress-bar bg-danger" role="progressbar"
                                         style="width: {{ $pct }}%; border-radius: 10px;"></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada spot yang ditambahkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================================================
         FITUR UTAMA
         ============================================================ --}}
    <div class="text-center mb-4">
        <h2 class="section-title">Fitur Unggulan</h2>
        <p class="text-muted">Semua yang kamu butuhkan untuk menjelajahi kuliner</p>
    </div>
    <div class="row g-4 mb-5">
        @php
            $features = [
                ['icon' => '🗺️', 'title' => 'Peta Interaktif Real-Time', 'desc' => 'Visualisasikan semua spot kuliner di peta dengan marker custom dan popup informatif.'],
                ['icon' => '📍', 'title' => 'Tambah Spot via Klik Peta', 'desc' => 'Tandai lokasi kuliner favorit langsung di peta dengan satu klik.'],
                ['icon' => '🔍', 'title' => 'Filter Cerdas', 'desc' => 'Filter berdasarkan jarak (terdekat/terjauh), kategori, terlaris, atau rating terbaik.'],
                ['icon' => '🎁', 'title' => 'Sistem Poin & Reward', 'desc' => 'Kumpulkan poin setiap tambah spot, tukar dengan voucher paket data atau Google Play.'],
                ['icon' => '📸', 'title' => 'Upload Foto', 'desc' => 'Bagikan foto suasana & makanan untuk ulasan yang lebih menarik.'],
                ['icon' => '🔎', 'title' => 'Pencarian Nama', 'desc' => 'Cari spot kuliner berdasarkan nama dengan hasil instan di peta.'],
            ];
        @endphp
        @foreach($features as $f)
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="feature-icon flex-shrink-0">{{ $f['icon'] }}</div>
                    <div>
                        <h6 class="fw-bold mb-1">{{ $f['title'] }}</h6>
                        <p class="text-muted small mb-0">{{ $f['desc'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ============================================================
         TEAM
         ============================================================ --}}
    <div class="text-center mb-4">
        <h2 class="section-title">Tim Pengembang</h2>
        <p class="text-muted">Dibuat dengan ❤️ untuk Final Project PPW</p>
    </div>
    <div class="row g-4 justify-content-center mb-5">
        @php
            $team = [
                ['name' => 'Developer', 'role' => 'Full-Stack Developer', 'emoji' => '👨‍💻', 'desc' => 'Laravel & JavaScript'],
                ['name' => 'UI Designer', 'role' => 'UI/UX Designer', 'emoji' => '🎨', 'desc' => 'Bootstrap & CSS Glassmorphism'],
                ['name' => 'Backend Dev', 'role' => 'Database Engineer', 'emoji' => '🗄️', 'desc' => 'PostgreSQL & Supabase'],
            ];
        @endphp
        @foreach($team as $member)
        <div class="col-md-4 col-sm-6">
            <div class="card team-card shadow-sm text-center p-4">
                <div class="mb-3" style="font-size: 3rem;">{{ $member['emoji'] }}</div>
                <h5 class="fw-bold mb-1">{{ $member['name'] }}</h5>
                <p class="text-danger fw-600 small mb-1">{{ $member['role'] }}</p>
                <p class="text-muted small">{{ $member['desc'] }}</p>
                <div class="mt-3">
                    <span class="badge-tech">{{ $member['desc'] }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ============================================================
         TECH STACK
         ============================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-5">
        <h5 class="fw-bold mb-3"><i class="bi bi-code-slash text-danger me-2"></i>Tech Stack</h5>
        <div class="d-flex flex-wrap gap-2">
            @foreach(['Laravel 11', 'Bootstrap 5', 'Leaflet.js', 'PostgreSQL', 'Supabase', 'Vite', 'PHP 8.2', 'JavaScript ES6+', 'Vercel'] as $tech)
            <span class="badge-tech">{{ $tech }}</span>
            @endforeach
        </div>
    </div>

    {{-- LINKS --}}
    <div class="text-center pb-3">
        <p class="text-muted mb-2">Hubungi kami:</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="https://github.com" target="_blank" class="btn btn-outline-dark rounded-pill px-3">
                <i class="bi bi-github me-1"></i>GitHub
            </a>
            <a href="mailto:radarkuliner@example.com" class="btn btn-outline-danger rounded-pill px-3">
                <i class="bi bi-envelope me-1"></i>Email
            </a>
            <a href="{{ route('tutorial') }}" class="btn btn-danger rounded-pill px-3">
                <i class="bi bi-play-circle me-1"></i>Lihat Tutorial
            </a>
        </div>
    </div>
</div>
@endsection
