@extends('layouts.dashboard')

@section('title', 'Tambah Kabinet')
@section('subtitle', 'Tambahkan kabinet baru dan kaitkan dengan lokasi penyimpanan.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Form Kabinet Baru</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('cabinets.store') }}" method="POST">
                @include('cabinets.form')
                <div class="mt-4">
                    <a href="{{ route('cabinets.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Kabinet</button>
                </div>
            </form>
        </div>
    </div>
@endsection
