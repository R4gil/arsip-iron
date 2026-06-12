@extends('layouts.dashboard')

@section('title', 'Catat Peminjaman')
@section('subtitle', 'Tambahkan transaksi peminjaman arsip ke sistem log peminjaman.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Form Peminjaman Baru</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('borrowings.store') }}" method="POST">
                @include('borrowings.form')
                <div class="mt-4">
                    <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
                </div>
            </form>
        </div>
    </div>
@endsection
