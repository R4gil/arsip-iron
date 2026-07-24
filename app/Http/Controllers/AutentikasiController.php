<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutentikasiController extends Controller
{
    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => 'Username atau password tidak valid.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function profile()
    {
        $user = Auth::user();
        $activities = \App\Models\AktivitasLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        return view('auth.profile', compact('user', 'activities'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'profile_photo.required' => 'Silakan pilih file foto terlebih dahulu.',
            'profile_photo.image' => 'File harus berupa gambar.',
            'profile_photo.mimes' => 'Format file harus: JPEG, PNG, JPG, atau GIF.',
            'profile_photo.max' => 'Ukuran file tidak boleh melebihi 2MB.',
        ]);

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('profile_photos', $filename, 'nas_storage');
            $user->profile_photo = $filename;
            $user->save();

            // Log activity only if photo was uploaded
            \App\Models\AktivitasLog::create([
                'user_id' => $user->id,
                'aktivitas' => 'Update Profil',
                'detail' => 'Mengupdate foto profil',
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('profile')->with('success', 'Foto profil berhasil diperbarui!');
        }

        return redirect()->route('profile')->with('info', 'Tidak ada perubahan foto profil.');
    }

    public function viewProfilePhoto($filename)
    {
        $path = 'profile_photos/' . $filename;

        // Try public storage first (where new uploads are stored)
        if (\Storage::disk('public')->exists($path)) {
            $file = \Storage::disk('public')->get($path);
            $mimeType = \Storage::disk('public')->mimeType($path);
            return response($file, 200)->header('Content-Type', $mimeType);
        }

        // Try NAS storage second
        if (\Storage::disk('nas_storage')->exists($path)) {
            $file = \Storage::disk('nas_storage')->get($path);
            $mimeType = \Storage::disk('nas_storage')->mimeType($path);
            return response($file, 200)->header('Content-Type', $mimeType);
        }

        // Try local storage (for old photos in app/private/public/profile_photos)
        $localPath = storage_path('app/private/public/profile_photos/' . $filename);
        if (file_exists($localPath)) {
            $file = file_get_contents($localPath);
            $mimeType = mime_content_type($localPath);
            return response($file, 200)->header('Content-Type', $mimeType);
        }

        \Log::error('Profile photo not found in any storage: ' . $path);
        abort(404);
    }
}
