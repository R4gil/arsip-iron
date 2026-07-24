@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
@include('partials.page-header', [
    'title' => 'Dashboard IRON SMART',
    'subtitle' => 'Ringkasan monitoring dan manajemen arsip digital.',
])

@if($arsipMasukRetensi > 0)
<div class="alert border-0 shadow-sm mb-4 d-flex align-items-center" style="background: linear-gradient(135deg, #fff7ed, #ffedd5); border-left: 4px solid #f59e0b !important; border-radius: 12px;">
    <i class="fas fa-exclamation-triangle text-warning me-3 fs-4"></i>
    <div>
        @if(($retensiNotifBaru ?? 0) > 0)
            <strong>⚠️ {{ $retensiNotifBaru }} arsip baru</strong> memasuki masa retensi!
        @endif
        Terdapat <strong>{{ $arsipMasukRetensi }}</strong> arsip yang sudah memasuki masa retensi.
        <a href="{{ route('retensi.index') }}" class="fw-bold ms-1">Lihat daftar retensi →</a>
    </div>
</div>
@endif

@if(($retensiNotifBaru ?? 0) > 0)
<div class="alert border-0 shadow-sm mb-4 d-flex align-items-center" style="background: linear-gradient(135deg, #fef2f2, #fee2e2); border-left: 4px solid #dc2626 !important; border-radius: 12px;">
    <i class="fas fa-bell text-danger me-3 fs-4"></i>
    <div>
        Ada <strong>{{ $retensiNotifBaru }}</strong> notifikasi retensi arsip baru yang belum Anda lihat.
        <a href="{{ route('retensi.index') }}" class="fw-bold ms-1">Klik untuk melihat →</a>
    </div>
</div>
@endif

<div class="row g-3 mb-4">
    @php
        $stats = [
            ['label' => 'Total Arsip', 'value' => $totalArsip, 'icon' => 'fa-archive', 'bg' => '#667eea', 'text' => '#fff'],
            ['label' => 'Masuk Retensi', 'value' => $arsipMasukRetensi, 'icon' => 'fa-hourglass-half', 'bg' => '#f5576c', 'text' => '#fff'],
            ['label' => 'Arsip Permanen', 'value' => $arsipPermanen, 'icon' => 'fa-infinity', 'bg' => '#4facfe', 'text' => '#fff'],
            ['label' => 'Tersedia', 'value' => $tersedia, 'icon' => 'fa-check-circle', 'bg' => '#43e97b', 'text' => '#fff'],
            ['label' => 'Dipinjam', 'value' => $dipinjam, 'icon' => 'fa-book', 'bg' => '#fa709a', 'text' => '#fff'],
            ['label' => 'Aktif / Inaktif', 'value' => $aktif . ' / ' . $inaktif, 'icon' => 'fa-toggle-on', 'bg' => '#30cfd0', 'text' => '#fff'],
        ];
    @endphp
    @foreach($stats as $stat)
    <div class="col-xl-4 col-md-6">
        <div style="background-color: {{ $stat['bg'] }}; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 14px; padding: 1.25rem;">
            <div style="background: rgba(255,255,255,0.2); color: #fff; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 0.75rem;">
                <i class="fas {{ $stat['icon'] }}"></i>
            </div>
            <div style="color: #fff; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">{{ $stat['label'] }}</div>
            <div style="color: #fff; font-weight: 700; font-size: 1.75rem; line-height: 1; margin-top: 0.25rem;">{{ $stat['value'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="is-card is-chart-card h-100">
            <div class="card-header py-3 px-4">Tren Arsip Masuk {{ date('Y') }}</div>
            <div class="card-body"><div style="height: 300px;"><canvas id="lineChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="is-card is-chart-card h-100">
            <div class="card-header py-3 px-4">Status Ketersediaan</div>
            <div class="card-body"><div style="height: 300px;"><canvas id="donutChart"></canvas></div></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="is-card is-chart-card">
            <div class="card-header py-3 px-4">Distribusi Arsip Per Lokasi</div>
            <div class="card-body"><div style="height: 260px;"><canvas id="barChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="is-card h-100">
            <div class="is-card-header"><h6>Jenis Arsip</h6></div>
            <div class="is-card-body">
                <ul class="list-unstyled mb-0">
                    @forelse($jenisData as $item)
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-secondary">{{ $item->label ?? 'Tidak ada data' }}</span>
                            <span class="badge rounded-pill" style="background:#fef3c7;color:#92400e;">{{ $item->total }}</span>
                        </li>
                    @empty
                        <li class="is-empty py-3">Belum ada jenis arsip.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="is-card">
    <div class="is-card-header"><h6>Arsip Terbaru</h6></div>
    <div class="is-card-body p-0">
        <div class="table-responsive">
            <table class="table is-table mb-0">
                <thead><tr><th class="ps-4">Nama Arsip</th><th>Tanggal Ditambahkan</th></tr></thead>
                <tbody>
                    @forelse($arsipTerbaru as $arsip)
                    <tr>
                        <td class="ps-4 fw-semibold">{{ $arsip->nama_arsip ?? 'Tanpa judul' }}</td>
                        <td class="text-muted">{{ $arsip->created_at ? \Carbon\Carbon::parse($arsip->created_at)->format('d M Y') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="is-empty">Belum ada arsip yang ditambahkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card-0 { background-color: #667eea !important; background: #667eea !important; }
    .stat-card-1 { background-color: #f5576c !important; background: #f5576c !important; }
    .stat-card-2 { background-color: #4facfe !important; background: #4facfe !important; }
    .stat-card-3 { background-color: #43e97b !important; background: #43e97b !important; }
    .stat-card-4 { background-color: #fa709a !important; background: #fa709a !important; }
    .stat-card-5 { background-color: #30cfd0 !important; background: #30cfd0 !important; }
    .stat-card-0 .card-body, .stat-card-1 .card-body, .stat-card-2 .card-body,
    .stat-card-3 .card-body, .stat-card-4 .card-body, .stat-card-5 .card-body {
        background-color: transparent !important;
        background: transparent !important;
    }
</style>
@endpush

@push('scripts')
<script>
    const gold = '#d4af37';
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const monthData = [@foreach(range(1, 12) as $m) {{ $arsipPerBulan->where('bulan', $m)->first()->total ?? 0 }}, @endforeach];

    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Arsip',
                data: monthData,
                borderColor: gold,
                backgroundColor: 'rgba(212,175,55,0.12)',
                tension: 0.4, fill: true, pointRadius: 4, pointBackgroundColor: gold
            }]
        },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
    });

    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusData->pluck('label')) !!},
            datasets: [{ data: {!! json_encode($statusData->pluck('total')) !!}, backgroundColor: ['#d4af37', '#f59e0b', '#ef4444'], borderWidth: 0 }]
        },
        options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($lokasiData->pluck('label')) !!},
            datasets: [
                { 
                    label: 'Aktif', 
                    data: {!! json_encode($lokasiData->pluck('aktif')) !!}, 
                    backgroundColor: 'rgba(212,175,55,0.8)', 
                    borderRadius: 8 
                },
                { 
                    label: 'Inaktif', 
                    data: {!! json_encode($lokasiData->pluck('inaktif')) !!}, 
                    backgroundColor: 'rgba(100,116,139,0.8)', 
                    borderRadius: 8 
                }
            ]
        },
        options: { 
            maintainAspectRatio: false, 
            plugins: { legend: { display: true, position: 'bottom' } }, 
            scales: { 
                x: { 
                    stacked: true, 
                    grid: { display: false } 
                }, 
                y: { 
                    stacked: true, 
                    beginAtZero: true, 
                    grid: { color: '#f1f5f9' } 
                } 
            } 
        }
    });
</script>
@endpush
