@extends('layouts.app')

@section('content')
<!-- Leaflet CSS & Bootstrap Icons -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    main.py-4 { padding-top: 0 !important; padding-bottom: 0 !important; }
    #map-container { position: relative; height: calc(100vh - 56px); width: 100%; overflow: hidden; }
    #map { position: absolute; top: 0; bottom: 0; width: 100%; z-index: 1; }
    .ui-layer { position: absolute; z-index: 400; pointer-events: none; width: 100%; height: 100%; top: 0; left: 0; }
    .interactive-element { pointer-events: auto; }

    .glass-search {
        background: rgba(255,255,255,0.88);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .glass-search:focus-within { background: rgba(255,255,255,0.97); transform: translateY(-2px); box-shadow: 0 12px 40px rgba(0,0,0,0.15); }

    .fab-add {
        position: absolute; bottom: 40px; right: 30px;
        border-radius: 50%; width: 65px; height: 65px;
        box-shadow: 0 10px 25px rgba(220,53,69,0.4);
        transition: all 0.3s cubic-bezier(0.175,0.885,0.32,1.275);
        z-index: 400;
    }
    .fab-add:hover { transform: scale(1.1) rotate(90deg); box-shadow: 0 15px 35px rgba(220,53,69,0.5); }

    .leaflet-popup-content-wrapper { border-radius: 16px; padding: 0; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.5); }
    .leaflet-popup-content { margin: 0; width: 250px !important; }
    .leaflet-popup-tip-container { display: none; }
    .popup-header-img { width: 100%; height: 120px; object-fit: cover; }

    .custom-marker {
        display: flex; align-items: center; justify-content: center;
        background: #dc3545; color: white; border-radius: 50%;
        border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        font-size: 20px; transition: transform 0.2s; width: 40px; height: 40px;
    }
    .custom-marker:hover { transform: scale(1.2) translateY(-5px); z-index: 1000 !important; }

    #add-mode-alert {
        position: absolute; top: -100px; left: 50%; transform: translateX(-50%);
        transition: top 0.4s cubic-bezier(0.175,0.885,0.32,1.275);
        z-index: 400; border-radius: 30px;
    }
    #add-mode-alert.show { top: 20px; }

    /* ===== FILTER PANEL ===== */
    #filter-panel {
        position: absolute;
        top: 80px;
        left: -320px;
        width: 300px;
        z-index: 450;
        transition: left 0.35s cubic-bezier(0.4,0,0.2,1);
        background: rgba(255,255,255,0.96);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-radius: 0 20px 20px 0;
        box-shadow: 6px 0 30px rgba(0,0,0,0.15);
        border: 1px solid rgba(255,255,255,0.4);
        pointer-events: auto;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }
    #filter-panel.open { left: 0; }
    #filter-toggle-btn {
        position: absolute;
        top: 80px;
        left: 15px;
        z-index: 450;
        pointer-events: auto;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: left 0.35s cubic-bezier(0.4,0,0.2,1);
    }
    #filter-toggle-btn.panel-open { left: 310px; }
    .filter-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #888; }
    .sort-btn { border-radius: 20px; font-size: 0.82rem; font-weight: 600; border: 2px solid #dee2e6; background: white; transition: all 0.2s; padding: 5px 14px; }
    .sort-btn:hover, .sort-btn.active { border-color: #dc3545; background: #dc3545; color: white; }
    .category-chip { border-radius: 20px; font-size: 0.82rem; font-weight: 600; border: 2px solid #dee2e6; background: white; transition: all 0.2s; cursor: pointer; padding: 5px 14px; }
    .category-chip:hover, .category-chip.active { border-color: #dc3545; background: #dc3545; color: white; }
    #spot-count-badge { position: absolute; bottom: 100px; left: 15px; z-index: 450; pointer-events: auto; border-radius: 30px; font-size: 0.8rem; font-weight: 700; }
</style>

<div id="map-container">
    <div id="map"></div>

    <div class="ui-layer d-flex flex-column p-3">
        <!-- Top Search Bar -->
        <div class="interactive-element mt-2 container max-w-md mx-auto" style="max-width: 500px; margin-top: 70px !important;">
            <div class="input-group glass-search rounded-pill overflow-hidden p-1">
                <span class="input-group-text bg-transparent border-0 ps-4"><i class="bi bi-search text-danger fs-5"></i></span>
                <input type="text" id="search-input" class="form-control bg-transparent border-0 py-2 shadow-none fw-bold" placeholder="Cari hidden gem terdekat..." autocomplete="off">
                <button id="search-btn" class="btn btn-danger rounded-pill px-4 m-1 fw-bold">Cari</button>
            </div>
        </div>
        
        <!-- Add Mode Notification -->
        <div id="add-mode-alert" class="interactive-element bg-dark text-white px-4 py-3 shadow-lg d-flex align-items-center">
            <i class="bi bi-geo-alt-fill text-danger me-2 fs-5"></i>
            <span class="fw-bold ms-2">Klik pada peta untuk menentukan lokasi</span>
            <button class="btn btn-sm btn-outline-light ms-4 rounded-pill px-3" id="cancel-add-mode">Batal</button>
        </div>
    </div>

    <!-- Floating Action Button -->
    <button id="fab-add-spot" class="btn btn-danger fab-add interactive-element d-flex align-items-center justify-content-center" title="Tambah Spot Kuliner">
        <i class="bi bi-plus-lg fs-2"></i>
    </button>
</div>

<!-- Filter Toggle Button -->
<button id="filter-toggle-btn" class="btn btn-white border shadow-sm interactive-element" onclick="toggleFilterPanel()">
    <i class="bi bi-sliders me-1 text-danger"></i> Filter
</button>

<!-- Spot Count Badge -->
<div id="spot-count-badge" class="badge bg-dark d-none">0 spot ditemukan</div>

<!-- Filter Panel -->
<div id="filter-panel">
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-funnel-fill text-danger me-2"></i>Filter & Urutkan</h6>
            <button class="btn btn-sm btn-light rounded-pill" onclick="resetFilters()">Reset</button>
        </div>

        <!-- Sort By -->
        <div class="mb-4">
            <div class="filter-label mb-2">Urutkan berdasarkan</div>
            <div class="d-flex flex-wrap gap-2">
                <button class="sort-btn active" data-sort="terbaru" onclick="setSortBy(this,'terbaru')">🕐 Terbaru</button>
                <button class="sort-btn" data-sort="terdekat" onclick="setSortBy(this,'terdekat')">📍 Terdekat</button>
                <button class="sort-btn" data-sort="terjauh" onclick="setSortBy(this,'terjauh')">🌍 Terjauh</button>
                <button class="sort-btn" data-sort="terlaris" onclick="setSortBy(this,'terlaris')">🔥 Terlaris</button>
                <button class="sort-btn" data-sort="rating" onclick="setSortBy(this,'rating')">⭐ Rating</button>
            </div>
        </div>

        <!-- Kategori -->
        <div class="mb-4">
            <div class="filter-label mb-2">Kategori</div>
            <div class="d-flex flex-wrap gap-2">
                <span class="category-chip active" data-cat="Semua" onclick="setCategoryFilter(this,'Semua')">🍽️ Semua</span>
                <span class="category-chip" data-cat="Berat" onclick="setCategoryFilter(this,'Berat')">🍲 Berat</span>
                <span class="category-chip" data-cat="Cemilan" onclick="setCategoryFilter(this,'Cemilan')">🍟 Cemilan</span>
                <span class="category-chip" data-cat="Minuman" onclick="setCategoryFilter(this,'Minuman')">☕ Minuman</span>
            </div>
        </div>

        <!-- Rating Min -->
        <div class="mb-3">
            <div class="filter-label mb-2">Rating Minimum: <span id="rating-val" class="text-danger">Semua</span></div>
            <input type="range" class="form-range" id="rating-filter" min="0" max="5" step="0.5" value="0"
                   oninput="setRatingFilter(this.value)">
            <div class="d-flex justify-content-between" style="font-size:0.75rem; color:#999;">
                <span>Semua</span><span>⭐ 5.0</span>
            </div>
        </div>

        <button class="btn btn-danger w-100 rounded-pill fw-bold mt-1" onclick="applyFilters()">
            <i class="bi bi-search me-1"></i>Terapkan Filter
        </button>
    </div>
</div>

<!-- Offcanvas Form -->
<div class="offcanvas offcanvas-bottom h-auto rounded-top-4 shadow-lg" tabindex="-1" id="addSpotOffcanvas" style="max-height: 85vh;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title fw-bold text-danger"><i class="bi bi-pin-map-fill me-2"></i> Tambah Spot Kuliner</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="add-spot-form" enctype="multipart/form-data">
            <input type="hidden" id="spot-lat" name="latitude">
            <input type="hidden" id="spot-lng" name="longitude">
            <input type="file" id="photo-input" name="photo" accept="image/*" class="d-none">
            <div class="mb-4 text-center mt-2">
                <div class="d-inline-flex justify-content-center align-items-center bg-light rounded-circle border border-2 border-dashed border-danger text-danger overflow-hidden" style="cursor:pointer; width: 80px; height: 80px;" id="upload-photo-btn">
                    <i class="bi bi-camera fs-2" id="camera-icon"></i>
                    <img id="photo-preview" src="" alt="Preview" class="d-none" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <div class="small text-muted fw-bold mt-2" id="photo-label">Tambahkan Foto</div>
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-bold">Nama Tempat</label>
                <input type="text" id="spot-name" name="name" class="form-control form-control-lg bg-light border-0 shadow-none" placeholder="Contoh: Nasi Goreng Gila Mas Bro" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-sm fw-bold">Kategori</label>
                <div class="d-flex gap-2 flex-wrap">
                    <input type="radio" class="btn-check" name="kategori" id="kat1" autocomplete="off" checked>
                    <label class="btn btn-outline-danger rounded-pill btn-sm px-3" for="kat1">🍲 Berat</label>
                    
                    <input type="radio" class="btn-check" name="kategori" id="kat2" autocomplete="off">
                    <label class="btn btn-outline-danger rounded-pill btn-sm px-3" for="kat2">🍟 Cemilan</label>

                    <input type="radio" class="btn-check" name="kategori" id="kat3" autocomplete="off">
                    <label class="btn btn-outline-danger rounded-pill btn-sm px-3" for="kat3">☕ Minuman</label>
                </div>
            </div>
            <button type="submit" class="btn btn-danger w-100 rounded-pill py-3 fw-bold fs-5 shadow-sm">
                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Simpan Lokasi
            </button>
        </form>
    </div>
</div>

<!-- ============================================================
     JS BLOCK #1: Filter, Sort & Search Logic
     ============================================================ -->
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // ============================================================
        // JS BLOCK #1 — Filter State & Controls
        // ============================================================
        let currentFilters = { sort_by: 'terbaru', category: 'Semua', min_rating: 0, search: '' };
        let userPosition = null; // { lat, lng } when GPS available
        let allMarkers = []; // track all markers for clearing
        let filterPanelOpen = false;

        window.toggleFilterPanel = function() {
            filterPanelOpen = !filterPanelOpen;
            document.getElementById('filter-panel').classList.toggle('open', filterPanelOpen);
            document.getElementById('filter-toggle-btn').classList.toggle('panel-open', filterPanelOpen);
            document.getElementById('filter-panel').classList.remove('hidden');
        };

        window.setSortBy = function(btn, val) {
            document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilters.sort_by = val;
        };

        window.setCategoryFilter = function(chip, val) {
            document.querySelectorAll('.category-chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            currentFilters.category = val;
        };

        window.setRatingFilter = function(val) {
            currentFilters.min_rating = parseFloat(val);
            document.getElementById('rating-val').textContent = val === '0' ? 'Semua' : '⭐ ' + val + '+';
        };

        window.resetFilters = function() {
            currentFilters = { sort_by: 'terbaru', category: 'Semua', min_rating: 0, search: '' };
            document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
            document.querySelector('[data-sort="terbaru"]').classList.add('active');
            document.querySelectorAll('.category-chip').forEach(c => c.classList.remove('active'));
            document.querySelector('[data-cat="Semua"]').classList.add('active');
            document.getElementById('rating-filter').value = 0;
            document.getElementById('rating-val').textContent = 'Semua';
            document.getElementById('search-input').value = '';
            applyFilters();
        };

        window.applyFilters = function() {
            loadSpots();
            if (filterPanelOpen) toggleFilterPanel();
        };

        // ============================================================
        // JS BLOCK #3 — Search with Debounce
        // ============================================================
        let searchDebounceTimer = null;
        document.getElementById('search-input').addEventListener('input', function() {
            clearTimeout(searchDebounceTimer);
            currentFilters.search = this.value.trim();
            searchDebounceTimer = setTimeout(() => loadSpots(), 400);
        });
        document.getElementById('search-btn').addEventListener('click', function() {
            currentFilters.search = document.getElementById('search-input').value.trim();
            loadSpots();
        });

        // 1. Initialize Map
        const map = L.map('map', { zoomControl: false }).setView([-6.175110, 106.827153], 13);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors', maxZoom: 19
        }).addTo(map);
        L.control.zoom({ position: 'bottomleft' }).addTo(map);

        // Geolocation
        map.locate({ setView: true, maxZoom: 15, enableHighAccuracy: true });
        map.on('locationfound', function(e) {
            userPosition = { lat: e.latlng.lat, lng: e.latlng.lng };
            L.circle(e.latlng, e.accuracy / 2, { color: '#0d6efd', fillColor: '#0d6efd', fillOpacity: 0.1, weight: 1 }).addTo(map);
            L.circleMarker(e.latlng, { color: 'white', fillColor: '#0d6efd', fillOpacity: 1, weight: 3, radius: 8 })
                .addTo(map).bindPopup("<div class='fw-bold text-primary p-1'>📍 Lokasi Anda Saat Ini</div>").openPopup();
        });

        // 2. Custom Icon Generator
        const categoryIcons = {
            'Berat': 'bi-shop',
            'Cemilan': 'bi-basket2-fill',
            'Minuman': 'bi-cup-hot-fill',
        };

        const createCustomIcon = (iconClass) => {
            return L.divIcon({
                className: 'custom-icon-wrapper',
                html: `<div class="custom-marker"><i class="bi ${iconClass}"></i></div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                popupAnchor: [0, -45]
            });
        };

        // Helper to create popup HTML for a spot
        function createSpotPopup(spot) {
            const imgHTML = spot.photo_url 
                ? `<img src="${spot.photo_url}" class="popup-header-img" alt="${spot.name}">` 
                : `<div class="popup-header-img bg-light d-flex align-items-center justify-content-center"><i class="bi bi-image text-muted" style="font-size:2rem"></i></div>`;
            return `
                <div class="d-flex flex-column" style="width: 250px;">
                    ${imgHTML}
                    <div class="p-3 bg-white">
                        <span class="badge bg-danger mb-2">${spot.category}</span>
                        <h5 class="fw-bold mb-1 fs-6">${spot.name}</h5>
                        <div class="text-muted small mb-2">
                            <i class="bi bi-person-fill me-1"></i>${spot.user} · ${spot.created}
                        </div>
                        <button class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold">Lihat Detail</button>
                    </div>
                </div>
            `;
        }

        // 3. Load saved spots from API (with filter params)
        function loadSpots() {
            // Clear existing markers
            allMarkers.forEach(m => map.removeLayer(m));
            allMarkers = [];

            const params = new URLSearchParams();
            params.set('sort_by', currentFilters.sort_by);
            if (currentFilters.category && currentFilters.category !== 'Semua') params.set('category', currentFilters.category);
            if (currentFilters.search) params.set('search', currentFilters.search);
            if (userPosition) { params.set('user_lat', userPosition.lat); params.set('user_lng', userPosition.lng); }

            fetch('/food-spots?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            })
            .then(res => res.json())
            .then(spots => {
                // Client-side min rating filter
                const minRating = currentFilters.min_rating || 0;
                const filtered = minRating > 0 ? spots.filter(s => s.rating >= minRating) : spots;

                filtered.forEach(spot => {
                    const iconClass = categoryIcons[spot.category] || 'bi-shop';
                    const marker = L.marker([spot.lat, spot.lng], { icon: createCustomIcon(iconClass) }).addTo(map);
                    marker.bindPopup(createSpotPopup(spot));
                    allMarkers.push(marker);
                });

                // Update count badge
                const badge = document.getElementById('spot-count-badge');
                badge.textContent = filtered.length + ' spot ditemukan';
                badge.classList.remove('d-none');
            })
            .catch(err => console.error('Failed to load spots:', err));
        }
        loadSpots();

        // 4. Interactive "Add Spot" Flow
        let isAddMode = false;
        let newMarker = null;
        const addModeAlert = document.getElementById('add-mode-alert');
        const offcanvasEl = document.getElementById('addSpotOffcanvas');
        const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
        const photoInput = document.getElementById('photo-input');
        const photoPreview = document.getElementById('photo-preview');
        const cameraIcon = document.getElementById('camera-icon');
        const photoLabel = document.getElementById('photo-label');
        const uploadBtn = document.getElementById('upload-photo-btn');

        // Photo upload: click the circle to trigger file input
        uploadBtn.addEventListener('click', function() {
            photoInput.click();
        });

        // Photo selected: show preview
        photoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                // Validate size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran foto maksimal 5MB!');
                    this.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    photoPreview.classList.remove('d-none');
                    cameraIcon.classList.add('d-none');
                    uploadBtn.classList.remove('border-danger', 'text-danger');
                    uploadBtn.classList.add('border-success');
                    photoLabel.textContent = file.name.substring(0, 20) + (file.name.length > 20 ? '...' : '');
                    photoLabel.classList.remove('text-muted');
                    photoLabel.classList.add('text-success');
                };
                reader.readAsDataURL(file);
            }
        });

        // Reset photo upload UI
        function resetPhotoUI() {
            photoInput.value = '';
            photoPreview.src = '';
            photoPreview.classList.add('d-none');
            cameraIcon.classList.remove('d-none');
            uploadBtn.classList.add('border-danger', 'text-danger');
            uploadBtn.classList.remove('border-success');
            photoLabel.textContent = 'Tambahkan Foto';
            photoLabel.classList.add('text-muted');
            photoLabel.classList.remove('text-success');
        }

        // Click FAB to activate Add Mode
        document.getElementById('fab-add-spot').addEventListener('click', function() {
            if(isAddMode) return;
            isAddMode = true;
            addModeAlert.classList.add('show');
            document.getElementById('map').style.cursor = 'crosshair';
            map.closePopup();
        });

        // Cancel Add Mode
        document.getElementById('cancel-add-mode').addEventListener('click', function(e) {
            e.stopPropagation();
            cancelAddMode();
        });

        // Map Click Event (Placing the pin)
        map.on('click', function(e) {
            if(!isAddMode) return;
            
            // Set hidden lat/lng inputs
            document.getElementById('spot-lat').value = e.latlng.lat;
            document.getElementById('spot-lng').value = e.latlng.lng;
            
            if(newMarker) {
                newMarker.setLatLng(e.latlng);
            } else {
                newMarker = L.marker(e.latlng, {
                    icon: createCustomIcon('bi-pin-angle-fill'),
                    draggable: true
                }).addTo(map);
                
                // Update hidden inputs when marker is dragged
                newMarker.on('dragend', function(ev) {
                    const pos = ev.target.getLatLng();
                    document.getElementById('spot-lat').value = pos.lat;
                    document.getElementById('spot-lng').value = pos.lng;
                });
            }
            
            map.flyTo(e.latlng, 16, { duration: 0.6 });
            
            setTimeout(() => {
                offcanvas.show();
            }, 700);
        });
        
        // Handle Form Submission (Real AJAX upload)
        document.getElementById('add-spot-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const spotName = document.getElementById('spot-name').value;
            const selectedKat = document.querySelector('input[name="kategori"]:checked');
            const kategoriLabel = selectedKat ? document.querySelector(`label[for="${selectedKat.id}"]`).textContent.trim() : 'Berat';
            // Extract category text without emoji (remove emoji and leading/trailing spaces)
            const category = kategoriLabel.replace(/^[\p{Emoji}\s]+|[\p{Emoji}\s]+$/gu, '').replace(/[^\w\s]/gu, '').trim() || 'Berat';
            
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
            btn.disabled = true;
            
            // Build FormData for multipart upload
            const formData = new FormData();
            formData.append('name', spotName);
            formData.append('category', category);
            formData.append('latitude', document.getElementById('spot-lat').value);
            formData.append('longitude', document.getElementById('spot-lng').value);
            
            if (photoInput.files[0]) {
                formData.append('photo', photoInput.files[0]);
            }

            fetch('/food-spots', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) return res.json().then(err => { throw err; });
                return res.json();
            })
            .then(data => {
                offcanvas.hide();
                btn.innerHTML = originalText;
                btn.disabled = false;
                this.reset();
                resetPhotoUI();
                
                if(newMarker) {
                    newMarker.dragging.disable();
                    const spot = data.spot;
                    const iconClass = categoryIcons[spot.category] || 'bi-shop';
                    newMarker.setIcon(createCustomIcon(iconClass));
                    newMarker.bindPopup(createSpotPopup(spot)).openPopup();
                    newMarker = null;
                }
                
                cancelAddMode();
            })
            .catch(err => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                console.error('Upload error:', err);
                
                let msg = 'Gagal menyimpan spot. ';
                if (err.errors) {
                    msg += Object.values(err.errors).flat().join(', ');
                } else if (err.message) {
                    msg += err.message;
                }
                alert(msg);
            });
        });

        // Cancel Add Mode when offcanvas is dismissed without saving
        offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
            if (newMarker && isAddMode) {
                map.removeLayer(newMarker);
                newMarker = null;
            }
            cancelAddMode();
            resetPhotoUI();
        });

        // Helper to reset UI state
        function cancelAddMode() {
            isAddMode = false;
            addModeAlert.classList.remove('show');
            document.getElementById('map').style.cursor = '';
            if(newMarker && !offcanvasEl.classList.contains('show')) {
                 map.removeLayer(newMarker);
                 newMarker = null;
            }
        }
    });
</script>
@endsection