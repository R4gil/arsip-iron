@extends('layouts.dashboard')

@section('title', 'Tambah Lokasi')
@section('subtitle', 'Tambahkan lokasi baru untuk kabinet dan rak arsip.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Form Lokasi Baru</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('locations.store') }}" method="POST">
                @include('locations.form')
                <div class="mt-4">
                    <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Lokasi</button>
                </div>
            </form>
        </div>
    </div>
@endsection
