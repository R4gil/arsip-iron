@extends('layouts.dashboard')

@section('title', 'Ubah Kabinet')
@section('subtitle', 'Perbarui konfigurasi kabinet untuk manajemen rak arsip.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Form Edit Kabinet</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('cabinets.update', $cabinet) }}" method="POST">
                @method('PUT')
                @include('cabinets.form')
                <div class="mt-4">
                    <a href="{{ route('cabinets.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Perbarui Kabinet</button>
                </div>
            </form>
        </div>
    </div>
@endsection
