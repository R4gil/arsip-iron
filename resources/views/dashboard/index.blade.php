@extends('layouts.dashboard')

@section('content')
<header class="page-header">
    <h2>Dashboard Statistik</h2>
</header>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <section class="card card-featured-left card-featured-primary">
            <div class="card-body">
                <h4 class="title">Total Arsip</h4>
                <div class="h3 font-weight-bold">{{ $totalArsip }}</div>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <section class="card">
            <header class="card-header"><h2 class="card-title">Tren Arsip Masuk {{ date('Y') }}</h2></header>
            <div class="card-body"><div style="height: 300px;"><canvas id="lineChart"></canvas></div></div>
        </section>
    </div>
    <div class="col-lg-4">
        <section class="card">
            <header class="card-header"><h2 class="card-title">Status Ketersediaan</h2></header>
            <div class="card-body"><div style="height: 300px;"><canvas id="donutChart"></canvas></div></div>
        </section>
    </div>
</div>

<div class="row mt-3">
    <div class="col-lg-12">
        <section class="card">
            <header class="card-header"><h2 class="card-title">Distribusi Arsip Per Ruangan</h2></header>
            <div class="card-body"><div style="height: 250px;"><canvas id="barChart"></canvas></div></div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
            datasets: [{ label: 'Arsip', data: [@foreach(range(1, 12) as $m) {{ $arsipPerBulan->where('bulan', $m)->first()->total ?? 0 }}, @endforeach], borderColor: '#0088cc', tension: 0.3, fill: true, backgroundColor: 'rgba(0, 136, 204, 0.1)' }]
        }, options: { maintainAspectRatio: false }
    });

    // Donut Chart
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusData->pluck('status_ketersediaan')) !!},
            datasets: [{ data: {!! json_encode($statusData->pluck('total')) !!}, backgroundColor: ['#0088cc', '#f6c23e', '#e74c3c'] }]
        }, options: { maintainAspectRatio: false }
    });

    // Bar Chart
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($lokasiData->pluck('ruangan')) !!},
            datasets: [{ label: 'Jumlah Arsip', data: {!! json_encode($lokasiData->pluck('total')) !!}, backgroundColor: '#36b9cc' }]
        }, options: { maintainAspectRatio: false }
    });
</script>
@endpush