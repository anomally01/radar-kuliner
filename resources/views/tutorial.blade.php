@extends('layouts.app')

@section('content')
<style>
    body { font-family: 'Inter', sans-serif; background: #f8f9fa; }

    .tutorial-hero {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        color: white;
        padding: 60px 0 50px;
    }
    .tutorial-hero h1 { font-weight: 800; }

    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 */
        height: 0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .video-wrapper iframe {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        border: 0;
    }

    .step-card {
        border: none;
        border-radius: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .step-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .step-number {
        width: 44px; height: 44px;
        background: linear-gradient(135deg, #FF750F, #F53003);
        border-radius: 50%;
        color: white;
        font-weight: 800;
        font-size: 1.1rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .faq-item { border: none; border-radius: 12px; margin-bottom: 10px; }
    .faq-item .accordion-button {
        font-weight: 600;
        border-radius: 12px !important;
        background: white;
    }
    .faq-item .accordion-button:not(.collapsed) {
        background: #fff5f5;
        color: #F53003;
        box-shadow: none;
    }
    .faq-item .accordion-button::after {
        filter: none;
    }
    .faq-item .accordion-body { background: white; border-radius: 0 0 12px 12px; }
</style>

{{-- HERO --}}
<div class="tutorial-hero">
    <div class="container text-center">
        <div class="mb-3" style="font-size: 3rem;">🎬</div>
        <h1>Video Tutorial</h1>
        <p class="opacity-75 mt-2">Pelajari cara menggunakan Radar Kuliner dalam hitungan menit</p>
    </div>
</div>

<div class="container py-5">

    {{-- ============================================================
         MAIN VIDEO EMBED
         ============================================================ --}}
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Tutorial Lengkap Radar Kuliner</h2>
                <p class="text-muted">Cara mendaftar, menambah spot kuliner, menggunakan filter, dan menukar poin</p>
            </div>
            {{-- VIDEO EMBED: cara pakai aplikasi peta/kuliner --}}
            <div class="video-wrapper mb-3">
                <iframe
                    src="https://www.youtube.com/embed/9e2ZVWsOEuY?rel=0&modestbranding=1"
                    title="Tutorial Radar Kuliner – Cara Menggunakan Aplikasi"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                </iframe>
            </div>
            <p class="text-center text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                Video di atas merupakan contoh tutorial peta interaktif. Tonton untuk memahami fitur-fitur Radar Kuliner.
            </p>
        </div>
    </div>

    {{-- ============================================================
         STEP-BY-STEP GUIDE
         ============================================================ --}}
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h3 class="fw-bold">Panduan Langkah demi Langkah</h3>
        </div>

        @php
        $steps = [
            ['num' => 1, 'icon' => '📝', 'title' => 'Daftar Akun', 'desc' => 'Klik tombol "Daftar" di pojok kanan atas, isi nama, email, dan password. Akun kamu langsung aktif!'],
            ['num' => 2, 'icon' => '🔑', 'title' => 'Login ke Aplikasi', 'desc' => 'Masuk dengan email dan password yang sudah didaftarkan. Kamu akan diarahkan ke halaman peta interaktif.'],
            ['num' => 3, 'icon' => '🗺️', 'title' => 'Jelajahi Peta', 'desc' => 'Lihat semua spot kuliner di sekitarmu. Klik marker untuk melihat info detail nama, foto, dan kategori.'],
            ['num' => 4, 'icon' => '➕', 'title' => 'Tambah Spot Baru', 'desc' => 'Klik tombol "+" merah di pojok kanan bawah, lalu klik lokasi di peta. Isi nama, kategori, dan foto.'],
            ['num' => 5, 'icon' => '🔍', 'title' => 'Gunakan Filter', 'desc' => 'Buka panel filter untuk menyortir spot: terdekat, terjauh, terlaris, atau rating terbaik. Bisa juga filter per kategori.'],
            ['num' => 6, 'icon' => '🎁', 'title' => 'Kumpulkan & Tukar Poin', 'desc' => 'Setiap tambah spot = 50 poin! Tukar poin di menu "Poin Saya" untuk voucher Paket Data atau Google Play.'],
        ];
        @endphp

        @foreach($steps as $step)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card step-card shadow-sm p-4 h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="step-number">{{ $step['num'] }}</div>
                    <div>
                        <h6 class="fw-bold mb-1">{{ $step['icon'] }} {{ $step['title'] }}</h6>
                        <p class="text-muted small mb-0">{{ $step['desc'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ============================================================
         VIDEO TIPS (second embed)
         ============================================================ --}}
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-danger text-white py-3 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-play-circle me-2"></i>Tips: Cara Pakai Filter Peta</h5>
                </div>
                <div class="card-body p-0">
                    <div class="video-wrapper" style="border-radius: 0;">
                        <iframe
                            src="https://www.youtube.com/embed/NWiCXBd1mK4?rel=0&modestbranding=1&start=30"
                            title="Tips Menggunakan Filter Peta"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         FAQ ACCORDION
         ============================================================ --}}
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="fw-bold text-center mb-4">Pertanyaan Umum (FAQ)</h3>
            <div class="accordion" id="faqAccordion">
                @php
                $faqs = [
                    ['q' => 'Apakah aplikasi ini gratis?', 'a' => 'Ya! Radar Kuliner sepenuhnya gratis. Daftar dan mulai tambah spot kuliner tanpa biaya apapun.'],
                    ['q' => 'Bagaimana cara mendapatkan poin?', 'a' => 'Setiap kali kamu menambahkan spot kuliner baru ke peta, kamu otomatis mendapat 50 poin. Semakin banyak spot yang kamu tambah, semakin banyak poin yang terkumpul!'],
                    ['q' => 'Voucher apa saja yang bisa ditukar?', 'a' => 'Tersedia Paket Data (1GB, 5GB, 10GB) dan Google Play (Rp10.000, Rp50.000, Rp100.000). Cek halaman Poin & Reward untuk detail poin yang dibutuhkan.'],
                    ['q' => 'Bagaimana cara filter spot terdekat?', 'a' => 'Buka panel filter di kiri peta, pilih "Terdekat". Aplikasi akan menggunakan lokasi GPS kamu untuk mengurutkan spot dari yang paling dekat.'],
                    ['q' => 'Apakah foto wajib diupload?', 'a' => 'Tidak wajib, tapi sangat disarankan! Spot dengan foto lebih menarik dan membantu pengguna lain mengenali tempat tersebut.'],
                ];
                @endphp
                @foreach($faqs as $i => $faq)
                <div class="accordion-item faq-item shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">
                            {{ $faq['q'] }}
                        </button>
                    </h2>
                    <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('home') }}" class="btn btn-danger rounded-pill px-5 py-3 fw-bold">
                    <i class="bi bi-map me-2"></i>Mulai Jelajahi Peta
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
