@extends('layouts.dashboard')

@section('title', 'Ubah Arsip')

@section('content')
@include('partials.page-header', ['title' => 'Ubah Arsip', 'subtitle' => 'Edit data arsip yang sudah ada.'])

<div class="is-card">
    <div class="is-card-body is-form">
        <form action="{{ route('arsip.update', $arsip->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('arsip.formulir')
        </form>
    </div>
</div>
@endsection

