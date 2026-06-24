<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // ... (fungsi index, create, edit tetap sama) ...

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pengguna' => 'required|string|max:191',
            'username'      => 'required|string|max:191|unique:users,username',
            'email'         => 'required|string|email|max:191|unique:users,email',
            'password'      => 'required|string|min:6|confirmed',
            'role'          => 'required|in:admin,petugas,pengguna',
            'unit_kerja'    => 'required|string|max:100', // Tambahkan validasi ini
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nama_pengguna' => 'required|string|max:191',
            'username'      => ['required', 'string', 'max:191', Rule::unique('users', 'username')->ignore($user->id)],
            'email'         => ['required', 'string', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user->id)],
            'password'      => 'nullable|string|min:6|confirmed',
            'role'          => 'required|in:admin,petugas,pengguna',
            'unit_kerja'    => 'required|string|max:100', // Tambahkan validasi ini
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function index(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
    // Mengembalikan view file create.blade.php di folder resources/views/users/
    return view('users.create');
    }

    public function edit($id)
    {
    // Mengambil data user berdasarkan ID
    $user = User::findOrFail($id);
    
    // Kirim data ke view (pastikan file view-nya ada di resources/views/users/edit.blade.php)
    return view('users.edit', compact('user'));
    }
}
