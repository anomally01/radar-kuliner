@extends('layouts.app')

@section('content')
<style>
    body { font-family: 'Inter', sans-serif; background: #f4f6fb; }

    .point-hero {
        background: linear-gradient(135deg, #FF750F 0%, #F53003 60%, #c40000 100%);
        color: white;
        border-radius: 0 0 30px 30px;
        padding: 40px 0 60px;
        margin-bottom: -30px;
    }
    .balance-card {
        background: white;
        border-radius: 24px;
        border: none;
        box-shadow: 0 20px 60px rgba(245, 48, 3, 0.15);
    }
    .balance-number {
        font-size: 3.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #FF750F, #F53003);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Voucher Cards */
    .voucher-card {
        border: 2px solid transparent;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .voucher-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, #FF750F11, #F5300311);
        opacity: 0;
        transition: opacity 0.25s;
    }
    .voucher-card:hover { border-color: #F53003; transform: translateY(-4px); box-shadow: 0 12px 30px rgba(245,48,3,0.15) !important; }
    .voucher-card:hover::before { opacity: 1; }
    .voucher-card.selected { border-color: #F53003 !important; background: #fff5f5; }

    .voucher-points {
        font-weight: 800;
        font-size: 1.1rem;
        color: #F53003;
    }
    .voucher-icon { font-size: 2.2rem; }

    /* Modal */
    .modal-confirm { border-radius: 24px; overflow: hidden; }
    .modal-confirm .modal-header { background: linear-gradient(135deg, #FF750F, #F53003); color: white; border: none; }

    /* Table */
    .history-table thead { background: linear-gradient(135deg, #1a1a2e, #16213e); color: white; }
    .history-table tbody tr:hover { background: #fff5f5; }
    .history-table td, .history-table th { vertical-align: middle; }

    /* Alert flash */
    .voucher-success-card {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        border: 1px solid #b8dacc;
        border-radius: 16px;
    }
    .code-badge {
        background: #1a1a2e;
        color: #FFD700;
        font-family: 'Courier New', monospace;
        font-size: 1.3rem;
        font-weight: 800;
        letter-spacing: 2px;
        border-radius: 12px;
        padding: 10px 20px;
    }

    /* Earn Log */
    .earn-log-item { border-left: 3px solid #F53003; padding-left: 12px; }
</style>

{{-- ============================================================
     HERO / BALANCE
     ============================================================ --}}
<div class="point-hero">
    <div class="container text-center">
        <h1 class="fw-bold fs-3 mb-1"><i class="bi bi-stars me-2"></i>Poin & Reward Saya</h1>
        <p class="opacity-75 small">Kumpulkan poin, tukar voucher gratis!</p>
    </div>
</div>

<div class="container pb-5" style="margin-top: 30px;">

    {{-- SUCCESS FLASH --}}
    @if(session('success'))
    <div class="voucher-success-card p-4 mb-4 text-center">
        <div style="font-size: 2.5rem;">🎉</div>
        <h5 class="fw-bold text-success mt-2">Redeem Berhasil!</h5>
        <p class="text-muted mb-2">Kamu berhasil menukar poin dengan <strong>{{ session('voucher_name') }}</strong></p>
        <div class="code-badge mx-auto d-inline-block">{{ session('voucher_code') }}</div>
        <p class="text-muted small mt-2 mb-0">Simpan kode voucher di atas sebelum meninggalkan halaman ini.</p>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger rounded-3 d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    {{-- ============================================================
         BALANCE CARD
         ============================================================ --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card balance-card p-4 text-center h-100 d-flex flex-column justify-content-center">
                <div class="mb-1" style="font-size: 2rem;">⭐</div>
                <div class="balance-number">{{ number_format($balance) }}</div>
                <div class="text-muted fw-600">Total Poin Aktif</div>
                <hr class="my-3">
                <div class="d-flex justify-content-around text-center small text-muted">
                    <div>
                        <div class="fw-bold text-success fs-5">+{{ $earnLogs->where('type','earn')->sum('points') }}</div>
                        <div>Poin Didapat</div>
                    </div>
                    <div>
                        <div class="fw-bold text-danger fs-5">-{{ $earnLogs->where('type','redeem')->sum('points') }}</div>
                        <div>Poin Dipakai</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h6 class="fw-bold mb-3"><i class="bi bi-clock-history text-danger me-2"></i>Riwayat Poin Terbaru</h6>
                @forelse($earnLogs->take(5) as $log)
                <div class="earn-log-item mb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-500 small">{{ $log->description }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">{{ $log->created_at->diffForHumans() }}</div>
                    </div>
                    <span class="badge {{ $log->type === 'earn' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3">
                        {{ $log->type === 'earn' ? '+' : '-' }}{{ $log->points }} pt
                    </span>
                </div>
                @empty
                <p class="text-muted small">Belum ada aktivitas poin. Tambah spot kuliner untuk mendapat poin!</p>
                @endforelse
                @if($earnLogs->count() > 5)
                <a href="#history-section" class="btn btn-sm btn-outline-danger rounded-pill mt-2">Lihat semua riwayat ↓</a>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================================================
         VOUCHER SELECTION (JS Block #2: Redeem Logic)
         ============================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <h5 class="fw-bold mb-1"><i class="bi bi-gift text-danger me-2"></i>Pilih Voucher</h5>
        <p class="text-muted small mb-4">Klik voucher yang ingin kamu tukarkan, lalu konfirmasi penukaran.</p>

        {{-- Tabs: Paket Data / Google Play --}}
        <ul class="nav nav-pills mb-4" id="voucherTabs">
            <li class="nav-item">
                <button class="nav-link active" onclick="filterVoucher('data')">📶 Paket Data</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" onclick="filterVoucher('gplay')">🎮 Google Play</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" onclick="filterVoucher('all')">🎁 Semua</button>
            </li>
        </ul>

        <div class="row g-3" id="voucher-grid">
            @foreach($vouchers as $key => $v)
            <div class="col-md-4 col-sm-6 voucher-item" data-type="{{ str_starts_with($key, 'data') ? 'data' : 'gplay' }}">
                <div class="card voucher-card shadow-sm p-3 h-100"
                     onclick="selectVoucher('{{ $key }}', '{{ $v['name'] }}', {{ $v['points'] }}, '{{ $v['icon'] }}')"
                     id="voucher-card-{{ $key }}">
                    <div class="d-flex align-items-start gap-3">
                        <div class="voucher-icon">{{ $v['icon'] }}</div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">{{ $v['name'] }}</div>
                            <div class="voucher-points mt-1">{{ number_format($v['points']) }} Poin</div>
                            @if($balance >= $v['points'])
                                <span class="badge bg-success-subtle text-success rounded-pill small mt-1">✓ Tersedia</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill small mt-1">
                                    Kurang {{ number_format($v['points'] - $balance) }} poin
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ============================================================
         RIWAYAT REDEEM (Tabel HTML)
         ============================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" id="history-section">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-table text-danger me-2"></i>Riwayat Redeem</h5>
            <span class="badge bg-danger rounded-pill">{{ $history->count() }} transaksi</span>
        </div>
        <div class="table-responsive">
            <table class="table history-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th class="py-3">Voucher</th>
                        <th class="py-3 text-center">Kode Voucher</th>
                        <th class="py-3 text-center">Poin Dipakai</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-4 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $i => $h)
                    <tr>
                        <td class="ps-4 text-muted">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-bold">{{ $h->voucher_type }}</div>
                        </td>
                        <td class="text-center">
                            <code class="bg-light px-2 py-1 rounded fw-bold text-danger">{{ $h->voucher_code }}</code>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-danger rounded-pill px-3">-{{ number_format($h->points_used) }} pt</span>
                        </td>
                        <td class="text-center">
                            @if($h->status === 'success')
                                <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Sukses</span>
                            @elseif($h->status === 'pending')
                                <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-clock me-1"></i>Pending</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Gagal</span>
                            @endif
                        </td>
                        <td class="pe-4 text-muted small">{{ $h->redeemed_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <div style="font-size: 2rem;">🎁</div>
                            <div class="mt-2">Belum ada riwayat redeem.</div>
                            <div class="small">Pilih voucher di atas untuk memulai!</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ============================================================
     MODAL KONFIRMASI REDEEM
     ============================================================ --}}
<div class="modal fade" id="redeemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-confirm">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-gift me-2"></i>Konfirmasi Penukaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div id="modal-voucher-icon" style="font-size: 3rem;" class="mb-3">🎁</div>
                <h5 class="fw-bold" id="modal-voucher-name">-</h5>
                <p class="text-muted mb-1">Membutuhkan:</p>
                <div class="fs-3 fw-bold text-danger mb-3" id="modal-points-needed">-</div>
                <div class="d-flex justify-content-between bg-light rounded-3 p-3 mb-3">
                    <span class="text-muted">Saldo saat ini:</span>
                    <span class="fw-bold">{{ number_format($balance) }} poin</span>
                </div>
                <div class="d-flex justify-content-between bg-light rounded-3 p-3">
                    <span class="text-muted">Saldo setelah redeem:</span>
                    <span class="fw-bold text-danger" id="modal-balance-after">-</span>
                </div>
                <p class="text-muted small mt-3 mb-0">Kode voucher akan ditampilkan setelah konfirmasi.</p>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary rounded-pill flex-fill" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('poin.redeem') }}" class="flex-fill">
                    @csrf
                    <input type="hidden" name="voucher_key" id="modal-voucher-key">
                    <button type="submit" id="modal-confirm-btn" class="btn btn-danger rounded-pill w-100 fw-bold">
                        <i class="bi bi-check-circle me-1"></i>Tukar Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     JAVASCRIPT BLOCK #2: Redeem Point Logic
     ============================================================ --}}
<script>
    const userBalance = {{ $balance }};
    let selectedVoucherKey = null;
    const redeemModal = new bootstrap.Modal(document.getElementById('redeemModal'));

    /**
     * JS Block: Filter voucher tabs
     */
    function filterVoucher(type) {
        // Update active tab
        document.querySelectorAll('#voucherTabs .nav-link').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        // Show/hide voucher items
        document.querySelectorAll('.voucher-item').forEach(item => {
            if (type === 'all' || item.dataset.type === type) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    /**
     * JS Block: Select voucher and open confirmation modal
     */
    function selectVoucher(key, name, points, icon) {
        // Remove previous selection highlight
        document.querySelectorAll('.voucher-card').forEach(c => c.classList.remove('selected'));
        const card = document.getElementById('voucher-card-' + key);
        if (card) card.classList.add('selected');

        if (userBalance < points) {
            // Shake animation to indicate insufficient balance
            card.style.animation = 'shake 0.4s ease';
            setTimeout(() => { card.style.animation = ''; }, 400);
            return;
        }

        // Populate modal
        selectedVoucherKey = key;
        document.getElementById('modal-voucher-key').value = key;
        document.getElementById('modal-voucher-name').textContent = name;
        document.getElementById('modal-voucher-icon').textContent = icon;
        document.getElementById('modal-points-needed').textContent = points.toLocaleString('id') + ' Poin';
        document.getElementById('modal-balance-after').textContent = (userBalance - points).toLocaleString('id') + ' poin';

        redeemModal.show();
    }

    // Shake keyframes via JS
    const shakeStyle = document.createElement('style');
    shakeStyle.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }
    `;
    document.head.appendChild(shakeStyle);
</script>
@endsection
