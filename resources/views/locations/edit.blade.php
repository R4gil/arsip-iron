@extends('layouts.dashboard')

@section('title', 'Ubah Lokasi')
@section('subtitle', 'Perbarui informasi lokasi penyimpanan arsip.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Form Edit Lokasi</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('locations.update', $location) }}" method="POST">
                @method('PUT')
                @include('locations.form')
                <div class="mt-4">
                    <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Perbarui Lokasi</button>
                </div>
            </form>
        </div>
    </div>
@endsection
