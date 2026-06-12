@extends('layouts.dashboard')

@section('title', 'Ubah Klasifikasi')
@section('subtitle', 'Perbarui detail klasifikasi arsip yang ada.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Form Edit Klasifikasi</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('classifications.update', $classification) }}" method="POST">
                @method('PUT')
                @include('classifications.form')
                <div class="mt-4">
                    <a href="{{ route('classifications.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Perbarui Klasifikasi</button>
                </div>
            </form>
        </div>
    </div>
@endsection
