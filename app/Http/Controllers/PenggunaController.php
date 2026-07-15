<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pengguna', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->simplePaginate($request->get('per_page', 10));

        return view('pengguna.daftar', compact('users'));
    }

    public function create()
    {
        return view('pengguna.tambah');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pengguna' => 'required|string|max:191',
            'username' => 'required|string|max:191|unique:users,username',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,petugas,pengguna',
            'unit_kerja' => 'nullable|string|max:191',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        $data['password'] = bcrypt($data['password']);

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('profile_photos', $fileName, 'public');
            $data['profile_photo'] = $fileName;
        }

        $user = User::create($data);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Tambah Pengguna',
            'detail' => "Menambahkan pengguna baru: {$user->nama_pengguna} ({$user->role})",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pengguna.ubah', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'nama_pengguna' => 'required|string|max:191',
            'username' => 'required|string|max:191|unique:users,username,' . $user->id,
            'email' => 'required|email|max:191|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,petugas,pengguna',
            'unit_kerja' => 'nullable|string|max:191',
            'profile_photo' => 'nullable|image|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }

        $data = $request->validate($rules);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama
            if ($user->profile_photo) {
                \Storage::disk('public')->delete('profile_photos/' . $user->profile_photo);
            }
            
            $file = $request->file('profile_photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('profile_photos', $fileName, 'public');
            $data['profile_photo'] = $fileName;
        }

        $user->update($data);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Ubah Pengguna',
            'detail' => "Mengubah data pengguna: {$user->nama_pengguna}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('pengguna.index')->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        // Hapus foto profil jika ada
        if ($user->profile_photo) {
            \Storage::disk('public')->delete('profile_photos/' . $user->profile_photo);
        }

        $user->delete();

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Hapus Pengguna',
            'detail' => "Menghapus pengguna: {$user->nama_pengguna}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}