<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Radar Kuliner</title>

    <!-- Memuat Bootstrap dari Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .hero-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #FF750F 0%, #F53003 100%);
            color: white;
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger" href="{{ url('/') }}">
                📍 Radar Kuliner
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/home') }}">Dashboard Saya</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link fw-bold" href="{{ route('login') }}">Masuk</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="btn btn-danger ms-2" href="{{ route('register') }}">Daftar</a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Temukan Hidden Gem Kuliner!</h1>
            <p class="lead mt-3">Bagikan dan temukan tempat kuliner terbaik yang jarang diketahui orang.</p>
            <div class="mt-4">
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-light btn-lg text-danger fw-bold shadow">Buka Radar</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg text-danger fw-bold shadow">Mulai Sekarang</a>
                @endauth
            </div>
        </div>
    </div>

    <div class="container mt-5 text-center">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="fs-1">🗺️</h3>
                        <h5 class="fw-bold mt-3">Peta Interaktif</h5>
                        <p class="text-muted">Jelajahi peta untuk melihat spot kuliner terdekat di sekitarmu.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="fs-1">⭐</h3>
                        <h5 class="fw-bold mt-3">Sistem Rating</h5>
                        <p class="text-muted">Berikan upvote pada tempat favoritmu agar semakin dikenal.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="fs-1">📸</h3>
                        <h5 class="fw-bold mt-3">Bagikan Foto</h5>
                        <p class="text-muted">Upload foto makanan dan suasana tempat untuk review.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
