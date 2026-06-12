@extends('layouts.dashboard')

@section('title', 'Dashboard Arsip')
@section('subtitle', 'Ringkasan statistik arsip, aktivitas, dan peminjaman terbaru.')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-sm-6">
            <div class="card card-soft shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="text-muted">Total Arsip</span>
                        </div>
                        <div class="badge badge-soft-primary">Statistik</div>
                    </div>
                    <h2 class="h3 mb-0">{{ $totalArchives }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-soft shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div><span class="text-muted">Arsip Aktif</span></div>
                        <i class="fas fa-check-circle text-primary"></i>
                    </div>
                    <h2 class="h3 mb-0">{{ $activeArchives }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-soft shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div><span class="text-muted">Arsip Inaktif</span></div>
                        <i class="fas fa-archive text-secondary"></i>
                    </div>
                    <h2 class="h3 mb-0">{{ $inactiveArchives }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-soft shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div><span class="text-muted">Arsip Dipinjam</span></div>
                        <i class="fas fa-hand-holding-box text-warning"></i>
                    </div>
                    <h2 class="h3 mb-0">{{ $borrowedArchives }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card card-soft shadow-sm">
                <div class="card-header card-header-soft d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Grafik Arsip per Lokasi</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartLocations" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card card-soft shadow-sm h-100">
                <div class="card-header card-header-soft d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Widget Arsip Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($recentArchives->isEmpty())
                        <div class="text-center text-muted py-4">Belum ada arsip terbaru.</div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($recentArchives as $archive)
                                <div class="list-group-item px-0 py-3 border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $archive->nama_arsip }}</h6>
                                            <small class="text-muted">{{ $archive->nomor_arsip }}</small>
                                        </div>
                                        <span class="badge bg-{{ $archive->status === 'tersedia' ? 'success' : ($archive->status === 'dipinjam' ? 'warning text-dark' : 'secondary') }}">{{ ucfirst($archive->status) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4">
            <div class="card card-soft shadow-sm h-100">
                <div class="card-header card-header-soft d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Arsip per Tahun</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartYear" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card card-soft shadow-sm h-100">
                <div class="card-header card-header-soft d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Peminjaman Bulanan</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartBorrowings" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card card-soft shadow-sm h-100">
                <div class="card-header card-header-soft d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Aktivitas Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($recentActivities->isEmpty())
                        <div class="text-center text-muted py-4">Tidak ada aktivitas terbaru.</div>
                    @else
                        <div class="timeline">
                            @foreach($recentActivities as $activity)
                                <div class="timeline-item mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $activity->aktivitas }}</strong>
                                            <div class="small text-muted">{{ $activity->user?->name ?? 'Sistem' }} · {{ $activity->created_at->diffForHumans() }}</div>
                                        </div>
                                        <span class="badge bg-secondary">{{ $activity->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const chartLocations = document.getElementById('chartLocations');
    if (chartLocations) {
        new Chart(chartLocations, {
            type: 'bar',
            data: {
                labels: @json($archivesPerLocation->pluck('nama_lokasi')),
                datasets: [{
                    label: 'Jumlah Arsip',
                    data: @json($archivesPerLocation->pluck('total')),
                    backgroundColor: 'rgba(37, 99, 235, 0.8)',
                    borderColor: 'rgba(30, 58, 138, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    const chartYear = document.getElementById('chartYear');
    if (chartYear) {
        new Chart(chartYear, {
            type: 'line',
            data: {
                labels: @json($archivesPerYear->pluck('tahun')),
                datasets: [{
                    label: 'Arsip',
                    data: @json($archivesPerYear->pluck('total')),
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    const chartBorrowings = document.getElementById('chartBorrowings');
    if (chartBorrowings) {
        new Chart(chartBorrowings, {
            type: 'bar',
            data: {
                labels: @json($borrowingsMonthly->pluck('month')->map(fn($m) => 
                    DateTime.fromISO(`${new Date().getFullYear()}-${String($m).padStart(2, '0')}-01`).toLocaleString({ month: 'short' })) ),
                datasets: [{
                    label: 'Peminjaman',
                    data: @json($borrowingsMonthly->pluck('total')),
                    backgroundColor: 'rgba(37, 99, 235, 0.7)',
                    borderColor: 'rgba(30, 58, 138, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
</script>
@endpush
