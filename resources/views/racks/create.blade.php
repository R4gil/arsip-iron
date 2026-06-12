@extends('layouts.dashboard')

@section('title', 'Tambah Rak')
@section('subtitle', 'Tambahkan rak arsip baru dan kaitkan dengan kabinet.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Form Rak Baru</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('racks.store') }}" method="POST">
                @include('racks.form')
                <div class="mt-4">
                    <a href="{{ route('racks.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Rak</button>
                </div>
            </form>
        </div>
    </div>
@endsection
