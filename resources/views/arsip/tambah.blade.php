@extends('layouts.dashboard')

@section('title', 'Tambah Arsip Baru')

@section('content')
@include('partials.page-header', ['title' => 'Tambah Arsip Baru', 'subtitle' => 'Isi data arsip dan simpan dengan cepat ke dalam sistem IRON SMART.'])

<div class="is-card">
    <div class="is-card-body is-form">
        <form action="{{ route('arsip.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('arsip.formulir')
        </form>
    </div>
</div>
@endsection
