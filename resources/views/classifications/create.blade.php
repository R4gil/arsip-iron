@extends('layouts.dashboard')

@section('title', 'Tambah Klasifikasi')
@section('subtitle', 'Tambahkan klasifikasi arsip baru untuk pengelompokan dokumen.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Form Klasifikasi Baru</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('classifications.store') }}" method="POST">
                @include('classifications.form')
                <div class="mt-4">
                    <a href="{{ route('classifications.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Klasifikasi</button>
                </div>
            </form>
        </div>
    </div>
@endsection
