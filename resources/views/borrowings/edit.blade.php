@extends('layouts.dashboard')

@section('title', 'Perbarui Peminjaman')
@section('subtitle', 'Ubah tanggal, arsip, atau status peminjaman sesuai kondisi terbaru.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Edit Transaksi Peminjaman</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('borrowings.update', $borrowing) }}" method="POST">
                @method('PUT')
                @include('borrowings.form')
                <div class="mt-4">
                    <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Perbarui Peminjaman</button>
                </div>
            </form>
        </div>
    </div>
@endsection
