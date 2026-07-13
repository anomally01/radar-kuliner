@extends('layouts.app')

@section('content')
<!-- Leaflet CSS & Bootstrap Icons -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    /* Full screen layout */
    main.py-4 { padding-top: 0 !important; padding-bottom: 0 !important; }
    
    #map-container { position: relative; height: calc(100vh - 56px); width: 100%; overflow: hidden; }
    #map { position: absolute; top: 0; bottom: 0; width: 100%; z-index: 1; }
    
    /* UI Layer for overlays */
    .ui-layer { position: absolute; z-index: 400; pointer-events: none; width: 100%; height: 100%; top: 0; left: 0; }
    .interactive-element { pointer-events: auto; }
    
    /* Glassmorphism components */
    .glass-search {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .glass-search:focus-within {
        background: rgba(255, 255, 255, 0.95);
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }
    
    /* FAB */
    .fab-add { 
        position: absolute; 
        bottom: 40px; 
        right: 30px; 
        border-radius: 50%; 
        width: 65px; 
        height: 65px; 
        box-shadow: 0 10px 25px rgba(220, 53, 69, 0.4); 
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 400;
    }
    
    .fab-add:hover {
        transform: scale(1.1) rotate(90deg);
        box-shadow: 0 15px 35px rgba(220, 53, 69, 0.5); 
    }

    /* Custom Leaflet Popups */
    .leaflet-popup-content-wrapper {
        border-radius: 16px;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.5);
    }
    .leaflet-popup-content {
        margin: 0;
        width: 250px !important;
    }
    .leaflet-popup-tip-container {
        display: none; /* Hide the little triangle */
    }
    
    .popup-header-img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }
    
    /* Marker Custom Icon */
    .custom-marker {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        font-size: 20px;
        transition: transform 0.2s;
        width: 40px;
        height: 40px;
    }
    .custom-marker:hover {
        transform: scale(1.2) translateY(-5px);
        z-index: 1000 !important;
    }

    /* Add Mode Alert */
    #add-mode-alert {
        position: absolute;
        top: -100px;
        left: 50%;
        transform: translateX(-50%);
        transition: top 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 400;
        border-radius: 30px;
    }
    #add-mode-alert.show {
        top: 20px;
    }
</style>

<div id="map-container">
    <div id="map"></div>

    <div class="ui-layer d-flex flex-column p-3">
        <!-- Top Search Bar -->
        <div class="interactive-element mt-2 container max-w-md mx-auto" style="max-width: 500px; margin-top: 70px !important;">
            <div class="input-group glass-search rounded-pill overflow-hidden p-1">
                <span class="input-group-text bg-transparent border-0 ps-4"><i class="bi bi-search text-danger fs-5"></i></span>
                <input type="text" class="form-control bg-transparent border-0 py-2 shadow-none fw-bold" placeholder="Cari hidden gem terdekat...">
                <button class="btn btn-danger rounded-pill px-4 m-1 fw-bold">Cari</button>
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

<!-- Offcanvas Form -->
<div class="offcanvas offcanvas-bottom h-auto rounded-top-4 shadow-lg" tabindex="-1" id="addSpotOffcanvas" style="max-height: 85vh;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title fw-bold text-danger"><i class="bi bi-pin-map-fill me-2"></i> Tambah Spot Kuliner</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="add-spot-form">
            <div class="mb-4 text-center mt-2">
                <div class="d-inline-flex justify-content-center align-items-center bg-light rounded-circle border border-2 border-dashed border-danger text-danger" style="cursor:pointer; width: 80px; height: 80px;" id="upload-photo-btn">
                    <i class="bi bi-camera fs-2"></i>
                </div>
                <div class="small text-muted fw-bold mt-2">Tambahkan Foto</div>
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-bold">Nama Tempat</label>
                <input type="text" id="spot-name" class="form-control form-control-lg bg-light border-0 shadow-none" placeholder="Contoh: Nasi Goreng Gila Mas Bro" required>
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

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Initialize Map
        const map = L.map('map', {
            zoomControl: false // Custom control position
        }).setView([-6.175110, 106.827153], 13); // Default Jakarta

        // Add Tile Layer (CARTO Voyager - Modern & Clean)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Add Zoom Control to bottom left (so it doesn't overlap with FAB)
        L.control.zoom({ position: 'bottomleft' }).addTo(map);

        // Geolocation: Find user's actual location
        map.locate({setView: true, maxZoom: 15, enableHighAccuracy: true});
        
        map.on('locationfound', function(e) {
            L.circle(e.latlng, e.accuracy / 2, {
                color: '#0d6efd',
                fillColor: '#0d6efd',
                fillOpacity: 0.1,
                weight: 1
            }).addTo(map);
            
            L.circleMarker(e.latlng, {
                color: 'white',
                fillColor: '#0d6efd',
                fillOpacity: 1,
                weight: 3,
                radius: 8
            }).addTo(map).bindPopup("<div class='fw-bold text-primary p-1'>📍 Lokasi Anda Saat Ini</div>").openPopup();
        });

        // 2. Dummy Data & Markers
        const dummySpots = [
            { lat: -6.175110, lng: 106.827153, name: "Sate Ayam Madura Cak Udin", cat: "Makanan Berat", rating: 4.8, img: "https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=400&q=80", icon: "bi-shop" },
            { lat: -6.178, lng: 106.823, name: "Kopi Kenangan Senja", cat: "Minuman", rating: 4.5, img: "https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=400&q=80", icon: "bi-cup-hot-fill" },
            { lat: -6.172, lng: 106.832, name: "Gorengan Maknyus", cat: "Cemilan", rating: 4.2, img: "https://images.unsplash.com/photo-1626804475297-41609ea264eb?auto=format&fit=crop&w=400&q=80", icon: "bi-basket2-fill" }
        ];

        // Custom Icon Generator
        const createCustomIcon = (iconClass) => {
            return L.divIcon({
                className: 'custom-icon-wrapper',
                html: `<div class="custom-marker"><i class="bi ${iconClass}"></i></div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                popupAnchor: [0, -45]
            });
        };

        // Render Dummy Markers
        dummySpots.forEach(spot => {
            const marker = L.marker([spot.lat, spot.lng], { icon: createCustomIcon(spot.icon) }).addTo(map);
            
            const popupHTML = `
                <div class="d-flex flex-column" style="width: 250px;">
                    <img src="${spot.img}" class="popup-header-img" alt="${spot.name}">
                    <div class="p-3 bg-white">
                        <span class="badge bg-danger mb-2">${spot.cat}</span>
                        <h5 class="fw-bold mb-1 fs-6">${spot.name}</h5>
                        <div class="text-warning mb-3 small fw-bold">
                            <i class="bi bi-star-fill"></i> ${spot.rating} <span class="text-muted fw-normal">(120 ulasan)</span>
                        </div>
                        <button class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold">Lihat Detail</button>
                    </div>
                </div>
            `;
            marker.bindPopup(popupHTML);
        });

        // 3. Interactive "Add Spot" Flow
        let isAddMode = false;
        let newMarker = null;
        const addModeAlert = document.getElementById('add-mode-alert');
        const offcanvasEl = document.getElementById('addSpotOffcanvas');
        const offcanvas = new bootstrap.Offcanvas(offcanvasEl);

        // Click FAB to activate Add Mode
        document.getElementById('fab-add-spot').addEventListener('click', function() {
            if(isAddMode) return;
            isAddMode = true;
            addModeAlert.classList.add('show');
            document.getElementById('map').style.cursor = 'crosshair';
            
            // Close any open popups to declutter
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
            
            // Place or move temporary marker
            if(newMarker) {
                newMarker.setLatLng(e.latlng);
            } else {
                newMarker = L.marker(e.latlng, {
                    icon: createCustomIcon('bi-pin-angle-fill'),
                    draggable: true // Allow user to drag it for precision
                }).addTo(map);
                
                // Add bounce animation class dynamically if needed
                newMarker.getElement().classList.add('animate__animated', 'animate__bounceInDown');
            }
            
            // Fly to location smoothly
            map.flyTo(e.latlng, 16, { duration: 0.6 });
            
            // Open Offcanvas form to fill details
            setTimeout(() => {
                offcanvas.show();
            }, 700);
        });
        
        // Handle Form Submission
        document.getElementById('add-spot-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const spotName = document.getElementById('spot-name').value;
            
            // Simulate saving animation
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
            
            setTimeout(() => {
                // Success State
                offcanvas.hide();
                btn.innerHTML = originalText;
                this.reset();
                
                // Make marker permanent and show success popup
                if(newMarker) {
                    newMarker.dragging.disable();
                    const successPopup = `
                        <div class="p-3 text-center">
                            <div class="text-success mb-2"><i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i></div>
                            <h6 class="fw-bold mb-0">${spotName}</h6>
                            <p class="text-muted small mt-1">Berhasil ditambahkan ke radar!</p>
                        </div>
                    `;
                    newMarker.bindPopup(successPopup).openPopup();
                    
                    // Change icon to default shop icon
                    newMarker.setIcon(createCustomIcon('bi-shop'));
                    newMarker = null; // reset reference
                }
                
                cancelAddMode();
                
            }, 1200);
        });

        // Cancel Add Mode when offcanvas is dismissed without saving
        offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
            if (newMarker && isAddMode) {
                map.removeLayer(newMarker);
                newMarker = null;
            }
            cancelAddMode();
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
        
        // Dummy photo upload interaction
        document.getElementById('upload-photo-btn').addEventListener('click', function() {
            this.classList.remove('text-danger', 'border-danger');
            this.classList.add('text-success', 'border-success', 'bg-success', 'bg-opacity-10');
            this.innerHTML = '<i class="bi bi-check-lg fs-2"></i>';
        });
    });
</script>
@endsection