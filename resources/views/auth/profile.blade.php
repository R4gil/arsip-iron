@extends('layouts.dashboard')

@section('title', 'Profil Saya')

@section('content')
@include('partials.page-header', ['title' => 'Profil Saya', 'subtitle' => 'Kelola informasi profil dan akun Anda.'])

@if ($errors->any())
    <div class="alert alert-danger p-3 mb-4" style="border-radius: 8px; border-left: 4px solid #dc2626;">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success" style="border-radius: 8px; border-left: 4px solid #16a34a;">{{ session('success') }}</div>
@endif

<div class="row g-3">
    <!-- ===== KIRI: FOTO PROFIL & UPLOAD ===== -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    @if($user->profile_photo)
                        <img src="{{ route('profile.photo', $user->profile_photo) }}" alt="Profile Photo" class="rounded-circle"
                            style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #d4af37; box-shadow: 0 3px 15px rgba(212,175,55,0.3);"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <i class="fas fa-user-circle text-muted" style="font-size: 100px; display: none;"></i>
                    @else
                        <i class="fas fa-user-circle text-muted" style="font-size: 100px; color: #cbd5e1 !important;"></i>
                    @endif
                </div>
                <h5 class="fw-bold mb-1" style="color: #1e293b;">{{ $user->nama_pengguna ?? 'Pengguna' }}</h5>
                <p class="text-muted mb-2" style="font-size: 0.85rem;">{{ $user->username ?? '-' }}</p>
                <span class="badge px-3 py-2" style="background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1e293b; font-weight: 600; border-radius: 6px;">{{ $user->role ?? 'User' }}</span>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="color: #1e293b; font-size: 0.85rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 8px;">
                    <i class="fas fa-camera me-2" style="color: #d4af37;"></i>Upload Foto Profil
                </h6>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="profile_photo" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif" required
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.5rem; font-size: 0.9rem; background-color: #f8fafc;">
                        <div class="form-text mt-2" style="color: #64748b; font-size: 0.8rem;">Format: JPEG, PNG, JPG, GIF (Max 2MB)</div>
                    </div>
                    <button type="submit" class="btn fw-bold w-100 py-2" style="border-radius: 8px; background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1e293b; border: none; box-shadow: 0 3px 10px rgba(212, 175, 55, 0.3);">
                        <i class="fas fa-upload me-2"></i>Upload Foto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ===== KANAN: DETAIL PROFIL ===== -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
                    <i class="fas fa-id-card me-2" style="color: #d4af37;"></i>Detail Profil
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Nama Lengkap</label>
                        <input type="text" class="form-control" value="{{ $user->nama_pengguna ?? '-' }}" readonly
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background: #f1f5f9;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Username</label>
                        <input type="text" class="form-control" value="{{ $user->username ?? '-' }}" readonly
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background: #f1f5f9;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Email</label>
                        <input type="email" class="form-control" value="{{ $user->email ?? '-' }}" readonly
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background: #f1f5f9;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Role</label>
                        <input type="text" class="form-control" value="{{ ucfirst($user->role ?? '-') }}" readonly
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background: #f1f5f9;">
                    </div>
                    @if($user->unit_kerja)
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Unit Kerja</label>
                        <input type="text" class="form-control" value="{{ $user->unit_kerja }}" readonly
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background: #f1f5f9;">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection