@extends('layouts.dashboard')

@section('title', 'Ubah Rak')
@section('subtitle', 'Perbarui nama atau kabinet rak arsip.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Form Edit Rak</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('racks.update', $rack) }}" method="POST">
                @method('PUT')
                @include('racks.form')
                <div class="mt-4">
                    <a href="{{ route('racks.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Perbarui Rak</button>
                </div>
            </form>
        </div>
    </div>
@endsection
