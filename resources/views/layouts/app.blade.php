<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Radar Kuliner – Temukan dan bagikan hidden gem kuliner terbaik di kotamu.">

    <title>{{ config('app.name', 'Radar Kuliner') }} – Temukan Hidden Gem Kuliner</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .navbar-brand-logo { font-weight: 800; font-size: 1.2rem; letter-spacing: -0.5px; }
        .nav-link { font-weight: 500; }
        .navbar { border-bottom: 1px solid rgba(0,0,0,0.06); }
        .point-badge {
            background: linear-gradient(135deg, #FF750F, #F53003);
            color: white;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 0.8rem;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand navbar-brand-logo text-danger" href="{{ url('/') }}">
                    📍 Radar Kuliner
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'text-danger fw-bold' : '' }}" href="{{ route('home') }}">
                                <i class="bi bi-map me-1"></i>Peta
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('about') ? 'text-danger fw-bold' : '' }}" href="{{ route('about') }}">
                                <i class="bi bi-info-circle me-1"></i>Tentang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tutorial') ? 'text-danger fw-bold' : '' }}" href="{{ route('tutorial') }}">
                                <i class="bi bi-play-circle me-1"></i>Tutorial
                            </a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Masuk</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="btn btn-danger ms-2 rounded-pill px-3" href="{{ route('register') }}">Daftar</a>
                                </li>
                            @endif
                        @else
                            <!-- Poin Badge -->
                            <li class="nav-item d-flex align-items-center me-2">
                                <a href="{{ route('poin.index') }}" class="text-decoration-none">
                                    <span class="point-badge">
                                        <i class="bi bi-stars me-1"></i>Poin Saya
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('poin.index') }}">
                                        <i class="bi bi-gift me-2 text-danger"></i>Poin & Reward
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
