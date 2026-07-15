@extends('layouts.dashboard')
@section('title','Lemari Arsip')
@section('content')
@include('partials.page-header',['title'=>'Lemari Arsip','subtitle'=>'Kelola lemari berdasarkan lokasi ruangan.','action'=>route('lemari.create'),'actionLabel'=>'Tambah Lemari'])
@include('partials.lokasi-hierarchy')
<div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold" style="color:#334155;font-size:0.85rem;">Filter Lokasi</label>
                <select name="lokasi_id" class="form-select form-select-sm" style="border-radius:8px;border:1.5px solid #e2e8f0;font-size:0.85rem;background-color:#f8fafc;">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('lokasi_id')==$loc->id?'selected':'' }}>{{ $loc->ruangan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark btn-sm w-100 py-2" style="border-radius:8px;font-weight:600;">Terapkan</button>
            </div>
        </form>
    </div>
</div>
<div class="card border-0 shadow-sm" style="border-radius:12px;">
    <div class="card-body px-0 py-0">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="background:#f8fafc;border-radius:12px 12px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <span style="color:#64748b;font-size:0.8rem;">Tampilkan</span>
                <select class="form-select form-select-sm" style="width:auto;min-width:70px;border-radius:6px;border:1.5px solid #e2e8f0;font-size:0.8rem;background-color:#fff;" onchange="window.location.href='{{ route('lemari.index') }}?per_page='+this.value+'&lokasi_id={{ request('lokasi_id') }}'">
                    <option value="10" {{ request('per_page',15)==10?'selected':'' }}>10</option>
                    <option value="25" {{ request('per_page',15)==25?'selected':'' }}>25</option>
                    <option value="50" {{ request('per_page',15)==50?'selected':'' }}>50</option>
                    <option value="100" {{ request('per_page',15)==100?'selected':'' }}>100</option>
                </select>
                <span style="color:#64748b;font-size:0.8rem;">per halaman</span>
            </div>
            <span style="color:#64748b;font-size:0.8rem;">Data lemari</span>
        </div>
        <div class="table-responsive">
            <table class="table is-table mb-0">
                <thead><tr><th class="ps-3" style="width:50px;">No</th><th>Lokasi</th><th>Nama Lemari</th><th>Jml Rak</th><th class="text-center" style="width:200px;">Aksi</th></tr></thead>
                <tbody>
                    @forelse($cabinets as $cabinet)
                    <tr>
                        <td class="ps-3 text-muted">{{ $cabinets->firstItem()+$loop->index }}</td>
                        <td>{{ $cabinet->location?->ruangan??'—' }}</td>
                        <td class="fw-bold">{{ $cabinet->lemari_nama }}</td>
                        <td><a href="{{ route('rak.index',['lemari_id'=>$cabinet->lemari_id,'lokasi_id'=>$cabinet->ruangarsip_id]) }}" class="text-decoration-none fw-semibold">{{ $cabinet->racks_count }} rak</a></td>
                        <td class="text-center" style="white-space:nowrap;">
                            <a href="{{ route('rak.create',['lokasi_id'=>$cabinet->ruangarsip_id,'lemari_id'=>$cabinet->lemari_id]) }}" class="btn btn-sm me-1" style="background:linear-gradient(135deg,#d4af37,#aa7c11);color:#1d2127;border:none;font-weight:700;border-radius:8px;box-shadow:0 2px 6px rgba(212,175,55,0.25);">+ Rak</a>
                            <a href="{{ route('lemari.edit', $cabinet) }}" class="btn btn-sm me-1" style="background:#fffbeb;color:#b45309;border:1.5px solid #fcd34d;font-weight:600;border-radius:8px;">Edit</a>
                            <form action="{{ route('lemari.destroy', $cabinet) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus lemari ini?');">@csrf @method('DELETE')<button class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border:1.5px solid #fecaca;font-weight:600;border-radius:8px;">Hapus</button></form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="is-empty">Belum ada lemari.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($cabinets->hasPages())
    <div class="card-body border-top d-flex justify-content-between align-items-center" style="background:#f8fafc;border-radius:0 0 12px 12px;">
        <span style="color:#64748b;font-size:0.8rem;">&nbsp;</span>
        {{ $cabinets->withQueryString()->links('pagination::simple-bootstrap-4') }}
    </div>
    @endif
</div>
<style>
.form-control:focus,.form-select:focus{border-color:#d4af37!important;box-shadow:0 0 0 3px rgba(212,175,55,0.15)!important;background-color:#fff!important;}
.form-control:hover,.form-select:hover{border-color:#cbd5e1!important;}
.card{transition:box-shadow 0.3s ease;}
.card:hover{box-shadow:0 4px 20px rgba(0,0,0,0.08)!important;}
.table th{background:#f1f5f9!important;color:#334155!important;font-size:0.78rem!important;text-transform:uppercase;letter-spacing:0.03em;border-bottom:2px solid #e2e8f0!important;padding:0.7rem 0.75rem!important;}
.table td{vertical-align:middle!important;font-size:0.85rem;padding:0.65rem 0.75rem!important;}
.table tbody tr:hover{background:#f8fafc!important;}
.table tbody tr:not(:last-child) td{border-bottom:1px solid #f1f5f9!important;}
.pagination{margin-bottom:0!important;gap:8px!important;}
.pagination .page-link{background:#fff!important;border:1.5px solid #e2e8f0!important;color:#334155!important;font-weight:600!important;font-size:0.85rem!important;padding:0.5rem 0.85rem!important;border-radius:8px!important;transition:all 0.2s ease!important;text-decoration:none!important;}
.pagination .page-item:first-child .page-link,.pagination .page-item:last-child .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#d4af37!important;color:#1d2127!important;font-weight:700!important;padding:0.5rem 1.2rem!important;box-shadow:0 2px 8px rgba(212,175,55,0.25)!important;}
.pagination .page-item:first-child .page-link:hover,.pagination .page-item:last-child .page-link:hover{background:linear-gradient(135deg,#f3e5ab,#d4af37)!important;border-color:#aa7c11!important;transform:translateY(-1px)!important;box-shadow:0 4px 12px rgba(212,175,55,0.4)!important;}
.pagination .page-item.active .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#aa7c11!important;color:#1d2127!important;font-weight:700!important;box-shadow:0 2px 8px rgba(212,175,55,0.3)!important;}
.pagination .page-item.disabled .page-link{background:#f8fafc!important;border-color:#e2e8f0!important;color:#94a3b8!important;cursor:not-allowed!important;opacity:0.5!important;}
.pagination .page-link .sr-only{display:none!important;}
</style>
@endsection